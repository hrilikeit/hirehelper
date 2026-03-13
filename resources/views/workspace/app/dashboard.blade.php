@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <h1>Client dashboard</h1>
            <p>This is the client dashboard before an offer becomes active. Save the project brief and offer details on one combined page, then continue into billing and activation.</p>
        </div>
        <a class="button button-primary" href="{{ route('workspace.hire-flow') }}">New project</a>
    </div>

    <div class="dashboard-grid">
        <section class="panel tall">
            <div class="float-action"><a class="button button-primary button-small" href="{{ route('workspace.hire-flow') }}">New project</a></div>
            <h3>Project drafts</h3>
            <hr />

            @if ($draftProject)
                <div class="project-row">
                    <div>
                        <div class="project-title">{{ $draftProject->title }}</div>
                        <div class="project-sub">Draft brief saved in the project setup page. Open it any time to update the scope.</div>
                    </div>
                    <span class="status-pill status-neutral">Draft</span>
                </div>
                <div class="separator"></div>
                <p class="muted">Last saved {{ optional($draftProject->last_saved_at)->diffForHumans() ?: 'just now' }}.</p>
                <div style="margin-top:18px">
                    <a class="cta-link" href="{{ route('workspace.hire-flow', ['project' => $draftProject->id]) }}">Open project setup</a>
                </div>
            @else
                <p class="empty">You do not have a draft yet. Create a project brief to start the hiring flow.</p>
                <div class="illustration-wrap">
                    <img alt="Projects illustration" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:180px" />
                </div>
            @endif
        </section>

        <section class="panel tall">
            <h3>Projects</h3>
            <hr />

            @if ($projects->isEmpty())
                <p class="empty">You do not have an active contract yet. Once an offer is sent and accepted, the project will appear here.</p>
                <div class="illustration-wrap">
                    <img alt="Projects illustration" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:180px" />
                </div>
            @else
                @foreach ($projects->take(3) as $project)
                    <div class="project-row">
                        <div>
                            <div class="project-title">{{ $project->title }}</div>
                            <div class="project-sub">{{ $project->specialty }} · {{ $project->timeframe }}</div>
                        </div>
                        <span class="status-pill {{ $project->status === 'active' ? 'status-active' : ($project->status === 'pending' ? 'status-pending' : 'status-neutral') }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    @if (! $loop->last)
                        <div class="separator"></div>
                    @endif
                @endforeach
            @endif
        </section>
    </div>

    <div class="dashboard-grid">
        <section class="panel">
            <h3>Freelancers ready to hire</h3>
            <hr />
            @forelse ($featuredFreelancers as $freelancer)
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
            @empty
                <p class="empty">No freelancer personas added yet. You can still invite a freelancer by email from the combined project and offer page.</p>
            @endforelse
        </section>

        <section class="panel">
            <h3>Co-workers</h3>
            <hr />
            <div class="illustration-wrap">
                <img alt="Co-workers illustration" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:140px" />
                <div class="muted small" style="margin-top:8px">Shared workspace support can be added in the next phase.</div>
            </div>
        </section>

        <section class="panel">
            <h3>Activity</h3>
            <hr />
            @if ($projects->isEmpty())
                <p class="empty">Workspace notifications, project updates, and payment reminders will appear here.</p>
            @else
                @foreach ($projects->take(3) as $project)
                    <div class="activity-row">
                        <div>
                            <div class="project-title">{{ ucfirst($project->status) }} project</div>
                            <div class="project-sub">{{ $project->title }}</div>
                        </div>
                        <span class="status-pill status-neutral">{{ $project->updated_at->diffForHumans() }}</span>
                    </div>
                    @if (! $loop->last)
                        <div class="separator"></div>
                    @endif
                @endforeach
            @endif
        </section>
    </div>
</div>
@endsection
