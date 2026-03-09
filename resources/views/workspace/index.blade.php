@extends('workspace.layouts.base')

@section('content')
<div class="container">
    <div class="page-heading">
        <div>
            <span class="badge"><span class="dot"></span> Client workspace</span>
            <h1>Client workspace with a streamlined project setup flow.</h1>
            <p>This workspace now runs inside Laravel. Clients can register, sign in, save project briefs, send offers to freelancers, and manage everything from one dashboard.</p>
        </div>
    </div>

    @include('workspace.partials.flash')

    <section class="section-card intro-card">
        <div>
            <span class="eyebrow"><span class="dot"></span> HireHelper.ai workspace</span>
            <h2>From registration to hiring in one clean flow.</h2>
            <p>Use this workspace to create a client account, post a project brief, choose a freelancer, and move a contract into the active state.</p>
            <div class="inline-actions">
                @auth
                    <a class="button button-primary" href="{{ route('workspace.dashboard') }}">Open dashboard</a>
                    <a class="button button-secondary" href="{{ route('workspace.hire-flow') }}">Create project</a>
                @else
                    <a class="button button-primary" href="{{ route('client.register') }}">Create account</a>
                    <a class="button button-secondary" href="{{ route('client.login') }}">Sign in</a>
                @endauth
                <a class="button button-ghost" href="{{ route('hire.start') }}">Start Hiring from public site</a>
            </div>
            <ul class="checklist">
                <li><span class="tick">✓</span><span>Client registration and login.</span></li>
                <li><span class="tick">✓</span><span>Database-backed project brief and hiring flow.</span></li>
                <li><span class="tick">✓</span><span>Admin management for clients, freelancers, projects, and offers.</span></li>
            </ul>
        </div>
        <div>
            <img alt="HireHelper.ai dashboard illustration" src="{{ asset('workspace-assets/img/hero.svg') }}" />
        </div>
    </section>

    <div class="spacer"></div>

    <section>
        <div class="page-heading">
            <div>
                <h2>Workspace pages</h2>
                <p>These pages are now connected to Laravel routes and real database records.</p>
            </div>
        </div>

        <div class="grid-auto">
            @php
                $cards = [
                    ['title' => 'Registration', 'text' => 'Create the client account that unlocks the workspace.', 'url' => route('client.register')],
                    ['title' => 'Dashboard', 'text' => 'See project drafts, live offers, and active work.', 'url' => auth()->check() ? route('workspace.dashboard') : route('client.login')],
                    ['title' => 'Project setup', 'text' => 'Write and save the project brief on one page.', 'url' => auth()->check() ? route('workspace.hire-flow') : route('client.login')],
                    ['title' => 'Offer setup', 'text' => 'Choose a freelancer and set rate, hours, and manual time.', 'url' => auth()->check() ? route('workspace.invite-offer') : route('client.login')],
                    ['title' => 'Billing setup', 'text' => 'Choose the billing method before activation.', 'url' => auth()->check() ? route('workspace.billing-method') : route('client.login')],
                    ['title' => 'Messages', 'text' => 'Keep project communication inside the workspace.', 'url' => auth()->check() ? route('workspace.messages') : route('client.login')],
                ];
            @endphp

            @foreach ($cards as $card)
                <a class="nav-card" href="{{ $card['url'] }}">
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['text'] }}</p>
                    <span class="cta-link">Open</span>
                </a>
            @endforeach
        </div>
    </section>

    <div class="spacer"></div>

    <section>
        <div class="page-heading">
            <div>
                <h2>Featured freelancers</h2>
                <p>The hiring flow uses these seeded freelancer profiles so the offer pages work immediately.</p>
            </div>
        </div>

        <div class="grid-2">
            @forelse ($featuredFreelancers as $freelancer)
                <div class="project-card">
                    <div class="avatar-line" style="margin-bottom:16px">
                        <img src="{{ $freelancer->avatar_url }}" alt="{{ $freelancer->name }}">
                        <div>
                            <strong>{{ $freelancer->name }}</strong>
                            <span>{{ $freelancer->title }}</span>
                        </div>
                    </div>
                    <p class="muted">{{ $freelancer->overview }}</p>
                    <div class="inline-actions" style="margin-top:18px">
                        <span class="badge">{{ $freelancer->location }}</span>
                        <span class="badge">${{ number_format((float) $freelancer->hourly_rate, 0) }}/hr</span>
                    </div>
                </div>
            @empty
                <div class="project-card">
                    <p class="empty">No freelancers are available yet. The workspace will auto-seed demo freelancers on first use.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
