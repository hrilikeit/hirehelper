@extends('workspace.layouts.base', ['activeNav' => 'hire'])

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span><span>Project setup</span>
    </div>

    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <span class="badge"><span class="dot"></span> Project setup</span>
            <h1>Project brief</h1>
            <p>Capture the work details in one clean page.</p>
        </div>
        <a class="button button-secondary" href="{{ route('workspace.dashboard') }}">Back to dashboard</a>
    </div>

    <div class="hire-layout">
        <section class="hire-brief-card">
            <h2>Project brief</h2>
            <p>Only the fields that help define the project are kept here.</p>

            <form method="post" action="{{ route('workspace.hire-flow.store') }}">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}" />

                <div class="form-group">
                    <label class="form-label" for="title">Project title</label>
                    <input class="input" id="title" name="title" placeholder="Write the project title" required value="{{ old('title', $project->title) }}" />
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">What needs to be done</label>
                    <textarea class="textarea" id="description" name="description" placeholder="Describe the project scope, deliverables, and expectations." required>{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="experience_level">Experience level</label>
                        <select class="select" id="experience_level" name="experience_level">
                            @foreach ($experienceOptions as $option)
                                <option value="{{ $option }}" @selected(old('experience_level', $project->experience_level) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="timeframe">Timeframe</label>
                        <select class="select" id="timeframe" name="timeframe">
                            @foreach ($timeframeOptions as $option)
                                <option value="{{ $option }}" @selected(old('timeframe', $project->timeframe) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="specialty">Specialty</label>
                        <select class="select" id="specialty" name="specialty">
                            @foreach ($specialtyOptions as $option)
                                <option value="{{ $option }}" @selected(old('specialty', $project->specialty) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="link-button" href="{{ route('workspace.dashboard') }}">‹ Back</a>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="button button-secondary" type="submit" name="action" value="save">Save brief</button>
                        <button class="button button-primary" type="submit" name="action" value="continue">Save & continue</button>
                    </div>
                </div>
            </form>
        </section>

        <aside class="hire-summary-card">
            <h2>Current brief</h2>
            <p>Only the essential project details are shown here.</p>

            <div class="brief-mini">
                <div class="mini-row">
                    <span class="mini-label">Current title</span>
                    <strong>{{ $project->title ?: 'Not set yet' }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Experience</span>
                    <strong>{{ $project->experience_level ?: 'Not set yet' }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Timeframe</span>
                    <strong>{{ $project->timeframe ?: 'Not set yet' }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Specialty</span>
                    <strong>{{ $project->specialty ?: 'Not set yet' }}</strong>
                </div>
            </div>

            @if ($project->exists)
                <div class="inline-actions" style="margin-top:18px">
                    <a class="button button-primary button-small" href="{{ route('workspace.invite-offer', ['project' => $project->id]) }}">Continue to offer</a>
                </div>
            @endif

            <div class="separator"></div>

            <h3 style="font-size:24px;letter-spacing:-.03em;margin:0 0 14px">Freelancer personas</h3>
            @forelse ($freelancers as $freelancer)
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
                    </div>
                </div>
                @if (! $loop->last)
                    <div class="separator"></div>
                @endif
            @empty
                <p class="empty">No freelancer personas added yet. You can still continue and invite a freelancer by email on the next step.</p>
            @endforelse
        </aside>
    </div>
</div>
@endsection
