@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <h1>Projects</h1>
        </div>
        <a class="button button-primary" href="{{ route('workspace.hire-flow') }}">New project</a>
    </div>

    <section class="panel" style="margin-bottom:24px">
        @if ($projects->isEmpty() && ! $draftProject)
            <p class="empty">You don't have any projects yet. Create a new project to get started.</p>
            <div class="illustration-wrap">
                <img alt="Projects illustration" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:180px" />
            </div>
        @else
            @if ($draftProject)
                <div class="project-row">
                    <div>
                        <div class="project-title">{{ $draftProject->title }}</div>
                        <div class="project-sub">{{ $draftProject->specialty }} · {{ $draftProject->timeframe }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <span class="status-pill status-neutral">Draft</span>
                        <a class="cta-link" href="{{ route('workspace.hire-flow', ['project' => $draftProject->id]) }}">Open</a>
                    </div>
                </div>
                @if ($projects->isNotEmpty())
                    <div class="separator"></div>
                @endif
            @endif

            @foreach ($projects as $project)
                <div class="project-row">
                    <div>
                        <div class="project-title">{{ $project->title }}</div>
                        <div class="project-sub">{{ $project->specialty }} · {{ $project->timeframe }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        @php
                            $statusClass = match($project->status) {
                                'active', 'accepted' => 'status-active',
                                'pending' => 'status-pending',
                                'completed' => 'status-active',
                                'cancelled' => 'status-neutral',
                                default => 'status-neutral',
                            };
                        @endphp
                        <span class="status-pill {{ $statusClass }}">{{ ucfirst($project->status) }}</span>
                    </div>
                </div>
                @if (! $loop->last)
                    <div class="separator"></div>
                @endif
            @endforeach
        @endif
    </section>

    @if ($featuredFreelancers->isNotEmpty())
        <section class="panel">
            <h3>Freelancers ready to hire</h3>
            <hr />
            @foreach ($featuredFreelancers as $freelancer)
                <div class="offer-row">
                    <div class="avatar-line">
                        <img alt="{{ $freelancer->name }}" src="{{ $freelancer->avatar_url }}" />
                        <div>
                            <strong>{{ $freelancer->name }}</strong>
                            <span>{{ $freelancer->title }}</span>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div class="muted small">${{ number_format((float) $freelancer->hourly_rate, 0) }}/hr</div>
                        <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap">
                            <a class="cta-link" href="{{ $freelancer->publicProfileUrl() }}">View profile</a>
                            <a class="cta-link" href="{{ route('workspace.hire-flow', array_filter(['project' => $draftProject?->id, 'freelancer' => $freelancer->id])) }}">Hire</a>
                        </div>
                    </div>
                </div>
                @if (! $loop->last)
                    <div class="separator"></div>
                @endif
            @endforeach
        </section>
    @endif
</div>
@endsection
