@extends('workspace.layouts.base')

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span>
        <span>Set new password</span>
    </div>

    @include('workspace.partials.flash')

    <div class="wizard-card compact" style="max-width:760px">
        <div class="wizard-header">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai" />
            <h1 class="wizard-title" style="font-size:42px">Set a new password</h1>
            <p class="wizard-subtitle">Choose a strong password with at least 8 characters.</p>
        </div>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <input class="input" id="email" name="email" type="email" value="{{ old('email', $email) }}" required />
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">New password</label>
                <input class="input" id="password" name="password" type="password" required autofocus />
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm new password</label>
                <input class="input" id="password_confirmation" name="password_confirmation" type="password" required />
            </div>

            <div class="form-actions">
                <a class="link-button" href="{{ route('client.login') }}">‹ Back to sign in</a>
                <button class="button button-primary" type="submit">Reset password</button>
            </div>
        </form>
    </div>
</div>
@endsection
