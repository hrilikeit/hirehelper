@extends('workspace.layouts.base')

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span><span>Create account</span>
    </div>

    @include('workspace.partials.flash')

    @php
        $nextUrl = old('next', $next ?? null);
    @endphp

    <div class="wizard-card compact" style="max-width:820px">
        <div class="wizard-header">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai" />
            <h1 class="wizard-title" style="font-size:42px">Create your client account</h1>
            <p class="wizard-subtitle">Register once, then post projects, choose the freelancer, and send an offer from one combined client page.</p>
        </div>

        <form method="post" action="/client/register">
            @csrf

            @if ($nextUrl)
                <input type="hidden" name="next" value="{{ $nextUrl }}">
            @endif

            <div class="form-group">
                <label class="form-label" for="name">Full name</label>
                <input class="input" id="name" name="name" type="text" value="{{ old('name') }}" required />
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" required />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="input" id="password" name="password" type="password" required />
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input class="input" id="password_confirmation" name="password_confirmation" type="password" required />
                </div>
            </div>

            {{-- Honeypot: hidden from humans, bots fill it --}}
            <div style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="" />
            </div>

            {{-- Timestamp to detect instant bot submissions --}}
            <input type="hidden" name="_form_loaded_at" value="{{ now()->timestamp }}" />

            <label class="checkbox-line" style="margin-top:4px;margin-bottom:0">
                <input name="agree_terms" type="checkbox" value="1" {{ old('agree_terms') ? 'checked' : '' }} required />
                <span>I Agree to the <a href="https://hirehelper.ai/terms.html" target="_blank" style="color:#2563eb;text-decoration:underline;font-weight:600">Terms of Service</a> and <a href="https://hirehelper.ai/privacy.html" target="_blank" style="color:#2563eb;text-decoration:underline;font-weight:600">Privacy Policy</a></span>
            </label>
            @error('agree_terms')
                <p style="color:#dc2626;font-size:12px;margin:4px 0 0">{{ $message }}</p>
            @enderror

            <div class="form-actions">
                <a class="link-button" href="{{ route('workspace.index') }}">‹ Back</a>
                <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
                    <a class="button button-secondary" href="{{ $nextUrl ? '/client/login?next=' . urlencode($nextUrl) : '/client/login' }}">Already have an account?</a>
                    <button class="button button-primary" type="submit">Create workspace</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
