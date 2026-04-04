<?php

namespace App\Services;

use App\Models\PaypalSetting;
use App\Models\ProjectOffer;
use App\Models\WeeklySubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PayPalSubscriptionService
{
    /**
     * Create a weekly subscription for a project offer.
     *
     * Flow:
     * 1. Create a PayPal catalog product (or reuse existing)
     * 2. Create a weekly billing plan
     * 3. Create a subscription and redirect the client to approve
     */
    public function createWeeklySubscription(
        ProjectOffer $offer,
        int $userId,
        string $returnUrl,
        string $cancelUrl,
    ): array {
        $setting = $this->requireSetting();
        $weeklyAmount = '1.00';
        $freelancerName = $offer->freelancer_display_name;
        $projectTitle = $offer->project?->title ?: 'Development project';

        // Step 1: Create a PayPal catalog product
        $product = $this->createProduct(
            $setting,
            "Weekly payment – {$freelancerName}",
            "Weekly development payment for {$freelancerName} on {$projectTitle}",
        );

        $productId = $product['id'];

        // Step 2: Create a weekly billing plan
        $plan = $this->createPlan(
            $setting,
            $productId,
            $weeklyAmount,
            $offer->currency ?? 'USD',
            "Weekly payment – {$freelancerName} – \${$weeklyAmount}/week",
        );

        $planId = $plan['id'];

        // Step 3: Create the subscription
        $startTime = $this->nextStartTime();

        $subscription = $this->createSubscriptionRequest(
            $setting,
            $planId,
            $startTime,
            $returnUrl,
            $cancelUrl,
            "HireHelper weekly payment for {$freelancerName}",
        );

        $approveUrl = $this->findLink($subscription, 'approve');

        if (! $approveUrl) {
            throw new RuntimeException('PayPal subscription approval URL was not returned.');
        }

        // Save the subscription record
        $record = WeeklySubscription::create([
            'project_offer_id' => $offer->id,
            'user_id' => $userId,
            'weekly_amount' => $weeklyAmount,
            'currency' => $offer->currency ?? 'USD',
            'status' => 'pending_approval',
            'paypal_product_id' => $productId,
            'paypal_plan_id' => $planId,
            'paypal_subscription_id' => $subscription['id'] ?? null,
            'paypal_subscription_status' => $subscription['status'] ?? null,
            'started_at' => Carbon::parse($startTime),
            'paypal_payload' => $subscription,
        ]);

        return [
            'subscription' => $record,
            'approve_url' => $approveUrl,
            'paypal_response' => $subscription,
        ];
    }

    /**
     * After the client approves, activate and sync the subscription.
     */
    public function handleApproval(WeeklySubscription $subscription): WeeklySubscription
    {
        $setting = $this->requireSetting();

        if (! filled($subscription->paypal_subscription_id)) {
            throw new RuntimeException('No PayPal subscription ID to activate.');
        }

        // Fetch the subscription details from PayPal
        $details = $this->showSubscription($setting, $subscription->paypal_subscription_id);
        $status = strtoupper($details['status'] ?? '');

        $subscription->forceFill([
            'status' => $this->mapStatus($status),
            'paypal_subscription_status' => $status,
            'paypal_payer_email' => data_get($details, 'subscriber.email_address'),
            'next_billing_at' => filled(data_get($details, 'billing_info.next_billing_time'))
                ? Carbon::parse(data_get($details, 'billing_info.next_billing_time'))
                : $subscription->started_at,
            'paypal_payload' => $details,
        ])->save();

        return $subscription;
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(WeeklySubscription $subscription, string $reason = 'Cancelled by client'): WeeklySubscription
    {
        $setting = $this->requireSetting();

        if (filled($subscription->paypal_subscription_id)) {
            Http::baseUrl($setting->baseUrl())
                ->withToken($this->accessToken($setting))
                ->acceptJson()
                ->contentType('application/json')
                ->post("/v1/billing/subscriptions/{$subscription->paypal_subscription_id}/cancel", [
                    'reason' => $reason,
                ]);
        }

        $subscription->forceFill([
            'status' => 'cancelled',
            'paypal_subscription_status' => 'CANCELLED',
            'cancelled_at' => now(),
        ])->save();

        return $subscription;
    }

    /**
     * Sync subscription status from PayPal.
     */
    public function sync(WeeklySubscription $subscription): WeeklySubscription
    {
        $setting = $this->requireSetting();

        if (! filled($subscription->paypal_subscription_id)) {
            return $subscription;
        }

        $details = $this->showSubscription($setting, $subscription->paypal_subscription_id);
        $status = strtoupper($details['status'] ?? '');

        $subscription->forceFill([
            'status' => $this->mapStatus($status),
            'paypal_subscription_status' => $status,
            'paypal_payer_email' => data_get($details, 'subscriber.email_address', $subscription->paypal_payer_email),
            'next_billing_at' => filled(data_get($details, 'billing_info.next_billing_time'))
                ? Carbon::parse(data_get($details, 'billing_info.next_billing_time'))
                : $subscription->next_billing_at,
            'paypal_payload' => $details,
        ])->save();

        return $subscription->fresh();
    }

    // ─── PayPal API calls ───────────────────────────────────────

    protected function createProduct(PaypalSetting $setting, string $name, string $description): array
    {
        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken($setting))
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders(['PayPal-Request-Id' => 'prod-' . Str::uuid()])
            ->post('/v1/catalogs/products', [
                'name' => Str::limit($name, 127, ''),
                'description' => Str::limit($description, 256, ''),
                'type' => 'SERVICE',
                'category' => 'SOFTWARE',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('PayPal create product failed: ' . $response->body());
        }

        return $response->json();
    }

    protected function createPlan(PaypalSetting $setting, string $productId, string $amount, string $currency, string $name): array
    {
        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken($setting))
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders(['PayPal-Request-Id' => 'plan-' . Str::uuid()])
            ->post('/v1/billing/plans', [
                'product_id' => $productId,
                'name' => Str::limit($name, 127, ''),
                'description' => "Weekly payment of \${$amount} {$currency}",
                'billing_cycles' => [
                    [
                        'frequency' => [
                            'interval_unit' => 'WEEK',
                            'interval_count' => 1,
                        ],
                        'tenure_type' => 'REGULAR',
                        'sequence' => 1,
                        'total_cycles' => 0, // infinite
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'value' => $amount,
                                'currency_code' => $currency,
                            ],
                        ],
                    ],
                ],
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'payment_failure_threshold' => 3,
                    'setup_fee_failure_action' => 'CONTINUE',
                    'setup_fee' => [
                        'value' => '0',
                        'currency_code' => $currency,
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('PayPal create plan failed: ' . $response->body());
        }

        return $response->json();
    }

    protected function createSubscriptionRequest(
        PaypalSetting $setting,
        string $planId,
        string $startTime,
        string $returnUrl,
        string $cancelUrl,
        string $description,
    ): array {
        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken($setting))
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders(['PayPal-Request-Id' => 'sub-' . Str::uuid()])
            ->post('/v1/billing/subscriptions', [
                'plan_id' => $planId,
                'start_time' => $startTime,
                'quantity' => '1',
                'application_context' => [
                    'brand_name' => 'HireHelper.ai',
                    'locale' => 'en-US',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('PayPal create subscription failed: ' . $response->body());
        }

        return $response->json();
    }

    protected function showSubscription(PaypalSetting $setting, string $subscriptionId): array
    {
        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken($setting))
            ->acceptJson()
            ->get("/v1/billing/subscriptions/{$subscriptionId}");

        if (! $response->successful()) {
            throw new RuntimeException('PayPal show subscription failed: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * List transactions for a subscription in a given date range.
     */
    public function listTransactions(WeeklySubscription $subscription, string $startTime, string $endTime): array
    {
        $setting = $this->requireSetting();

        if (! filled($subscription->paypal_subscription_id)) {
            return [];
        }

        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken($setting))
            ->acceptJson()
            ->get("/v1/billing/subscriptions/{$subscription->paypal_subscription_id}/transactions", [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);

        if (! $response->successful()) {
            return [];
        }

        return $response->json('transactions', []);
    }

    /**
     * Check if PayPal collected a payment for the given week (Monday charge for past week).
     */
    public function hasPaymentForWeek(WeeklySubscription $subscription, \DateTimeInterface $weekStart): bool
    {
        // PayPal charges on Monday for the past week (Sun-Sat).
        // Look for transactions from the Monday after the week starts through to the following Sunday.
        $chargeDate = \Carbon\Carbon::parse($weekStart)->next(Carbon::MONDAY);
        $startTime = $chargeDate->copy()->startOfDay()->toIso8601String();
        $endTime = $chargeDate->copy()->addDays(6)->endOfDay()->toIso8601String();

        $transactions = $this->listTransactions($subscription, $startTime, $endTime);

        foreach ($transactions as $tx) {
            $status = strtoupper($tx['status'] ?? '');
            if ($status === 'COMPLETED') {
                return true;
            }
        }

        return false;
    }

    // ─── Helpers ────────────────────────────────────────────────

    /**
     * Calculate the next start time: next Monday at 00:00 UTC.
     * First charge happens on the next Monday, then every Monday after.
     */
    protected function nextStartTime(): string
    {
        return now()->next(Carbon::MONDAY)->startOfDay()->toIso8601String();
    }

    protected function mapStatus(string $paypalStatus): string
    {
        return match ($paypalStatus) {
            'ACTIVE' => 'active',
            'APPROVED' => 'active',
            'SUSPENDED' => 'suspended',
            'CANCELLED' => 'cancelled',
            'EXPIRED' => 'expired',
            default => 'pending',
        };
    }

    protected function findLink(array $response, string $rel): ?string
    {
        foreach ((array) ($response['links'] ?? []) as $link) {
            if (($link['rel'] ?? '') === $rel) {
                return $link['href'] ?? null;
            }
        }

        return null;
    }

    protected function accessToken(PaypalSetting $setting): string
    {
        $cacheKey = 'paypal_sub_token_' . md5(($setting->id ?: 'env') . '|' . $setting->environmentLabel());

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($setting) {
            $response = Http::baseUrl($setting->baseUrl())
                ->asForm()
                ->withBasicAuth((string) $setting->client_id, (string) $setting->client_secret)
                ->acceptJson()
                ->post('/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                throw new RuntimeException('PayPal access token failed: ' . $response->body());
            }

            $token = $response->json('access_token');

            if (! filled($token)) {
                throw new RuntimeException('PayPal access token was not returned.');
            }

            return $token;
        });
    }

    protected function requireSetting(): PaypalSetting
    {
        $setting = PaypalSetting::active();

        if (! $setting || ! $setting->isConfigured()) {
            throw new RuntimeException('PayPal is not configured.');
        }

        return $setting;
    }
}
