<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Mail\PaymentMethodAddedMail;
use App\Models\ClientBillingMethod;
use App\Models\EmailSetting;
use App\Models\ProjectOffer;
use App\Services\PayPalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PayPalBillingController extends Controller
{
    public function start(Request $request, PayPalService $payPalService): RedirectResponse
    {
        $user = $request->user();

        if (! $payPalService->isConfigured()) {
            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $request->input('offer_id')]))
                ->with('info', 'PayPal is not configured yet in the admin panel.');
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
                ->with('info', 'Create the project brief and offer before you connect PayPal.');
        }

        try {
            $setup = $payPalService->createSetupToken(
                $user,
                route('workspace.billing-method.paypal.return'),
                route('workspace.billing-method.paypal.cancel'),
                $offer,
            );
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $offer?->id]))
                ->with('info', 'PayPal could not start the approval flow. Please check the admin PayPal credentials and try again.');
        }

        session()->put('paypal_billing_setup', [
            'setup_token_id' => $setup['id'],
            'offer_id' => $offer?->id,
            'started_at' => now()->toDateTimeString(),
        ]);

        return redirect()->away($setup['approve_url']);
    }

    public function handleReturn(Request $request, PayPalService $payPalService): RedirectResponse
    {
        $user = $request->user();
        $context = session('paypal_billing_setup');

        if (! is_array($context) || empty($context['setup_token_id'])) {
            return redirect()
                ->route('workspace.billing-method')
                ->with('info', 'The PayPal approval session has expired. Please start again.');
        }

        $offer = ! empty($context['offer_id'])
            ? ProjectOffer::query()
                ->whereHas('project', fn ($query) => $query->where('user_id', $user->id))
                ->with(['project', 'freelancer'])
                ->find($context['offer_id'])
            : null;

        try {
            $tokenData = $payPalService->createPaymentToken((string) $context['setup_token_id']);
        } catch (\Throwable $exception) {
            report($exception);
            session()->forget('paypal_billing_setup');

            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $offer?->id]))
                ->with('info', 'PayPal returned to HireHelper but the billing method could not be saved. Please try again.');
        }

        try {
            $billing = null;

            DB::transaction(function () use ($user, $offer, $tokenData, $context, &$billing) {
                $user->billingMethods()->update(['is_default' => false]);

                $billing = ClientBillingMethod::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'provider' => 'paypal',
                        'provider_payment_token_id' => data_get($tokenData, 'id'),
                    ],
                    [
                        'method_type' => 'PayPal',
                        'label' => data_get($tokenData, 'payment_source.paypal.email_address'),
                        'last_four' => null,
                        'is_default' => true,
                        'provider' => 'paypal',
                        'provider_customer_id' => data_get($tokenData, 'customer.id'),
                        'provider_payer_id' => data_get($tokenData, 'payment_source.paypal.payer_id'),
                        'provider_email' => data_get($tokenData, 'payment_source.paypal.email_address'),
                        'provider_setup_token_id' => $context['setup_token_id'],
                        'provider_payment_token_id' => data_get($tokenData, 'id'),
                        'provider_payload' => $tokenData,
                        'verified_at' => now(),
                    ],
                );

                if ($offer) {
                    $offer->update([
                        'billing_method' => $billing->display_label,
                        'status' => 'pending',
                    ]);

                    // Move project to "active" when payment method is added
                    if ($offer->project && in_array($offer->project->status, ['draft', 'pending'], true)) {
                        $offer->project->update(['status' => 'active']);
                    }
                }
            });
        } catch (\Throwable $exception) {
            report($exception);
            session()->forget('paypal_billing_setup');

            return redirect()
                ->route('workspace.billing-method', array_filter(['offer' => $offer?->id]))
                ->with('info', 'PayPal returned successfully, but HireHelper could not save the billing method. Run the latest database migrations on the server, then try again.');
        }

        session()->forget('paypal_billing_setup');

        if ($billing && EmailSetting::isActive('payment_method_added')) {
            try {
                Mail::to($user->email)->send(new PaymentMethodAddedMail(
                    billingMethod: $billing,
                    userName: $user->name,
                    dashboardUrl: route('workspace.dashboard'),
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route($offer ? 'workspace.project-pending' : 'workspace.billing-method')
            ->with('success', 'Your payment method was successfully added.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $context = session('paypal_billing_setup');
        session()->forget('paypal_billing_setup');

        return redirect()
            ->route('workspace.billing-method', array_filter(['offer' => $context['offer_id'] ?? null]))
            ->with('info', 'PayPal approval was cancelled.');
    }
}
