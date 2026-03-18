<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\ClientBillingMethod;
use App\Models\ProjectOffer;
use App\Services\AcbaArcaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcbaBillingController extends Controller
{
    public function start(Request $request, AcbaArcaService $acbaService): RedirectResponse
    {
        $user = $request->user();

        if (! $acbaService->isConfigured()) {
            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $request->input('offer_id')]))
                ->with('info', 'ACBA / ArCa card gateway is not configured yet in the admin panel.');
        }

        $data = $request->validate([
            'offer_id' => ['nullable', 'integer'],
        ]);

        $offer = ! empty($data['offer_id'])
            ? ProjectOffer::query()
                ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
                ->with(['project', 'freelancer'])
                ->find($data['offer_id'])
            : null;

        if (! empty($data['offer_id']) && ! $offer) {
            return redirect()
                ->route('workspace.hire-flow')
                ->with('info', 'Create the project brief and offer before you connect a card billing method.');
        }

        try {
            $registration = $acbaService->registerVerification(
                $user,
                route('workspace.billing-method.acba.return', array_filter(['offer' => $offer?->id])),
                $offer,
            );
        } catch (\Throwable $exception) {
            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $offer?->id]))
                ->with('info', 'ACBA / ArCa could not start the card approval flow. Please check the admin gateway credentials and try again.');
        }

        session()->put('acba_billing_setup', [
            'offer_id' => $offer?->id,
            'order_id' => $registration['order_id'] ?? null,
            'order_number' => $registration['order_number'] ?? null,
            'started_at' => now()->toDateTimeString(),
        ]);

        return redirect()->away((string) $registration['form_url']);
    }

    public function handleReturn(Request $request, AcbaArcaService $acbaService): RedirectResponse
    {
        $user = $request->user();
        $context = session('acba_billing_setup', []);
        $offerId = $request->query('offer') ?: ($context['offer_id'] ?? null);

        $offer = $offerId
            ? ProjectOffer::query()
                ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
                ->with(['project', 'freelancer'])
                ->find($offerId)
            : null;

        $orderId = (string) ($request->query('orderId') ?: ($context['order_id'] ?? ''));

        if (blank($orderId)) {
            session()->forget('acba_billing_setup');

            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $offer?->id]))
                ->with('info', 'ACBA / ArCa returned without an order reference. Please try again.');
        }

        try {
            $status = $acbaService->fetchOrderStatus($orderId);
        } catch (\Throwable $exception) {
            session()->forget('acba_billing_setup');

            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $offer?->id]))
                ->with('info', 'ACBA / ArCa returned to HireHelper but the card billing method could not be verified. Please try again.');
        }

        if (! $acbaService->isSuccessful($status)) {
            session()->forget('acba_billing_setup');

            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $offer?->id]))
                ->with('info', (string) ($status['actionCodeDescription'] ?? $status['errorMessage'] ?? 'The card verification was not completed.'));
        }

        $billing = null;

        DB::transaction(function () use ($user, $offer, $status, $context, $orderId, &$billing) {
            $user->billingMethods()->update(['is_default' => false]);

            $billing = ClientBillingMethod::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => 'acba_arca',
                    'provider_payment_token_id' => $orderId,
                ],
                [
                    'method_type' => 'Card',
                    'label' => 'ACBA / ArCa card',
                    'last_four' => null,
                    'is_default' => true,
                    'provider' => 'acba_arca',
                    'provider_setup_token_id' => $context['order_number'] ?? null,
                    'provider_payment_token_id' => $orderId,
                    'provider_payload' => $status,
                    'verified_at' => now(),
                ],
            );

            if ($offer) {
                $offer->update([
                    'billing_method' => $billing->display_label,
                    'status' => 'pending',
                ]);
            }
        });

        session()->forget('acba_billing_setup');

        return redirect()
            ->route($offer ? 'workspace.project-pending' : 'workspace.billing-method', array_filter(['offer' => $offer?->id]))
            ->with('success', 'ACBA / ArCa card billing method connected successfully.');
    }
}
