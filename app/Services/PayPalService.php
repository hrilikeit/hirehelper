<?php

namespace App\Services;

use App\Models\PaypalSetting;
use App\Models\ProjectOffer;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PayPalService
{
    public function setting(): ?PaypalSetting
    {
        $dbSetting = PaypalSetting::active();

        if ($dbSetting) {
            return $dbSetting;
        }

        $clientId = config('paypal.client_id');
        $clientSecret = config('paypal.client_secret');

        if (! filled($clientId) || ! filled($clientSecret)) {
            return null;
        }

        $setting = new PaypalSetting();
        $setting->name = 'Environment PayPal config';
        $setting->is_active = true;
        $setting->is_live = (bool) config('paypal.live', false);
        $setting->api_username = config('paypal.api_username');
        $setting->api_password = config('paypal.api_password');
        $setting->client_id = $clientId;
        $setting->client_secret = $clientSecret;
        $setting->webhook_id = config('paypal.webhook_id');

        return $setting;
    }

    public function isConfigured(): bool
    {
        return $this->setting()?->isConfigured() ?? false;
    }

    public function createSetupToken(User $user, string $returnUrl, string $cancelUrl, ?ProjectOffer $offer = null): array
    {
        $setting = $this->requireSetting();

        $payload = [
            'payment_source' => [
                'paypal' => [
                    'description' => $offer
                        ? 'HireHelper.ai billing method for ' . ($offer->project->title ?: 'project offer')
                        : 'HireHelper.ai billing method',
                    'permit_multiple_payment_tokens' => false,
                    'usage_pattern' => 'IMMEDIATE',
                    'usage_type' => 'MERCHANT',
                    'customer_type' => 'CONSUMER',
                    'experience_context' => [
                        'shipping_preference' => 'NO_SHIPPING',
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                        'brand_name' => 'HireHelper.ai',
                        'locale' => 'en-US',
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                    ],
                ],
            ],
        ];

        if (filled($user->name)) {
            $payload['payment_source']['paypal']['shipping'] = [
                'name' => [
                    'full_name' => $user->name,
                ],
            ];
        }

        if ($user->invoiceDetail?->country) {
            $payload['payment_source']['paypal']['shipping']['address'] = array_filter([
                'address_line_1' => $user->invoiceDetail->address_line_1,
                'address_line_2' => $user->invoiceDetail->address_line_2,
                'admin_area_2' => $user->invoiceDetail->city,
                'postal_code' => $user->invoiceDetail->postal_code,
                'country_code' => $this->countryCode($user->invoiceDetail->country),
            ]);
        }

        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken())
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders([
                'PayPal-Request-Id' => (string) Str::uuid(),
            ])
            ->post('/v3/vault/setup-tokens', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('PayPal setup token request failed: ' . $response->body());
        }

        $data = $response->json();
        $approveUrl = collect($data['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        if (! $approveUrl) {
            throw new RuntimeException('PayPal setup token was created but no approval URL was returned.');
        }

        return [
            'id' => $data['id'] ?? null,
            'customer_id' => Arr::get($data, 'customer.id'),
            'approve_url' => $approveUrl,
            'payload' => $data,
        ];
    }

    public function createPaymentToken(string $setupTokenId): array
    {
        $setting = $this->requireSetting();

        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken())
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders([
                'PayPal-Request-Id' => (string) Str::uuid(),
            ])
            ->post('/v3/vault/payment-tokens', [
                'payment_source' => [
                    'token' => [
                        'id' => $setupTokenId,
                        'type' => 'SETUP_TOKEN',
                    ],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('PayPal payment token request failed: ' . $response->body());
        }

        return $response->json();
    }

    public function deletePaymentToken(string $paymentTokenId): void
    {
        $setting = $this->requireSetting();

        $response = Http::baseUrl($setting->baseUrl())
            ->withToken($this->accessToken())
            ->acceptJson()
            ->delete('/v3/vault/payment-tokens/' . $paymentTokenId);

        if ($response->status() === 404 || $response->status() === 204) {
            return;
        }

        if (! $response->successful()) {
            throw new RuntimeException('PayPal payment token delete failed: ' . $response->body());
        }
    }

    protected function accessToken(): string
    {
        $setting = $this->requireSetting();
        $cacheKey = 'paypal_access_token_' . md5(($setting->id ?: 'env') . '|' . $setting->environmentLabel() . '|' . (string) $setting->updated_at);

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($setting) {
            $response = Http::baseUrl($setting->baseUrl())
                ->asForm()
                ->withBasicAuth((string) $setting->client_id, (string) $setting->client_secret)
                ->acceptJson()
                ->post('/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                throw new RuntimeException('PayPal access token request failed: ' . $response->body());
            }

            $token = $response->json('access_token');

            if (! filled($token)) {
                throw new RuntimeException('PayPal access token response did not include an access_token value.');
            }

            return $token;
        });
    }

    protected function requireSetting(): PaypalSetting
    {
        $setting = $this->setting();

        if (! $setting || ! $setting->isConfigured()) {
            throw new RuntimeException('PayPal is not configured.');
        }

        return $setting;
    }

    protected function countryCode(?string $country): ?string
    {
        if (! filled($country)) {
            return null;
        }

        $country = strtoupper(trim((string) $country));

        $map = [
            'UNITED STATES' => 'US',
            'USA' => 'US',
            'UNITED KINGDOM' => 'GB',
            'UK' => 'GB',
            'GREAT BRITAIN' => 'GB',
            'ARMENIA' => 'AM',
            'CANADA' => 'CA',
            'GERMANY' => 'DE',
            'FRANCE' => 'FR',
            'SPAIN' => 'ES',
            'ITALY' => 'IT',
            'NETHERLANDS' => 'NL',
            'POLAND' => 'PL',
            'PORTUGAL' => 'PT',
            'ROMANIA' => 'RO',
            'FINLAND' => 'FI',
            'SWEDEN' => 'SE',
            'NORWAY' => 'NO',
        ];

        if (isset($map[$country])) {
            return $map[$country];
        }

        return strlen($country) === 2 ? $country : null;
    }
}
