@extends('workspace.layouts.base')

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span><span>Sign in</span>
    </div>

    @include('workspace.partials.flash')

    @php
        $nextUrl = old('next', $next ?? null);
    @endphp

    <div class="wizard-card compact" style="max-width:760px">
        <div class="wizard-header">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai" />
            <h1 class="wizard-title" style="font-size:42px">Sign in to your client workspace</h1>
            <p class="wizard-subtitle">Open your dashboard, continue the combined project and offer page, and manage billing, messages, and live work.</p>
        </div>

        <form method="post" action="/client/login">
            @csrf

            @if ($nextUrl)
                <input type="hidden" name="next" value="{{ $nextUrl }}">
            @endif

            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" required />
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="input" id="password" name="password" type="password" required />
                <a href="{{ route('password.request') }}" style="display:inline-block;margin-top:8px;font-size:14px;color:#6366f1;text-decoration:none">Forgot your password?</a>
            </div>

            <label class="checkbox-line" style="margin:20px 0">
                <input name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }} />
                <span><strong>Remember this device</strong><br /><span class="muted">Stay signed in on this browser.</span></span>
            </label>

            <div class="form-actions">
                <a class="link-button" href="{{ route('workspace.index') }}">‹ Back</a>
                <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                    <a class="button button-secondary" href="{{ $nextUrl ? '/client/register?next=' . urlencode($nextUrl) : '/client/register' }}">Create account</a>
                    <button class="button button-primary" type="submit">Sign in</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
