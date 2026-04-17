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

                <div class="form-group" style="position:relative">
                    <label class="form-label" for="freelancer_search">Freelancer name or email</label>
                    <input class="input" id="freelancer_search" type="text" autocomplete="off"
                           placeholder="Type freelancer name or email..."
                           value="{{ $selectedFreelancer ? $selectedFreelancer->name . ' (' . ($selectedFreelancer->contact_email ?? '') . ')' : '' }}" />
                    <input type="hidden" name="freelancer_email" id="freelancer_email" value="{{ $freelancerEmailValue }}" />
                    <input type="hidden" name="selected_freelancer_id" id="selected_freelancer_id_field" value="{{ $selectedFreelancerId ?? '' }}" />
                    <div id="freelancer-dropdown" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:50;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 12px 32px rgba(31,43,82,.12);max-height:280px;overflow-y:auto;margin-top:4px"></div>
                </div>

                @if ($selectedFreelancer)
                    <div class="selected-freelancer-card selected-freelancer-card-compact" id="selected-freelancer-preview">
                        <div class="selected-freelancer-main">
                            <img alt="{{ $selectedFreelancer->name }}" class="selected-freelancer-avatar" src="{{ $selectedFreelancer->avatar_url }}" />
                            <div class="selected-freelancer-text">
                                <strong>{{ $selectedFreelancer->name }}</strong>
                                <span>{{ $selectedFreelancer->title ?: 'Freelancer profile' }}</span>
                                <div class="selected-freelancer-meta-row">
                                    <span class="badge">Selected</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="clearFreelancer()" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:13px;font-weight:600">Remove</button>
                    </div>
                @else
                    <div class="selected-freelancer-card selected-freelancer-card-compact" id="selected-freelancer-preview" style="display:none">
                        <div class="selected-freelancer-main">
                            <img alt="" class="selected-freelancer-avatar" id="preview-avatar" src="" />
                            <div class="selected-freelancer-text">
                                <strong id="preview-name"></strong>
                                <span id="preview-title"></span>
                                <div class="selected-freelancer-meta-row">
                                    <span class="badge">Selected</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="clearFreelancer()" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:13px;font-weight:600">Remove</button>
                    </div>
                @endif

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

@push('scripts')
<script>
(function() {
    const searchInput = document.getElementById('freelancer_search');
    const dropdown = document.getElementById('freelancer-dropdown');
    const emailField = document.getElementById('freelancer_email');
    const idField = document.getElementById('selected_freelancer_id_field');
    const preview = document.getElementById('selected-freelancer-preview');
    let debounceTimer = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();

        if (q.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(function() {
            fetch('{{ route("workspace.freelancers.search") }}?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(function(results) {
                    if (results.length === 0) {
                        dropdown.innerHTML = '<div style="padding:14px 18px;color:#6b7280;font-size:14px">No freelancers found. Type an email to invite a new one.</div>';
                        dropdown.style.display = 'block';
                        // If input looks like an email, set it directly
                        if (q.includes('@')) {
                            emailField.value = q;
                            idField.value = '';
                        }
                        return;
                    }

                    dropdown.innerHTML = results.map(function(f) {
                        return '<div class="fl-search-item" data-id="' + f.id + '" data-email="' + (f.email || '') + '" data-name="' + f.name + '" data-title="' + (f.title || '') + '" data-avatar="' + (f.avatar_url || '') + '" data-rate="' + (f.hourly_rate || '') + '" style="display:flex;align-items:center;gap:12px;padding:12px 18px;cursor:pointer;transition:background .12s ease">' +
                            '<img src="' + (f.avatar_url || '') + '" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid #e5e7eb" />' +
                            '<div style="flex:1;min-width:0">' +
                                '<strong style="display:block;font-size:14px">' + f.name + '</strong>' +
                                '<span style="display:block;font-size:12px;color:#6b7280">' + (f.email || f.title || '') + '</span>' +
                            '</div>' +
                            '<span style="font-size:13px;color:#6b7280;white-space:nowrap">$' + parseFloat(f.hourly_rate || 0).toFixed(0) + '/hr</span>' +
                        '</div>';
                    }).join('');
                    dropdown.style.display = 'block';

                    dropdown.querySelectorAll('.fl-search-item').forEach(function(item) {
                        item.addEventListener('mouseenter', function() { this.style.background = '#f4f7ff'; });
                        item.addEventListener('mouseleave', function() { this.style.background = ''; });
                        item.addEventListener('click', function() {
                            selectFreelancer(
                                this.dataset.id,
                                this.dataset.email,
                                this.dataset.name,
                                this.dataset.title,
                                this.dataset.avatar,
                                this.dataset.rate
                            );
                        });
                    });
                });
        }, 300);
    });

    searchInput.addEventListener('blur', function() {
        setTimeout(function() { dropdown.style.display = 'none'; }, 200);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length >= 2) {
            this.dispatchEvent(new Event('input'));
        }
    });

    window.selectFreelancer = function(id, email, name, title, avatarUrl, rate) {
        idField.value = id;
        emailField.value = email || '';
        searchInput.value = name + (email ? ' (' + email + ')' : '');
        dropdown.style.display = 'none';

        // Update preview
        var previewName = document.getElementById('preview-name');
        var previewTitle = document.getElementById('preview-title');
        var previewAvatar = document.getElementById('preview-avatar');

        if (previewName) previewName.textContent = name;
        if (previewTitle) previewTitle.textContent = title || 'Freelancer';
        if (previewAvatar) {
            previewAvatar.src = avatarUrl || '';
            previewAvatar.alt = name;
        }
        preview.style.display = '';

        // Update rate if provided
        if (rate && parseFloat(rate) > 0) {
            var rateField = document.getElementById('hourly_rate');
            if (rateField) rateField.value = parseFloat(rate);
        }
    };

    window.clearFreelancer = function() {
        idField.value = '';
        emailField.value = '';
        searchInput.value = '';
        preview.style.display = 'none';
    };
})();
</script>
@endpush
