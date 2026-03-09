@extends('workspace.layouts.base', ['activeNav' => 'hire'])

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span>
        <a href="{{ route('workspace.hire-flow', ['project' => $project->id]) }}">Project setup</a><span>›</span>
        <span>Create offer</span>
    </div>

    @include('workspace.partials.flash')

    <div class="wizard-card compact" style="padding-top:30px">
        <div class="wizard-header" style="margin-bottom:8px">
            <img alt="HireHelper.ai" src="{{ asset('workspace-assets/img/logo.svg') }}" />
            <h1 class="wizard-title" style="font-size:42px">Create an offer</h1>
            <p class="wizard-subtitle">Set the hourly rate, weekly cap, and manual time preferences, then continue to billing.</p>
        </div>

        <form method="post" action="{{ route('workspace.invite-offer.store') }}" data-invite-form>
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}" />

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="freelancer_id">Freelancer</label>
                    <select class="select" id="freelancer_id" name="freelancer_id" data-freelancer-select required>
                        @foreach ($freelancers as $freelancer)
                            <option
                                value="{{ $freelancer->id }}"
                                data-role="{{ $freelancer->title }}"
                                data-rate="{{ number_format((float) $freelancer->hourly_rate, 0, '.', '') }}"
                                @selected((int) $selectedFreelancerId === (int) $freelancer->id)
                            >{{ $freelancer->name }} · {{ $freelancer->title }} · ${{ number_format((float) $freelancer->hourly_rate, 0) }}/hr</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <input class="input" id="role" name="role" required value="{{ old('role', $offer?->role ?? optional($freelancers->firstWhere('id', $selectedFreelancerId))->title ?? 'Full stack developer') }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Project</label>
                <div class="input" style="display:flex;align-items:center;height:auto;min-height:56px">{{ $project->title }}</div>
            </div>

            <h2 style="font-size:32px;letter-spacing:-.04em;margin:26px 0 16px;text-align:left">Rate and weekly limit</h2>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="hourly_rate">Rate</label>
                    <div style="display:grid;grid-template-columns:1fr 78px">
                        <input class="input" id="hourly_rate" min="1" name="hourly_rate" required step="1" type="number" value="{{ old('hourly_rate', $offer?->hourly_rate ?? optional($freelancers->firstWhere('id', $selectedFreelancerId))->hourly_rate ?? 35) }}" />
                        <div class="input" style="border-left:none;border-radius:0 16px 16px 0;display:grid;place-items:center;font-weight:700;background:#f3f6ff">$ / hr</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="weekly_limit">Weekly limit</label>
                    <div style="display:grid;grid-template-columns:1fr 110px 170px">
                        <input class="input" id="weekly_limit" min="1" name="weekly_limit" required step="1" type="number" value="{{ old('weekly_limit', $offer?->weekly_limit ?? 20) }}" />
                        <div class="input" style="border-left:none;border-radius:0;display:grid;place-items:center;font-weight:700;background:#f3f6ff">hrs/week</div>
                        <div class="input" data-weekly-max style="border-left:none;border-radius:0 16px 16px 0;display:grid;place-items:center;background:#fbfcff">${{ number_format((float) old('hourly_rate', $offer?->hourly_rate ?? optional($freelancers->firstWhere('id', $selectedFreelancerId))->hourly_rate ?? 35) * (int) old('weekly_limit', $offer?->weekly_limit ?? 20), 2) }} max / week</div>
                    </div>
                </div>
            </div>

            <div class="checkbox-grid">
                <label class="checkbox-line">
                    <input name="manual_time" type="checkbox" value="1" {{ old('manual_time', $offer?->manual_time ?? true) ? 'checked' : '' }} />
                    <span><strong>Allow manual time</strong><br /><span class="muted">Let the freelancer log time manually if you want to support non-tracker work.</span></span>
                </label>

                <label class="checkbox-line">
                    <input name="multi_offer" type="checkbox" value="1" {{ old('multi_offer', $offer?->multi_offer ?? false) ? 'checked' : '' }} />
                    <span><strong>Send offers to more freelancers for this project</strong><br /><span class="muted">Keep this off if you only want to proceed with a single specialist.</span></span>
                </label>
            </div>

            <div class="form-actions">
                <a class="link-button" href="{{ route('workspace.hire-flow', ['project' => $project->id]) }}">‹ Back</a>
                <button class="button button-primary" type="submit">Continue</button>
            </div>
        </form>
    </div>
</div>
@endsection
