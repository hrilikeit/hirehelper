<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\PaypalSetting;
use App\Models\ProjectOffer;
use App\Models\WeeklySubscription;
use App\Services\PayPalSubscriptionService;
use Illuminate\Http\Request;
use Throwable;

class WeeklySubscriptionController extends Controller
{
    /**
     * Start a weekly PayPal subscription for a project offer.
     */
    public function start(Request $request, PayPalSubscriptionService $service)
    {
        $user = $request->user();

        $data = $request->validate([
            'offer_id' => ['required', 'integer'],
        ]);

        $offer = ProjectOffer::query()
            ->whereHas('project', fn ($q) => $q->where('user_id', $user->id))
            ->with(['project', 'freelancer'])
            ->findOrFail($data['offer_id']);

        // Check if there's already an active subscription for this offer
        $existing = WeeklySubscription::query()
            ->where('project_offer_id', $offer->id)
            ->whereIn('status', ['active', 'pending_approval'])
            ->first();

        if ($existing && $existing->status === 'active') {
            return redirect()
                ->route('workspace.project-active')
                ->with('info', 'A weekly subscription is already active for this project.');
        }

        try {
            $result = $service->createWeeklySubscription(
                $offer,
                $user->id,
                route('workspace.weekly-subscription.return', ['subscription' => '__SUB_ID__']),
                route('workspace.weekly-subscription.cancel', ['subscription' => '__SUB_ID__']),
            );

            // Update the return/cancel URLs with the actual subscription ID
            $sub = $result['subscription'];

            return redirect()->away($result['approve_url']);
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('workspace.billing-method', ['offer' => $offer->id])
                ->with('error', 'Could not start the PayPal weekly subscription. Please try again or use a different payment method.');
        }
    }

    /**
     * Handle PayPal return after client approves the subscription.
     */
    public function handleReturn(Request $request, PayPalSubscriptionService $service)
    {
        $user = $request->user();
        $subscriptionId = $request->query('subscription_id');

        if (! $subscriptionId) {
            return redirect()
                ->route('workspace.dashboard')
                ->with('error', 'PayPal subscription approval was not completed.');
        }

        // Find the subscription by PayPal subscription ID
        $subscription = WeeklySubscription::query()
            ->where('paypal_subscription_id', $subscriptionId)
            ->where('user_id', $user->id)
            ->first();

        if (! $subscription) {
            return redirect()
                ->route('workspace.dashboard')
                ->with('error', 'Could not find the subscription record.');
        }

        try {
            $subscription = $service->handleApproval($subscription);

            // Update the offer status
            $offer = $subscription->offer;

            if ($offer) {
                $offer->update([
                    'billing_method' => 'PayPal Weekly Subscription',
                    'payment_status' => 'subscription_active',
                    'status' => 'active',
                    'activated_at' => now(),
                ]);

                $offer->project?->update(['status' => 'active']);
            }

            return redirect()
                ->route('workspace.project-active')
                ->with('success', 'Weekly PayPal subscription is now active! You will be billed ' . $subscription->formatted_amount . ' every week.');
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('workspace.dashboard')
                ->with('error', 'Subscription was approved but we could not verify it. Please contact support.');
        }
    }

    /**
     * Handle PayPal cancellation.
     */
    public function cancelReturn(Request $request)
    {
        $subscriptionId = $request->query('subscription_id');

        if ($subscriptionId) {
            WeeklySubscription::query()
                ->where('paypal_subscription_id', $subscriptionId)
                ->where('user_id', $request->user()->id)
                ->where('status', 'pending_approval')
                ->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        }

        return redirect()
            ->route('workspace.billing-method')
            ->with('info', 'Weekly subscription setup was cancelled. You can try again or choose a different payment method.');
    }
}
