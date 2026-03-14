@extends('workspace.layouts.base', ['activeNav' => 'projects'])

@section('content')
<div class="container">
    <div class="breadcrumbs">
        <a href="{{ route('workspace.dashboard') }}">Dashboard</a><span>›</span><span>Invoice details</span>
    </div>

    @include('workspace.partials.flash')

    <div class="wizard-card compact" style="max-width:920px">
        <div class="wizard-header" style="margin-bottom:14px">
            <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai">
            <h1 class="wizard-title" style="font-size:42px">Invoice details</h1>
            <p class="wizard-subtitle">Save the company, VAT, billing email, and address details that should appear on invoices.</p>
        </div>

        <form method="post" action="{{ route('workspace.invoice-details.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="company_name">Company name</label>
                    <input class="input" id="company_name" name="company_name" type="text" value="{{ old('company_name', $invoiceDetail?->company_name) }}" required />
                </div>
                <div class="form-group">
                    <label class="form-label" for="vat_number">VAT number</label>
                    <input class="input" id="vat_number" name="vat_number" type="text" value="{{ old('vat_number', $invoiceDetail?->vat_number) }}" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="contact_name">Contact name</label>
                    <input class="input" id="contact_name" name="contact_name" type="text" value="{{ old('contact_name', $invoiceDetail?->contact_name) }}" />
                </div>
                <div class="form-group">
                    <label class="form-label" for="billing_email">Billing email</label>
                    <input class="input" id="billing_email" name="billing_email" type="email" value="{{ old('billing_email', $invoiceDetail?->billing_email ?? auth()->user()->email) }}" required />
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address_line_1">Address line 1</label>
                <input class="input" id="address_line_1" name="address_line_1" type="text" value="{{ old('address_line_1', $invoiceDetail?->address_line_1) }}" />
            </div>

            <div class="form-group">
                <label class="form-label" for="address_line_2">Address line 2</label>
                <input class="input" id="address_line_2" name="address_line_2" type="text" value="{{ old('address_line_2', $invoiceDetail?->address_line_2) }}" />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <input class="input" id="city" name="city" type="text" value="{{ old('city', $invoiceDetail?->city) }}" />
                </div>
                <div class="form-group">
                    <label class="form-label" for="postal_code">Postal code</label>
                    <input class="input" id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $invoiceDetail?->postal_code) }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="country">Country</label>
                <input class="input" id="country" name="country" type="text" value="{{ old('country', $invoiceDetail?->country) }}" />
            </div>

            <div class="form-actions">
                <a class="link-button" href="{{ route('workspace.settings') }}">‹ Back</a>
                <button class="button button-primary" type="submit">Save invoice details</button>
            </div>
        </form>
    </div>
</div>
@endsection
