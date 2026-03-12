@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span>
        @if ($offer)
            <a href="{{ route('workspace.invite-offer', ['project' => $offer->project->id]) }}">Create offer</a><span>›</span>
        @endif
        <span>Billing method</span>
    </div>

    @include('workspace.partials.flash')

    <div class="grid-2">
        <section class="project-card">
            <div class="page-heading" style="margin-bottom:18px">
                <div>
                    <h1 style="font-size:38px">Billing methods</h1>
                    <p>Save PayPal, Visa, or Mastercard methods. Set one as primary or remove it any time.</p>
                </div>
            </div>

            @if ($offer)
                <div class="chat-card" style="background:#f8fbff;border-style:dashed;margin-bottom:18px">
                    <strong>Current offer</strong>
                    <p class="muted" style="margin:8px 0 0">This offer will use the billing method you save or set as primary here.</p>
                </div>
            @endif

            <div class="setting-list">
                @forelse ($billingMethods as $method)
                    <div class="setting-row" style="align-items:flex-start">
                        <div>
                            <strong>{{ $method->display_label }}</strong>
                            <span>
                                {{ $method->is_default ? 'Primary billing method.' : 'Saved billing method.' }}
                                Added {{ $method->created_at?->diffForHumans() ?: 'recently' }}.
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
                @empty
                    <p class="empty">No billing methods saved yet. Add the first one on the right.</p>
                @endforelse
            </div>
        </section>

        <section class="project-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Add new billing method</h3>
            <form method="post" action="{{ route('workspace.billing-method.store') }}">
                @csrf
                @if ($offer)
                    <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                @endif

                <div class="form-group">
                    <label class="form-label" for="method_type">Billing method</label>
                    <select class="select" id="method_type" name="method_type" required>
                        @foreach (['PayPal', 'Visa', 'Mastercard'] as $methodType)
                            <option value="{{ $methodType }}" @selected(old('method_type') === $methodType)>{{ $methodType }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="label">Label</label>
                    <input class="input" id="label" name="label" type="text" value="{{ old('label') }}" placeholder="PayPal email or card label" required />
                </div>

                <div class="form-group">
                    <label class="form-label" for="last_four">Last 4 digits <span class="muted small">(cards only)</span></label>
                    <input class="input" id="last_four" name="last_four" type="text" maxlength="4" value="{{ old('last_four') }}" placeholder="4242" />
                </div>

                <label class="checkbox-line">
                    <input name="set_default" type="checkbox" value="1" {{ old('set_default', true) ? 'checked' : '' }} />
                    <span><strong>Make this the primary billing method</strong><br /><span class="muted">Primary billing is used first for new offers and billing checks.</span></span>
                </label>

                <div class="form-actions">
                    <a class="link-button" href="{{ $offer ? route('workspace.project-pending') : route('workspace.settings') }}">‹ Back</a>
                    <button class="button button-primary" type="submit">{{ $offer ? 'Save and continue' : 'Add billing method' }}</button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
