@extends('workspace.layouts.base', ['activeNav' => 'hire'])

@section('content')
@php
    $selectedFreelancerId = old('selected_freelancer_id', $selectedFreelancer?->id);
    $freelancerEmailValue = old('freelancer_email', $offer?->freelancer_email ?? $selectedFreelancer?->contact_email ?? '');

    $hourlyRateSeed = str_replace(',', '.', (string) old('hourly_rate', $offer?->hourly_rate ?? $selectedFreelancer?->hourly_rate ?? 35));
    $hourlyRateNumber = is_numeric($hourlyRateSeed) ? (float) $hourlyRateSeed : 35.0;
    $hourlyRateValue = rtrim(rtrim(number_format($hourlyRateNumber, 2, '.', ''), '0'), '.');

    $weeklyLimitSeed = preg_replace('/[^0-9]/', '', (string) old('weekly_limit', $offer?->weekly_limit ?? 40));
    $weeklyLimitValue = (int) ($weeklyLimitSeed !== '' ? $weeklyLimitSeed : 40);

    $selectedFreelancerLocked = filled($selectedFreelancer) && filled($freelancerEmailValue);
@endphp
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span><span>Project + offer</span>
    </div>

    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <span class="badge"><span class="dot"></span> Project + offer</span>
            <h1>Project brief and offer</h1>
            <p>Define the work and send the freelancer offer from one combined page.</p>
        </div>
        <a class="button button-secondary" href="{{ route('workspace.dashboard') }}">Back to dashboard</a>
    </div>

    <div class="hire-layout">
        <section class="hire-brief-card">
            <form method="post" action="{{ route('workspace.hire-flow.store') }}" data-invite-form>
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}" />
                @if (filled($selectedFreelancerId))
                    <input type="hidden" name="selected_freelancer_id" value="{{ $selectedFreelancerId }}" />
                @endif

                <h2>Project brief</h2>
                <p>Keep the brief focused, then finish the offer details below.</p>

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

                <div class="separator"></div>

                <div class="offer-section-header compact">
                    <div>
                        <h2>Create an offer</h2>
                        <p>Only the freelancer, hourly rate, and weekly limit are needed here.</p>
                    </div>
                </div>

                @if ($selectedFreelancer)
                    <div class="selected-freelancer-card selected-freelancer-card-compact">
                        <div class="selected-freelancer-main">
                            <img alt="{{ $selectedFreelancer->name }}" class="selected-freelancer-avatar" src="{{ $selectedFreelancer->avatar_url }}" />
                            <div class="selected-freelancer-text">
                                <strong>{{ $selectedFreelancer->name }}</strong>
                                <span>{{ $selectedFreelancer->title ?: 'Freelancer profile' }}</span>
                                <div class="selected-freelancer-meta-row">
                                    <span class="badge">Selected from Hire Now</span>
                                </div>
                            </div>
                        </div>
                        @if ($selectedFreelancer->status === 'active')
                            <a class="cta-link selected-freelancer-link" href="{{ $selectedFreelancer->publicProfileUrl() }}">Open public profile</a>
                        @endif
                    </div>
                @endif

                <input type="hidden" name="freelancer_email" value="{{ $freelancerEmailValue }}" />

                <div class="compact-offer-shell">
                    <div class="compact-offer-card">
                        <label class="form-label" for="hourly_rate">Rate</label>
                        <div class="compact-inline-field compact-inline-rate">
                            <input class="input compact-inline-input" id="hourly_rate" min="1" name="hourly_rate" step="0.01" type="number" value="{{ $hourlyRateValue }}" required />
                            <span class="compact-inline-addon">$ / hr</span>
                        </div>
                    </div>

                    <div class="compact-offer-card compact-offer-card-wide">
                        <label class="form-label" for="weekly_limit">Weekly limit</label>
                        <div class="compact-inline-field compact-inline-limit">
                            <input class="input compact-inline-input" id="weekly_limit" min="1" name="weekly_limit" step="1" type="number" value="{{ $weeklyLimitValue }}" required />
                            <span class="compact-inline-addon">hrs/week</span>
                            <span class="compact-inline-total" data-weekly-max>${{ number_format($hourlyRateNumber * $weeklyLimitValue, 2) }} max / week</span>
                        </div>
                    </div>
                </div>


                <div class="form-actions">
                    <a class="link-button" href="{{ route('workspace.dashboard') }}">‹ Back</a>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="button button-secondary" type="submit" name="action" value="save">Save brief</button>
                        <button class="button button-primary" type="submit" name="action" value="continue">Save + continue</button>
                    </div>
                </div>
            </form>
        </section>

        <aside class="hire-summary-card">
            <h2>Current setup</h2>
            <p>Everything needed before billing is now kept in one place.</p>

            <div class="brief-mini">
                <div class="mini-row">
                    <span class="mini-label">Project title</span>
                    <strong>{{ old('title', $project->title ?: 'Not set yet') }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Experience</span>
                    <strong>{{ old('experience_level', $project->experience_level ?: 'Not set yet') }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Timeframe</span>
                    <strong>{{ old('timeframe', $project->timeframe ?: 'Not set yet') }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Specialty</span>
                    <strong>{{ old('specialty', $project->specialty ?: 'Not set yet') }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Freelancer</span>
                    <strong>{{ $selectedFreelancer?->name ?: ($offer?->freelancer_display_name ?? 'Invite freelancer in the form') }}</strong>
                </div>
                <div class="mini-row">
                    <span class="mini-label">Offer summary</span>
                    <strong>${{ number_format($hourlyRateNumber, $hourlyRateNumber == floor($hourlyRateNumber) ? 0 : 2) }}/hr · {{ $weeklyLimitValue }} hrs/week</strong>
                </div>
            </div>

            <div class="separator"></div>

            @if ($selectedFreelancer)
                <div class="selected-freelancer-panel">
                    <div class="avatar-line" style="margin-bottom:14px">
                        <img alt="{{ $selectedFreelancer->name }}" src="{{ $selectedFreelancer->avatar_url }}" />
                        <div>
                            <strong>{{ $selectedFreelancer->name }}</strong>
                            <span>{{ $selectedFreelancer->title ?: 'Freelancer profile' }}</span>
                        </div>
                    </div>
                    <div class="muted small">{{ $selectedFreelancer->display_location ?: 'Available remotely' }}</div>
                    @if ($selectedFreelancer->status === 'active')
                        <div style="margin-top:12px">
                            <a class="cta-link" href="{{ $selectedFreelancer->publicProfileUrl() }}">Open public profile</a>
                        </div>
                    @endif
                </div>
            @else
                <div class="note-panel">
                    <strong>No freelancer selected yet.</strong>
                    <p class="muted small" style="margin:10px 0 0">Select a public freelancer profile with Hire Now, or invite a freelancer directly in the form.</p>
                </div>

                @if ($freelancers->isNotEmpty())
                    <div class="separator"></div>
                    <h3 style="font-size:22px;letter-spacing:-.03em;margin:0 0 14px">Featured freelancers</h3>
                    @foreach ($freelancers as $freelancer)
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
                                <a class="cta-link" href="{{ $freelancer->publicProfileUrl() }}" style="display:inline-block;margin-top:8px">View profile</a>
                            </div>
                        </div>
                        @if (! $loop->last)
                            <div class="separator"></div>
                        @endif
                    @endforeach
                @endif
            @endif

            <div class="separator"></div>

        </aside>
    </div>
</div>
@endsection
