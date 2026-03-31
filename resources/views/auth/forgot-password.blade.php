@extends('workspace.layouts.base')

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span>
        <a href="{{ route('client.login') }}">Sign in</a><span>›</span>
        <span>Forgot password</span>
    </div>

    @include('workspace.partials.flash')

    <div class="wizard-card compact" style="max-width:760px">
        <div class="wizard-header">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai" />
            <h1 class="wizard-title" style="font-size:42px">Reset your password</h1>
            <p class="wizard-subtitle">Enter the email address you used to create your account and we will send you a link to reset your password.</p>
        </div>

        <form method="post" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email address</label>
                <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus />
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <a class="link-button" href="{{ route('client.login') }}">‹ Back to sign in</a>
                <button class="button button-primary" type="submit">Send reset link</button>
            </div>
        </form>
    </div>
</div>
@endsection
