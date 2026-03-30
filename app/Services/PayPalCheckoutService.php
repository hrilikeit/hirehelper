<?php

namespace App\Services;

use App\Models\PaymentLink;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PayPalCheckoutService
{
    public function createOrder(PaymentLink $paymentLink): array
    {
        $response = $this->client()
            ->withHeaders([
                'PayPal-Request-Id' => 'paylink-' . $paymentLink->slug . '-' . Str::uuid(),
            ])
            ->post($this->baseUrl() . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'PAYMENT_LINK_' . $paymentLink->getKey(),
                        'custom_id' => 'payment_link:' . $paymentLink->getKey(),
                        'description' => Str::limit(
                            $paymentLink->description ?: ('Freelancer payment for ' . ($paymentLink->freelancer?->name ?: 'project')),
                            127,
                            ''
                        ),
                        'amount' => [
                            'currency_code' => $paymentLink->currency,
                            'value' => $paymentLink->formatted_amount,
                        ],
                    ],
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            'brand_name' => config('paypal.brand_name', config('app.name')),
                            'landing_page' => 'LOGIN',
                            'user_action' => 'PAY_NOW',
                            'shipping_preference' => 'NO_SHIPPING',
                            'return_url' => route('payment-links.paypal.return', $paymentLink),
                            'cancel_url' => route('payment-links.paypal.cancel', $paymentLink),
                        ],
                    ],
                ],
            ])
            ->throw()
            ->json();

        $approvalUrl = $this->approvalUrl($response);

        if (! filled($approvalUrl)) {
            throw new RuntimeException('PayPal approval URL was not returned.');
        }

        return [
            'order' => $response,
            'approval_url' => $approvalUrl,
        ];
    }

    public function captureOrFetchOrder(string $orderId): array
    {
        $response = $this->client()->post($this->baseUrl() . '/v2/checkout/orders/' . $orderId . '/capture', (object) []);

        if ($response->successful()) {
            return $response->json();
        }

        if (in_array($response->status(), [409, 422], true)) {
            return $this->showOrder($orderId);
        }

        $response->throw();

        return [];
    }

    public function showOrder(string $orderId): array
    {
        return $this->client()
            ->get($this->baseUrl() . '/v2/checkout/orders/' . $orderId)
            ->throw()
            ->json();
    }

    public function syncPaymentLink(PaymentLink $paymentLink): PaymentLink
    {
        if (! filled($paymentLink->paypal_order_id)) {
            return $paymentLink;
        }

        $paymentLink->applyPayPalOrder($this->showOrder($paymentLink->paypal_order_id));

        return $paymentLink->fresh();
    }

    protected function client(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withToken($this->accessToken());
    }

    protected function accessToken(): string
    {
        $clientId = (string) config('paypal.client_id');
        $secret = (string) config('paypal.secret');

        if ($clientId === '' || $secret === '') {
            throw new RuntimeException('PayPal credentials are not configured.');
        }

        $response = Http::acceptJson()
            ->asForm()
            ->withBasicAuth($clientId, $secret)
            ->post($this->baseUrl() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ])
            ->throw()
            ->json();

        $token = data_get($response, 'access_token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('PayPal access token was not returned.');
        }

        return $token;
    }

    protected function approvalUrl(array $order): ?string
    {
        foreach ((array) data_get($order, 'links', []) as $link) {
            $rel = data_get($link, 'rel');

            if (in_array($rel, ['payer-action', 'approve', 'approval_url'], true)) {
                return data_get($link, 'href');
            }
        }

        return null;
    }

    protected function baseUrl(): string
    {
        return config('paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
