<?php

namespace App\Services;

use App\Models\AcbaSetting;
use App\Models\ProjectOffer;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class AcbaArcaService
{
    public function setting(): ?AcbaSetting
    {
        return AcbaSetting::active();
    }

    public function isConfigured(): bool
    {
        return $this->setting()?->isConfigured() ?? false;
    }

    public function registerVerification(User $user, string $returnUrl, ?ProjectOffer $offer = null): array
    {
        $setting = $this->requireSetting();
        $orderNumber = $this->makeOrderNumber($user, $offer);

        $payload = [
            'userName' => $setting->username(),
            'password' => $setting->password(),
            'orderNumber' => $orderNumber,
            'amount' => $setting->verificationAmountMinorUnits(),
            'currency' => $setting->currencyNumericCode(),
            'returnUrl' => $returnUrl,
            'description' => $offer
                ? 'HireHelper.ai card verification for offer #' . $offer->id
                : 'HireHelper.ai card verification',
            'language' => 'en',
            'pageView' => 'DESKTOP',
            'jsonParams' => json_encode(array_filter([
                'workspace_user_id' => (string) $user->id,
                'offer_id' => $offer?->id ? (string) $offer->id : null,
                'gateway' => 'acba_arca',
            ]), JSON_UNESCAPED_SLASHES),
        ];

        $response = Http::asForm()
            ->acceptJson()
            ->timeout(90)
            ->post($setting->registerUrl(), $payload);

        if (! $response->successful()) {
            throw new RuntimeException('ACBA / ArCa register request failed: ' . $response->body());
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('ACBA / ArCa register response was not valid JSON.');
        }

        if ((int) ($data['errorCode'] ?? 1) !== 0 || blank($data['formUrl'] ?? null)) {
            throw new RuntimeException((string) ($data['errorMessage'] ?? 'ACBA / ArCa did not return a payment form URL.'));
        }

        return [
            'order_number' => $orderNumber,
            'order_id' => $data['orderId'] ?? null,
            'form_url' => $data['formUrl'],
            'payload' => $data,
        ];
    }

    public function fetchOrderStatus(string $orderId): array
    {
        $setting = $this->requireSetting();

        $response = Http::asForm()
            ->acceptJson()
            ->timeout(90)
            ->post($setting->orderStatusUrl(), [
                'userName' => $setting->username(),
                'password' => $setting->password(),
                'orderId' => $orderId,
                'language' => 'en',
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('ACBA / ArCa order status request failed: ' . $response->body());
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('ACBA / ArCa order status response was not valid JSON.');
        }

        return $data;
    }

    public function isSuccessful(array $status): bool
    {
        return (int) ($status['errorCode'] ?? 1) === 0
            && (int) ($status['orderStatus'] ?? -1) === 2;
    }

    protected function requireSetting(): AcbaSetting
    {
        $setting = $this->setting();

        if (! $setting || ! $setting->isConfigured()) {
            throw new RuntimeException('ACBA / ArCa card gateway is not configured.');
        }

        return $setting;
    }

    protected function makeOrderNumber(User $user, ?ProjectOffer $offer = null): string
    {
        $prefix = $offer ? 'HHO' . $offer->id : 'HHU' . $user->id;
        $stamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(4));

        return substr($prefix . $stamp . $random, 0, 32);
    }
}
