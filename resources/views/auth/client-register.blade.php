@extends('workspace.layouts.base')

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.index') }}">Workspace home</a><span>›</span><span>Create account</span>
    </div>

    @include('workspace.partials.flash')

    <div class="wizard-card compact" style="max-width:820px">
        <div class="wizard-header">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai" />
            <h1 class="wizard-title" style="font-size:42px">Create your client account</h1>
            <p class="wizard-subtitle">Register once, then post projects, invite freelancers, and manage everything from one client dashboard.</p>
        </div>

        <form method="post" action="{{ route('client.register') }}">
            @csrf

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

            <div class="form-actions">
                <a class="link-button" href="{{ route('workspace.index') }}">‹ Back</a>
                <div style="display:flex;gap:12px;align-items:center">
                    <a class="button button-secondary" href="{{ route('client.login') }}">Already have an account?</a>
                    <button class="button button-primary" type="submit">Create workspace</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
