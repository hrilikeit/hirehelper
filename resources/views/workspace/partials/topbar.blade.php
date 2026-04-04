<header class="topbar">
    <div class="container topbar-inner">
        <a class="brand" href="{{ auth()->check() ? route('workspace.dashboard') : route('workspace.index') }}" aria-label="HireHelper.ai home">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai" />
        </a>

        @auth
            <nav class="primary-nav" aria-label="Primary">
                <a href="{{ route('workspace.dashboard') }}" class="{{ ($activeNav ?? '') === 'projects' ? 'active' : '' }}">Projects</a>
                <a href="{{ route('workspace.messages') }}" class="{{ ($activeNav ?? '') === 'messages' ? 'active' : '' }}">Messages</a>
                <a href="{{ route('workspace.reports') }}" class="{{ ($activeNav ?? '') === 'reports' ? 'active' : '' }}">Reports</a>
            </nav>
        @else
            <nav class="primary-nav" aria-label="Primary">
                <a href="{{ route('workspace.index') }}" class="{{ ($activeNav ?? '') === 'workspace' ? 'active' : '' }}">Workspace</a>
                <a href="{{ route('home') }}">Website</a>
                <a href="{{ route('help.index') }}">Help</a>
            </nav>
        @endauth

        <div class="account-nav">
            <button class="icon-button menu-toggle" type="button" aria-label="Open menu" data-menu-toggle>☰</button>
            @auth
                <a class="account-pill" href="{{ route('workspace.settings') }}">
                    <img src="{{ asset('workspace-assets/img/avatar-jade.svg') }}" alt="Account avatar" />
                    <div class="meta">
                        <strong>{{ \Illuminate\Support\Str::limit(auth()->user()->name, 18) }}</strong>
                        <span>{{ auth()->user()->company ?: 'My workspace' }}</span>
                    </div>
                </a>
            @else
                <a class="button button-secondary button-compact" href="/client/login">Login</a>
                <a class="button button-primary button-compact" href="/client/register">Register</a>
            @endauth
        </div>
    </div>

    <div class="mobile-menu" data-mobile-menu>
        @auth
            <a href="{{ route('workspace.dashboard') }}">Projects</a>
            <a href="{{ route('workspace.messages') }}">Messages</a>
            <a href="{{ route('workspace.reports') }}">Reports</a>
            <a href="{{ route('workspace.settings') }}">Settings</a>
        @else
            <a href="{{ route('workspace.index') }}">Workspace</a>
            <a href="{{ route('home') }}">Website</a>
            <a href="{{ route('help.index') }}">Help</a>
            <a href="/client/login">Login</a>
            <a href="/client/register">Register</a>
        @endauth
    </div>
</header>
