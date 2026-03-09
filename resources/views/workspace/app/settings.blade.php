@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    @include('workspace.partials.flash')

    <div class="page-heading">
        <div>
            <span class="badge"><span class="dot"></span> Signed in</span>
            <h1>Workspace settings</h1>
            <p>This screen organizes the most common client-side settings after login: notifications, billing reminders, security, and interface preferences.</p>
        </div>
    </div>

    <div class="grid-2">
        <section class="project-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Notifications</h3>

            <form method="post" action="{{ route('workspace.settings.update') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Name</label>
                        <input class="input" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="company">Company</label>
                        <input class="input" id="company" name="company" type="text" value="{{ old('company', $user->company) }}" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input class="input" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input class="input" id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" />
                    </div>
                </div>

                <label class="checkbox-line">
                    <input name="notify_messages" type="checkbox" value="1" {{ old('notify_messages', $user->notify_messages) ? 'checked' : '' }} />
                    <span><strong>Proposal alerts</strong><br /><span class="muted">Send a client notification whenever a new proposal or freelancer response arrives.</span></span>
                </label>

                <label class="checkbox-line" style="margin-top:12px">
                    <input name="notify_reports" type="checkbox" value="1" {{ old('notify_reports', $user->notify_reports) ? 'checked' : '' }} />
                    <span><strong>Billing reminders</strong><br /><span class="muted">Warn the client before contracts pause or payment setup is incomplete.</span></span>
                </label>

                <div class="form-group" style="margin-top:16px">
                    <label class="form-label" for="reminder_frequency">Weekly summaries</label>
                    <select class="select" id="reminder_frequency" name="reminder_frequency">
                        @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('reminder_frequency', $user->reminder_frequency) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions">
                    <span class="muted small">Your settings apply to the client workspace only.</span>
                    <button class="button button-primary" type="submit">Save settings</button>
                </div>
            </form>
        </section>

        <section class="project-card">
            <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Account and security</h3>
            <div class="setting-list">
                <div class="setting-row">
                    <div>
                        <strong>Password and access</strong>
                        <span>Review account credentials and manage workspace security controls.</span>
                    </div>
                    <span class="badge">Laravel auth</span>
                </div>
                <div class="setting-row">
                    <div>
                        <strong>Billing method</strong>
                        <span>{{ $billingMethod?->method_type ?: 'No default billing method saved yet.' }}</span>
                    </div>
                    <a class="cta-link" href="{{ route('workspace.billing-method') }}">Manage</a>
                </div>
                <div class="setting-row">
                    <div>
                        <strong>Workspace status</strong>
                        <span>Use the dashboard to manage projects, offers, and active work.</span>
                    </div>
                    <a class="cta-link" href="{{ route('workspace.dashboard') }}">Open</a>
                </div>
            </div>

            <div class="separator"></div>

            <form method="post" action="{{ route('client.logout') }}">
                @csrf
                <button class="button button-secondary" type="submit">Sign out</button>
            </form>
        </section>
    </div>
</div>
@endsection
