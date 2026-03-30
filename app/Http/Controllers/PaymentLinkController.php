<?php

namespace App\Http\Controllers;

use App\Models\PaymentLink;
use App\Services\PayPalCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PaymentLinkController extends Controller
{
    public function show(PaymentLink $paymentLink): View
    {
        return view('payments.show', [
            'paymentLink' => $paymentLink,
        ]);
    }

    public function startPayPal(PaymentLink $paymentLink, PayPalCheckoutService $payPalCheckoutService): RedirectResponse
    {
        if ($paymentLink->isPaid()) {
            return redirect()
                ->route('payment-links.show', $paymentLink)
                ->with('payment_notice', 'This payment link has already been paid.');
        }

        if (! $paymentLink->isPayable()) {
            return redirect()
                ->route('payment-links.show', $paymentLink)
                ->with('payment_notice', 'This payment link is not currently available for payment.');
        }

        try {
            $createdOrder = $payPalCheckoutService->createOrder($paymentLink);

            $paymentLink->forceFill([
                'paypal_order_id' => data_get($createdOrder, 'order.id'),
                'paypal_order_status' => data_get($createdOrder, 'order.status'),
            ])->save();

            return redirect()->away((string) $createdOrder['approval_url']);
        } catch (Throwable $throwable) {
            report($throwable);

            return redirect()
                ->route('payment-links.show', $paymentLink)
                ->with('payment_error', 'We could not start PayPal checkout right now. Please try again.');
        }
    }

    public function handleReturn(Request $request, PaymentLink $paymentLink, PayPalCheckoutService $payPalCheckoutService): RedirectResponse
    {
        if ($paymentLink->isPaid()) {
            return redirect()
                ->route('payment-links.show', $paymentLink)
                ->with('payment_success', 'Payment already completed.');
        }

        $orderId = trim((string) ($request->query('token') ?: $paymentLink->paypal_order_id));

        if ($orderId === '') {
            return redirect()
                ->route('payment-links.show', $paymentLink)
                ->with('payment_error', 'PayPal did not return a valid order token.');
        }

        try {
            $paymentLink->applyPayPalOrder($payPalCheckoutService->captureOrFetchOrder($orderId));

            if ($paymentLink->isPaid()) {
                return redirect()
                    ->route('payment-links.show', $paymentLink)
                    ->with('payment_success', 'Payment completed successfully.');
            }

            return redirect()
                ->route('payment-links.show', $paymentLink)
                ->with('payment_notice', 'PayPal approved the payment, but it is not completed yet.');
        } catch (Throwable $throwable) {
            report($throwable);

            return redirect()
                ->route('payment-links.show', $paymentLink)
                ->with('payment_error', 'We could not confirm the PayPal payment yet. Please contact support or try again later.');
        }
    }

    public function cancel(PaymentLink $paymentLink): RedirectResponse
    {
        return redirect()
            ->route('payment-links.show', $paymentLink)
            ->with('payment_notice', 'The PayPal payment was cancelled.');
    }
}
