<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Billing method · HireHelper.ai</title>
  <meta name="description" content="Post-login client workspace for HireHelper.ai">
  <link rel="stylesheet" href="{{ asset('workspace-assets/css/styles.css') }}">

</head>
<body>
  <div class="app-shell">

    <header class="topbar">
      <div class="container topbar-inner">
        <a class="brand" href="../app/dashboard.html" aria-label="HireHelper.ai home">
          <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai">
        </a>
        <nav class="primary-nav" aria-label="Primary">
          <a href="../app/dashboard.html" class="active">Projects</a>
<a href="../app/messages.html">Messages</a>
<a href="../app/reports.html">Reports</a>
<a href="../app/hire-flow.html">Hire</a>
        </nav>
        <div class="account-nav">
          <button class="icon-button menu-toggle" type="button" aria-label="Open menu" data-menu-toggle>☰</button>
          <button class="icon-button" type="button" aria-label="Notifications">🔔</button>
          <button class="icon-button" type="button" aria-label="Support">?</button>
          <a class="account-pill" href="../app/settings.html">
            <img src="{{ asset('workspace-assets/img/avatar-jade.svg') }}" alt="Account avatar">
            <div class="meta">
              <strong>My Account</strong>
              <span>Client workspace</span>
            </div>
          </a>
        </div>
      </div>
      <div class="mobile-menu" data-mobile-menu>
        <a href="../app/dashboard.html">Projects</a><a href="../app/messages.html">Messages</a><a href="../app/reports.html">Reports</a><a href="../app/hire-flow.html">Hire</a>
        <a href="../app/settings.html">Settings</a>
      </div>
    </header>

    <main class="page-main">

<div class="container">
  <div class="breadcrumbs">
    <a href="../index.html">Workspace home</a><span>›</span><a href="invite-offer.html">Create offer</a><span>›</span><span>Billing method</span>
  </div>
  <div class="wizard-card compact" style="max-width:760px">
    <div class="wizard-header">
      <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai">
      <h1 class="wizard-title" style="font-size:42px">Add billing method</h1>
      <p class="wizard-subtitle">Choose how the client workspace should handle payments before the contract starts.</p>
    </div>
    <input type="hidden" name="billingMethod" value="Credit or Debit Card">
    <div class="setting-list">
      <button type="button" class="setting-row is-selected" data-billing-choice="Credit or Debit Card">
        <div style="text-align:left">
          <strong>Credit or Debit Card</strong>
          <span>To verify the card, a very small temporary authorization may be used by the payment provider.</span>
        </div>
        <div style="font-size:28px;color:var(--primary)">💳</div>
      </button>
      <button type="button" class="setting-row" data-billing-choice="PayPal">
        <div style="text-align:left">
          <strong>PayPal</strong>
          <span>Use a PayPal account if the client prefers a faster checkout handoff for billing verification.</span>
        </div>
        <div style="font-size:28px;color:var(--primary)">Ⓟ</div>
      </button>
    </div>
    <div class="form-actions">
      <a class="link-button" href="invite-offer.html">‹ Back</a>
      <div style="display:flex;gap:12px">
        <a class="button button-secondary" href="dashboard-live.html">Skip for now</a>
        <a class="button button-primary" href="dashboard-live.html">Add</a>
      </div>
    </div>
  </div>
</div>

    </main>

    <footer class="app-footer app-footer-minimal">
      <div class="container footer-minimal">
        <img src="{{ asset('workspace-assets/img/logo.svg') }}" alt="HireHelper.ai">
        <div>
          <strong>HireHelper.ai</strong>
          <small>Client workspace</small>
        </div>
      </div>
    </footer>

  </div>
  <script src="{{ asset('workspace-assets/js/app.js') }}"></script>
</body>
</html>
