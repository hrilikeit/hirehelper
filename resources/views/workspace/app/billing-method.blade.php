@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
@php
    $mode = request()->query('mode');
    $cardStep = $mode === 'card';
    $backUrl = $offer ? route('workspace.project-pending') : route('workspace.dashboard');
    $skipUrl = $offer ? route('workspace.project-pending') : route('workspace.dashboard');
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

    @if (! $cardStep)
        <section class="billing-choice-card">
            <div class="billing-choice-header">
                <h1>Add Billing Method</h1>
                <p>Choose how you want to add billing for this offer. You can continue with PayPal now, or open the manual card step. You can also skip this step and come back later.</p>
            </div>

            @if ($offer)
                <div class="note-panel" style="margin:0 auto 22px;max-width:760px">
                    <strong>Current offer</strong>
                    <p class="muted small" style="margin:8px 0 0">{{ $offer->project->title }} · {{ $offer->freelancer_display_name }}</p>
                </div>
            @endif

            <div class="billing-choice-form" data-billing-choice-shell>
                <label class="billing-choice-option">
                    <input checked name="billing_choice" type="radio" value="card" />
                    <span class="billing-choice-indicator"></span>
                    <span class="billing-choice-copy">
                        <strong>Credit or Debit Card</strong>
                        <span>In order to verify your card, we may make a temporary charge of $0.01. Card gateway will be finished in the next step, and for now you can use the manual card fallback.</span>
                    </span>
                    <span class="billing-choice-brand billing-choice-brand-card">Card</span>
                </label>

                <label class="billing-choice-option {{ $paypalConfigured ? '' : 'billing-choice-disabled' }}">
                    <input name="billing_choice" type="radio" value="paypal" {{ $paypalConfigured ? '' : 'disabled' }} />
                    <span class="billing-choice-indicator"></span>
                    <span class="billing-choice-copy">
                        <strong>PayPal</strong>
                        <span>{{ $paypalConfigured ? 'Use the saved PayPal approval flow so the client connects PayPal securely.' : 'PayPal is not configured yet in the admin panel. Add the PayPal API credentials first.' }}</span>
                    </span>
                    <span class="billing-choice-brand billing-choice-brand-paypal">PayPal</span>
                </label>
            </div>

            <div class="billing-choice-actions">
                <a class="link-button" href="{{ $backUrl }}">‹ Back</a>
                <div class="billing-choice-action-buttons">
                    <a class="button button-secondary" href="{{ $skipUrl }}">Skip for now</a>
                    <button class="button button-primary" type="button" data-billing-choice-submit>Add</button>
                </div>
            </div>

            <form id="paypal-start-form" method="post" action="{{ route('workspace.billing-method.paypal.start') }}" style="display:none">
                @csrf
                @if ($offer)
                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                @endif
            </form>
        </section>
    @else
        <section class="billing-choice-card">
            <div class="billing-choice-header">
                <h1>Add Billing Method</h1>
                <p>Enter the manual billing details now. Later you can replace this with the full card gateway or a PayPal billing method.</p>
            </div>

            <div class="billing-manual-step">
                <form method="post" action="{{ route('workspace.billing-method.store') }}">
                    @csrf
                    @if ($offer)
                        <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                    @endif
                    <input type="hidden" name="set_default" value="1">

                    <div class="form-group">
                        <label class="form-label" for="method_type">Card type</label>
                        <select class="select" id="method_type" name="method_type" required>
                            @foreach (['Visa', 'Mastercard'] as $methodType)
                                <option value="{{ $methodType }}" @selected(old('method_type', 'Visa') === $methodType)>{{ $methodType }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="label">Card label</label>
                        <input class="input" id="label" name="label" type="text" value="{{ old('label') }}" placeholder="Company card" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="last_four">Last 4 digits</label>
                        <input class="input" id="last_four" name="last_four" type="text" maxlength="4" value="{{ old('last_four') }}" placeholder="4242" required />
                    </div>

                    <div class="form-actions">
                        <a class="link-button" href="{{ route('workspace.billing-method', array_filter(['offer' => $offer?->id])) }}">‹ Back</a>
                        <div style="display:flex;gap:12px;flex-wrap:wrap">
                            <a class="button button-secondary" href="{{ $skipUrl }}">Skip for now</a>
                            <button class="button button-primary" type="submit">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    @endif

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
                                @if ($method->method_type === 'PayPal' && $method->verified_at)
                                    Verified with PayPal {{ $method->verified_at->diffForHumans() }}.
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
        const paypalForm = document.getElementById('paypal-start-form');

        if (!shell || !button) {
            return;
        }

        button.addEventListener('click', function () {
            const selected = shell.querySelector('input[name="billing_choice"]:checked');
            const value = selected ? selected.value : 'card';
            const base = @json(route('workspace.billing-method', array_filter(['offer' => $offer?->id])));

            if (value === 'paypal') {
                if (paypalForm) {
                    paypalForm.submit();
                }
                return;
            }

            window.location.href = base + (base.includes('?') ? '&' : '?') + 'mode=card';
        });
    });
</script>
@endsection
