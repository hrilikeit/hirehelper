@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
@php
    $offer = $primaryOffer;
    $project = $primaryProject;
    $isActive = $offer && in_array($offer->status, ['active', 'accepted'], true);
    $needsBilling = $offer && ! ($offer->billing_method ?: ($billingMethod?->display_label));
@endphp

<div class="container">
    @include('workspace.partials.flash')

    @if ($needsBilling)
        <div class="notice-banner" data-dismissible-notice>
            <div style="display:flex;align-items:center;gap:12px">
                <span class="notice-icon">⚠</span>
                <span><strong>Critical notice:</strong> all contracts are paused until the billing method is verified.</span>
            </div>
            <div style="display:flex;align-items:center;gap:18px">
                <a href="{{ route('workspace.billing-method', ['offer' => $offer->id]) }}">Verify billing method</a>
                <button class="link-button" data-dismiss-notice type="button">Dismiss</button>
            </div>
        </div>
    @endif

    <div class="page-heading">
        <div>
            <h1>Projects</h1>
        </div>
        <a class="button button-primary" href="{{ route('workspace.hire-flow') }}">New project</a>
    </div>

    <section class="panel" style="margin-bottom:24px">
        @foreach ($projects as $p)
            @php
                $pOffer = $p->offers->first();
                $statusClass = match($p->status) {
                    'active', 'accepted' => 'status-active',
                    'pending' => 'status-pending',
                    'completed' => 'status-active',
                    'cancelled' => 'status-neutral',
                    default => 'status-neutral',
                };
                $linkRoute = match(true) {
                    $p->status === 'draft' => route('workspace.hire-flow', ['project' => $p->id]),
                    $pOffer && in_array($pOffer->status, ['active'], true) => route('workspace.project-active'),
                    $pOffer && $pOffer->status === 'pending' => route('workspace.project-pending'),
                    default => route('workspace.hire-flow', ['project' => $p->id]),
                };
            @endphp
            <div class="project-row">
                <div>
                    <div class="project-title">{{ $p->title }}</div>
                    <div class="project-sub">
                        {{ $p->specialty }} · {{ $p->timeframe }}
                        @if ($pOffer)
                            · {{ $pOffer->freelancer_display_name }}
                        @endif
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="status-pill {{ $statusClass }}">{{ ucfirst($p->status) }}</span>
                    <a class="cta-link" href="{{ $linkRoute }}">Open</a>
                </div>
            </div>
            @if (! $loop->last)
                <div class="separator"></div>
            @endif
        @endforeach

        @if ($projects->isEmpty())
            <p class="empty">No projects yet. Create one to get started.</p>
        @endif
    </section>
</div>
@endsection
