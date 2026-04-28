<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\MilestoneProject;
use App\Services\PayPalSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MilestoneController extends Controller
{
    /**
     * Show milestone project page (public link).
     */
    public function show(string $token)
    {
        $project = MilestoneProject::where('token', $token)
            ->with(['milestones', 'freelancer'])
            ->firstOrFail();

        $user = Auth::user();

        // Auto-assign client if logged in and project has no client yet
        if ($user && ! $project->user_id) {
            $project->update(['user_id' => $user->id, 'status' => 'active']);
        }

        $isOwner = $user && $project->user_id === $user->id;
        $hasBillingMethod = $user ? $user->billingMethods()->where('is_default', true)->exists() : false;

        return view('milestones.show', [
            'project' => $project,
            'milestones' => $project->milestones,
            'freelancer' => $project->freelancer,
            'isOwner' => $isOwner,
            'user' => $user,
            'hasBillingMethod' => $hasBillingMethod,
        ]);
    }

    /**
     * Update a milestone (client can edit name/description).
     */
    public function update(Request $request, string $token, Milestone $milestone)
    {
        $project = MilestoneProject::where('token', $token)->firstOrFail();
        $user = $request->user();

        if (! $user || $project->user_id !== $user->id) {
            abort(403);
        }

        if ($milestone->milestone_project_id !== $project->id) {
            abort(404);
        }

        if ($milestone->status !== 'pending') {
            return back()->with('error', 'Cannot edit a milestone that has been funded.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $milestone->update($data);

        return back()->with('success', 'Milestone updated.');
    }

    /**
     * Fund a milestone via PayPal.
     */
    public function fund(Request $request, string $token, Milestone $milestone)
    {
        $project = MilestoneProject::where('token', $token)->firstOrFail();
        $user = $request->user();

        if (! $user || $project->user_id !== $user->id) {
            abort(403);
        }

        if ($milestone->milestone_project_id !== $project->id || $milestone->status !== 'pending') {
            return back()->with('error', 'This milestone cannot be funded.');
        }

        // Check billing method
        if (! $user->billingMethods()->where('is_default', true)->exists()) {
            return back()->with('error', 'Please add a payment method before funding milestones.');
        }

        try {
            $service = app(PayPalSubscriptionService::class);
            $result = $service->createBonusOrder(
                amount: (float) $milestone->amount,
                description: 'Milestone: ' . $milestone->name . ' — ' . $project->title,
                returnUrl: route('milestones.fund-return', [$token, $milestone->id]),
                cancelUrl: route('milestones.fund-cancel', [$token, $milestone->id]),
            );

            session([
                'milestone_fund_order_id' => $result['order_id'],
                'milestone_fund_id' => $milestone->id,
            ]);

            return redirect()->away($result['approve_url']);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'PayPal error: ' . $e->getMessage());
        }
    }

    /**
     * PayPal return after funding.
     */
    public function fundReturn(Request $request, string $token, Milestone $milestone)
    {
        $project = MilestoneProject::where('token', $token)->firstOrFail();
        $orderId = session('milestone_fund_order_id');

        if (! $orderId || (int) session('milestone_fund_id') !== $milestone->id) {
            return redirect()->route('milestones.show', $token)->with('error', 'Invalid payment session.');
        }

        try {
            $service = app(PayPalSubscriptionService::class);
            $capture = $service->captureOrder($orderId);

            $captureId = data_get($capture, 'purchase_units.0.payments.captures.0.id');

            $milestone->update([
                'status' => 'funded',
                'paypal_order_id' => $orderId,
                'paypal_capture_id' => $captureId,
                'funded_at' => now(),
            ]);

            session()->forget(['milestone_fund_order_id', 'milestone_fund_id']);

            return redirect()->route('milestones.show', $token)->with('success', 'Milestone "' . $milestone->name . '" has been funded successfully!');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('milestones.show', $token)->with('error', 'Payment capture failed: ' . $e->getMessage());
        }
    }

    /**
     * PayPal cancel for funding.
     */
    public function fundCancel(Request $request, string $token, Milestone $milestone)
    {
        session()->forget(['milestone_fund_order_id', 'milestone_fund_id']);

        return redirect()->route('milestones.show', $token)->with('info', 'Payment was cancelled.');
    }

    /**
     * Release a funded milestone.
     */
    public function release(Request $request, string $token, Milestone $milestone)
    {
        $project = MilestoneProject::where('token', $token)->firstOrFail();
        $user = $request->user();

        if (! $user || $project->user_id !== $user->id) {
            abort(403);
        }

        if ($milestone->milestone_project_id !== $project->id || $milestone->status !== 'funded') {
            return back()->with('error', 'This milestone cannot be released.');
        }

        $milestone->update([
            'status' => 'released',
            'released_at' => now(),
        ]);

        // Check if all milestones are released
        $allReleased = $project->milestones()->where('status', '!=', 'released')->count() === 0;
        if ($allReleased) {
            $project->update(['status' => 'completed']);
        }

        return back()->with('success', 'Milestone "' . $milestone->name . '" has been released.');
    }
}
