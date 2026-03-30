@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
@php
    $backUrl = $offer ? route('workspace.project-pending') : route('workspace.dashboard');
    $skipUrl = $offer ? route('workspace.project-pending') : route('workspace.dashboard');
    $isWeeklyMode = ($paymentMode ?? 'recurring') === 'weekly';
    $defaultBillingChoice = $isWeeklyMode ? 'weekly' : ($acbaConfigured ? 'acba' : ($paypalConfigured ? 'paypal' : 'acba'));
@endphp
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span>
        @if ($offer)
            <a href="{{ route('workspace.hire-flow', array_filter(['project' => $offer->project->id, 'freelancer' => $offer->freelancer_id])) }}">Project + offer</a><span>›</span>
        @endif
        <span>Billing method</span>
    </div>

    @include('workspace.partials.flash')

    <section class="billing-choice-card">
        <div class="billing-choice-header">
            <h1>Add Billing Method</h1>
            <p>Choose how you want to add billing for this offer. The ACBA / ArCa option opens the secure hosted card form, while PayPal keeps the existing approval flow. You can also skip this step and come back later.</p>
        </div>

        @if ($offer)
            <div class="note-panel" style="margin:0 auto 22px;max-width:760px">
                <strong>Current offer</strong>
                <p class="muted small" style="margin:8px 0 0">{{ $offer->project->title }} · {{ $offer->freelancer_display_name }}</p>
            </div>
        @endif

        <div class="billing-choice-form" data-billing-choice-shell>
            @if ($isWeeklyMode && $offer && $paypalConfigured)
                <label class="billing-choice-option">
                    <input name="billing_choice" type="radio" value="weekly" @checked($defaultBillingChoice === 'weekly') />
                    <span class="billing-choice-indicator"></span>
                    <span class="billing-choice-copy">
                        <strong>Weekly PayPal Subscription</strong>
                        <span>
                            Set up automatic weekly payments via PayPal.
                            You will be charged <strong>${{ number_format((float)$offer->hourly_rate * (int)$offer->weekly_limit, 2) }}/week</strong>
                            ({{ $offer->freelancer_display_name }} · ${{ number_format((float)$offer->hourly_rate, 2) }}/hr × {{ $offer->weekly_limit }}h).
                            First charge starts immediately, then every week on Monday.
                        </span>
                    </span>
                    <span class="billing-choice-brand billing-choice-brand-paypal">PayPal</span>
                </label>
            @endif

            @if (! $isWeeklyMode)
                <label class="billing-choice-option {{ $acbaConfigured ? '' : 'billing-choice-disabled' }}">
                    <input name="billing_choice" type="radio" value="acba" @checked($defaultBillingChoice === 'acba') {{ $acbaConfigured ? '' : 'disabled' }} />
                    <span class="billing-choice-indicator"></span>
                    <span class="billing-choice-copy">
                        <strong>Credit or Debit Card</strong>
                        <span>{{ $acbaConfigured ? 'Use the ACBA / ArCa hosted checkout page. The client is sent to the secure card payment form, then returned to HireHelper after the bank confirms the billing setup.' : 'ACBA / ArCa card gateway is not configured yet in the admin panel. Add the live or test gateway credentials first.' }}</span>
                    </span>
                    <span class="billing-choice-brand billing-choice-brand-card">ACBA / ArCa</span>
                </label>

                <label class="billing-choice-option {{ $paypalConfigured ? '' : 'billing-choice-disabled' }}">
                    <input name="billing_choice" type="radio" value="paypal" @checked($defaultBillingChoice === 'paypal') {{ $paypalConfigured ? '' : 'disabled' }} />
                    <span class="billing-choice-indicator"></span>
                    <span class="billing-choice-copy">
                        <strong>PayPal</strong>
                        <span>{{ $paypalConfigured ? 'Use the saved PayPal approval flow so the client connects PayPal securely.' : 'PayPal is not configured yet in the admin panel. Add the PayPal API credentials first.' }}</span>
                    </span>
                    <span class="billing-choice-brand billing-choice-brand-paypal">PayPal</span>
                </label>
            @endif
        </div>

        <div class="billing-choice-actions">
            <a class="link-button" href="{{ $backUrl }}">‹ Back</a>
            <div class="billing-choice-action-buttons">
                <a class="button button-secondary" href="{{ $skipUrl }}">Skip for now</a>
                <button class="button button-primary" type="button" data-billing-choice-submit>Add</button>
            </div>
        </div>

        <form id="acba-start-form" method="post" action="{{ route('workspace.billing-method.acba.start') }}" style="display:none">
            @csrf
            @if ($offer)
                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
            @endif
        </form>

        <form id="paypal-start-form" method="post" action="{{ route('workspace.billing-method.paypal.start') }}" style="display:none">
            @csrf
            @if ($offer)
                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
            @endif
        </form>

        @if ($offer)
            <form id="weekly-start-form" method="post" action="{{ route('workspace.weekly-subscription.start') }}" style="display:none">
                @csrf
                <input type="hidden" name="offer_id" value="{{ $offer->id }}">
            </form>
        @endif
    </section>

    @if ($billingMethods->isNotEmpty())
        <section class="project-card billing-saved-methods" style="margin-top:24px">
            <h2 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Saved billing methods</h2>
            <div class="setting-list">
                @foreach ($billingMethods as $method)
                    <div class="setting-row" style="align-items:flex-start">
                        <div>
                            <strong>{{ $method->display_label }}</strong>
                            <span>
                                {{ $method->is_default ? 'Primary billing method.' : 'Saved billing method.' }}
                                @if ($method->provider === 'paypal' && $method->verified_at)
                                    Verified with PayPal {{ $method->verified_at->diffForHumans() }}.
                                @elseif ($method->provider === 'acba_arca' && $method->verified_at)
                                    Verified with ACBA / ArCa {{ $method->verified_at->diffForHumans() }}.
                                @else
                                    Added {{ $method->created_at?->diffForHumans() ?: 'recently' }}.
                                @endif
                            </span>
                        </div>
                        <div class="method-action-stack">
                            @if (! $method->is_default)
                                <form method="post" action="{{ route('workspace.billing-method.primary') }}">
                                    @csrf
                                    <input type="hidden" name="billing_method_id" value="{{ $method->id }}">
                                    @if ($offer)
                                        <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                    @endif
                                    <button class="button button-secondary button-small" type="submit">Make primary</button>
                                </form>
                            @else
                                <span class="badge">Primary</span>
                            @endif

                            <form method="post" action="{{ route('workspace.billing-method.destroy') }}" onsubmit="return confirm('Remove this billing method?');">
                                @csrf
                                <input type="hidden" name="billing_method_id" value="{{ $method->id }}">
                                @if ($offer)
                                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                                @endif
                                <button class="button button-ghost button-small" type="submit">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const shell = document.querySelector('[data-billing-choice-shell]');
        const button = document.querySelector('[data-billing-choice-submit]');
        const acbaForm = document.getElementById('acba-start-form');
        const paypalForm = document.getElementById('paypal-start-form');

        if (!shell || !button) {
            return;
        }

        const weeklyForm = document.getElementById('weekly-start-form');

        button.addEventListener('click', function () {
            const selected = shell.querySelector('input[name="billing_choice"]:checked');
            const value = selected ? selected.value : 'acba';

            if (value === 'weekly') {
                if (weeklyForm) {
                    weeklyForm.submit();
                }
                return;
            }

            if (value === 'paypal') {
                if (paypalForm) {
                    paypalForm.submit();
                }
                return;
            }

            if (acbaForm) {
                acbaForm.submit();
            }
        });
    });
</script>
@endsection
