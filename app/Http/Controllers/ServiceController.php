<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceSubscription;
use App\Models\PaypalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Show the public service page.
     */
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)
            ->where('status', 'active')
            ->with('freelancer')
            ->firstOrFail();

        $freelancer = $service->freelancer;

        // Check if user is already subscribed
        $existingSubscription = null;
        if (auth()->check()) {
            $existingSubscription = ServiceSubscription::where('service_id', $service->id)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['active', 'pending'])
                ->first();
        }

        return view('site.service', [
            'service' => $service,
            'freelancer' => $freelancer,
            'existingSubscription' => $existingSubscription,
        ]);
    }

    /**
     * Subscribe to a service — creates PayPal monthly subscription.
     */
    public function subscribe(Request $request, string $slug)
    {
        $service = Service::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $user = $request->user();

        if (! $user) {
            return redirect()->route('client.login.form')
                ->with('info', 'Please log in to subscribe to a service.');
        }

        // Check for existing active subscription
        $existing = ServiceSubscription::where('service_id', $service->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->first();

        if ($existing) {
            return redirect()->route('services.show', $service->slug)
                ->with('info', 'You already have an active subscription to this service.');
        }

        $setting = PaypalSetting::where('is_active', true)->first();

        if (! $setting) {
            return redirect()->route('services.show', $service->slug)
                ->with('error', 'Payment system is not configured.');
        }

        try {
            $accessToken = $this->getAccessToken($setting);

            // 1. Create product
            $product = $this->createProduct($setting, $accessToken, $service);

            // 2. Create monthly plan with immediate first payment (setup_fee)
            $plan = $this->createMonthlyPlan(
                $setting,
                $accessToken,
                $product['id'],
                $service,
            );

            // 3. Create subscription
            $subscription = $this->createPayPalSubscription(
                $setting,
                $accessToken,
                $plan['id'],
                $service,
                route('services.subscribe-return', $service->slug),
                route('services.subscribe-cancel', $service->slug),
            );

            $approveUrl = collect($subscription['links'] ?? [])
                ->firstWhere('rel', 'approve')['href'] ?? null;

            if (! $approveUrl) {
                throw new \RuntimeException('PayPal did not return an approval URL.');
            }

            // Save local record
            ServiceSubscription::create([
                'service_id' => $service->id,
                'user_id' => $user->id,
                'status' => 'pending',
                'paypal_subscription_id' => $subscription['id'] ?? null,
                'amount' => $service->monthly_price,
                'currency' => $service->currency,
                'paypal_payload' => $subscription,
            ]);

            return redirect()->away($approveUrl);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('services.show', $service->slug)
                ->with('error', 'Could not start PayPal subscription: ' . $e->getMessage());
        }
    }

    /**
     * Handle PayPal return after subscription approval.
     */
    public function subscribeReturn(Request $request, string $slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $subscriptionId = $request->query('subscription_id');

        $sub = ServiceSubscription::where('service_id', $service->id)
            ->where('user_id', $user->id)
            ->where('paypal_subscription_id', $subscriptionId)
            ->first();

        if (! $sub) {
            return redirect()->route('services.show', $service->slug)
                ->with('error', 'Subscription not found.');
        }

        // Activate the subscription
        $sub->update([
            'status' => 'active',
            'subscribed_at' => now(),
            'next_billing_at' => now()->addMonth(),
        ]);

        // Increment active_users count on the service
        $service->increment('active_users');

        return redirect()->route('workspace.dashboard')
            ->with('success', 'You are now subscribed to ' . $service->name . '! A message thread has been opened with your freelancer.');
    }

    /**
     * Handle PayPal cancel.
     */
    public function subscribeCancel(Request $request, string $slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        $user = $request->user();

        // Mark pending subscription as cancelled
        ServiceSubscription::where('service_id', $service->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return redirect()->route('services.show', $service->slug)
            ->with('info', 'Subscription was cancelled.');
    }

    // ─── PayPal helpers ─────────────────────────────────────

    protected function getAccessToken(PaypalSetting $setting): string
    {
        $response = Http::baseUrl($setting->baseUrl())
            ->withBasicAuth($setting->client_id, $setting->client_secret)
            ->asForm()
            ->post('/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal auth failed: ' . $response->body());
        }

        return $response->json('access_token');
    }

    protected function createProduct(PaypalSetting $setting, string $token, Service $service): array
    {
        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($token)
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders(['PayPal-Request-Id' => 'svc-prod-' . Str::uuid()])
            ->post('/v1/catalogs/products', [
                'name' => Str::limit($service->name . ' — ' . $service->freelancer->name, 127, ''),
                'description' => Str::limit($service->description ?: $service->name, 256, ''),
                'type' => 'SERVICE',
                'category' => 'SOFTWARE',
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal create product failed: ' . $response->body());
        }

        return $response->json();
    }

    protected function createMonthlyPlan(PaypalSetting $setting, string $token, string $productId, Service $service): array
    {
        $amount = number_format((float) $service->monthly_price, 2, '.', '');

        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($token)
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders(['PayPal-Request-Id' => 'svc-plan-' . Str::uuid()])
            ->post('/v1/billing/plans', [
                'product_id' => $productId,
                'name' => Str::limit('Service: ' . $service->name, 127, ''),
                'description' => "Monthly payment of \${$amount} for {$service->name}",
                'billing_cycles' => [
                    [
                        'frequency' => [
                            'interval_unit' => 'MONTH',
                            'interval_count' => 1,
                        ],
                        'tenure_type' => 'REGULAR',
                        'sequence' => 1,
                        'total_cycles' => 0, // infinite
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'value' => $amount,
                                'currency_code' => $service->currency,
                            ],
                        ],
                    ],
                ],
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'payment_failure_threshold' => 3,
                    'setup_fee_failure_action' => 'CONTINUE',
                    'setup_fee' => [
                        'value' => $amount, // charge first payment immediately
                        'currency_code' => $service->currency,
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('PayPal create plan failed: ' . $response->body());
        }

        return $response->json();
    }

    protected function createPayPalSubscription(PaypalSetting $setting, string $token, string $planId, Service $service, string $returnUrl, string $cancelUrl): array
    {
        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($token)
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders(['PayPal-Request-Id' => 'svc-sub-' . Str::uuid()])
            ->post('/v1/billing/subscriptions', [
                'plan_id' => $planId,
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
            throw new \RuntimeException('PayPal create subscription failed: ' . $response->body());
        }

        return $response->json();
    }
}
