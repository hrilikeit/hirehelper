@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span><span>After registration</span>
    </div>

    @include('workspace.partials.flash')

    <section class="section-card intro-card">
        <div>
            <span class="eyebrow"><span class="dot"></span> Welcome to HireHelper.ai</span>
            <h1>Your client workspace is ready, {{ $user->name }}.</h1>
            <p>You are now inside the client workspace. The next step is to create a project brief so you can move directly into the freelancer hiring flow.</p>
            <ul class="checklist">
                <li><span class="tick">✓</span><span>Save the project scope and requirements in one place.</span></li>
                <li><span class="tick">✓</span><span>Choose a freelancer and send an offer with weekly limits.</span></li>
                <li><span class="tick">✓</span><span>Manage messages, reports, and billing from the same dashboard.</span></li>
            </ul>
            <div class="inline-actions" style="margin-top:24px">
                <a class="button button-primary" href="{{ route('workspace.hire-flow') }}">Create project</a>
                <a class="button button-secondary" href="{{ route('workspace.dashboard') }}">Skip to dashboard</a>
            </div>
        </div>
        <div>
            <img alt="Client onboarding illustration" src="{{ asset('workspace-assets/img/hero.svg') }}" />
        </div>
    </section>

    <div class="spacer"></div>

    <section>
        <div class="grid-3">
            <div class="feature-card">
                <div class="icon-chip">01</div>
                <h3>Start with the brief</h3>
                <p>Capture the essential details once: scope, level, timeframe, and specialty.</p>
            </div>
            <div class="feature-card">
                <div class="icon-chip">02</div>
                <h3>Send the offer</h3>
                <p>Pick a freelancer, define the rate and weekly cap, and move the project into a pending state.</p>
            </div>
            <div class="feature-card">
                <div class="icon-chip">03</div>
                <h3>Run the project</h3>
                <p>Track messages, reports, billing, and active work from the dashboard once the contract starts.</p>
            </div>
        </div>
    </section>
</div>
@endsection
