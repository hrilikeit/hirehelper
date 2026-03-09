@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span>
        <a href="{{ route('workspace.invite-offer', ['project' => $offer->project->id]) }}">Create offer</a><span>›</span>
        <span>Billing method</span>
    </div>

    @include('workspace.partials.flash')

    <div class="wizard-card compact" style="max-width:760px">
        <div class="wizard-header">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai">
            <h1 class="wizard-title" style="font-size:42px">Add billing method</h1>
            <p class="wizard-subtitle">Choose how the client workspace should handle payments before the contract starts.</p>
        </div>

        <form method="post" action="{{ route('workspace.billing-method.store') }}">
            @csrf
            <input type="hidden" name="offer_id" value="{{ $offer->id }}">
            <input type="hidden" name="billing_method" value="{{ $billingMethod }}" data-billing-input>

            <div class="setting-list">
                @foreach (['Credit or Debit Card' => '💳', 'PayPal' => 'Ⓟ'] as $method => $icon)
                    <button type="button" class="setting-row {{ $billingMethod === $method ? 'is-selected' : '' }}" data-billing-choice="{{ $method }}">
                        <div style="text-align:left">
                            <strong>{{ $method }}</strong>
                            <span>
                                @if ($method === 'Credit or Debit Card')
                                    To verify the card, a very small temporary authorization may be used by the payment provider.
                                @else
                                    Use a PayPal account if the client prefers a faster checkout handoff for billing verification.
                                @endif
                            </span>
                        </div>
                        <div style="font-size:28px;color:var(--primary)">{{ $icon }}</div>
                    </button>
                @endforeach
            </div>

            <div class="form-actions">
                <a class="link-button" href="{{ route('workspace.invite-offer', ['project' => $offer->project->id]) }}">‹ Back</a>
                <div style="display:flex;gap:12px">
                    <a class="button button-secondary" href="{{ route('workspace.dashboard-live') }}">Skip for now</a>
                    <button class="button button-primary" type="submit">Add</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
