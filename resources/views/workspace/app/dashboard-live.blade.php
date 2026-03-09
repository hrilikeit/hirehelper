@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
@php
    $offer = $primaryOffer;
    $project = $primaryProject;
    $isActive = $offer && $offer->status === 'active';
    $needsBilling = $offer && ! ($offer->billing_method ?: $billingMethod?->method_type ?? null);
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
            <span class="badge"><span class="dot"></span> Signed in</span>
            <h1>Client dashboard</h1>
            <p>This state reflects the dashboard after a brief is saved and the freelancer hiring flow has started.</p>
        </div>
        <a class="button button-primary" href="{{ route('workspace.hire-flow') }}">New project</a>
    </div>

    <div class="dashboard-grid">
        <section class="panel tall">
            <div class="float-action"><a class="button button-primary button-small" href="{{ route('workspace.hire-flow') }}">New project</a></div>
            <h3>Project drafts</h3>
            <hr />
            @if ($project)
                <div class="project-row">
                    <div>
                        <div class="project-title">{{ $project->title }}</div>
                        <div class="project-sub">Latest brief saved from the client-side flow.</div>
                    </div>
                    <span class="status-pill status-neutral">{{ ucfirst($project->status) }}</span>
                </div>
                <div class="separator"></div>
                <div class="inline-actions">
                    <a class="cta-link" href="{{ route('workspace.hire-flow', ['project' => $project->id]) }}">Open project setup</a>
                    @if ($offer)
                        <a class="cta-link" href="{{ $isActive ? route('workspace.project-active') : route('workspace.project-pending') }}">{{ $isActive ? 'Open active contract' : 'Open pending offer' }}</a>
                    @endif
                </div>
            @else
                <p class="empty">Save a brief to create your first project.</p>
            @endif
        </section>

        <section class="panel tall">
            <h3>Projects</h3>
            <hr />
            @if ($offer && $project)
                <div class="project-row">
                    <div style="display:flex;gap:14px">
                        <span class="project-bullet"></span>
                        <div>
                            <div class="project-title">{{ $project->title }}</div>
                            <div class="project-sub">Freelancer: {{ $offer->freelancer->name }}</div>
                        </div>
                    </div>
                    <span class="status-pill {{ $isActive ? 'status-active' : 'status-pending' }}">{{ $isActive ? 'Active' : 'Pending' }}</span>
                </div>
                <div class="separator"></div>
                <a class="cta-link" href="{{ $isActive ? route('workspace.project-active') : route('workspace.project-pending') }}">{{ $isActive ? 'Open active project' : 'Open project terms and settings' }}</a>
            @else
                <p class="empty">No live project found yet.</p>
            @endif
        </section>
    </div>

    <div class="dashboard-grid">
        <section class="panel">
            <h3>My offers</h3>
            <hr />
            @if ($offer)
                <div class="offer-row">
                    <div class="avatar-line">
                        <img alt="{{ $offer->freelancer->name }}" src="{{ $offer->freelancer->avatar_url }}" />
                        <div>
                            <strong>{{ $offer->freelancer->name }}</strong>
                            <span>{{ $offer->freelancer->location }}</span>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div class="muted small">{{ optional($offer->sent_at)->diffForHumans() ?: 'just now' }}</div>
                        <a class="cta-link" href="{{ $isActive ? route('workspace.project-active') : route('workspace.project-pending') }}">View offer</a>
                    </div>
                </div>
            @else
                <p class="empty">Offers will appear here once you continue from the brief into the hiring flow.</p>
            @endif
        </section>

        <section class="panel">
            <h3>Co-workers</h3>
            <hr />
            <div class="illustration-wrap">
                <img alt="Co-workers" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:140px" />
                <div class="muted small" style="margin-top:8px">Invite team members when the project is ready.</div>
            </div>
        </section>

        <section class="panel">
            <h3>Activity</h3>
            <hr />
            @if ($offer)
                <div class="activity-row">
                    <div>
                        <div class="project-title">{{ $isActive ? 'Contract active' : 'Offer sent' }}</div>
                        <div class="project-sub">{{ $isActive ? 'The contract is active with' : 'A new offer was created for' }} {{ $offer->freelancer->name }}.</div>
                    </div>
                    <span class="status-pill status-neutral">{{ optional($offer->updated_at)->diffForHumans() }}</span>
                </div>
            @else
                <p class="empty">Activity appears after the first offer is sent.</p>
            @endif
        </section>
    </div>
</div>
@endsection
