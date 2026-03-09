<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Settings · HireHelper.ai</title>
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

    <div class="page-heading">
      <div>
        <span class="badge"><span class="dot"></span> Signed in</span>
        <h1>Workspace settings</h1>
        <p>This screen organizes the most common client-side settings after login: notifications, billing reminders, security, and interface preferences.</p>
      </div>

    </div>

  <div class="grid-2">
    <section class="project-card">
      <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Notifications</h3>
      <div class="setting-list">
        <div class="setting-row">
          <div>
            <strong>Proposal alerts</strong>
            <span>Send a client notification whenever a new proposal or freelancer response arrives.</span>
          </div>
          <span class="toggle on"></span>
        </div>
        <div class="setting-row">
          <div>
            <strong>Billing reminders</strong>
            <span>Warn the client before contracts pause or payment setup is incomplete.</span>
          </div>
          <span class="toggle on"></span>
        </div>
        <div class="setting-row">
          <div>
            <strong>Weekly summaries</strong>
            <span>Deliver a short digest of hours, spend, and contract changes to the client inbox.</span>
          </div>
          <span class="toggle"></span>
        </div>
      </div>
    </section>
    <section class="project-card">
      <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Account and security</h3>
      <div class="setting-list">
        <div class="setting-row">
          <div>
            <strong>Password and access</strong>
            <span>Review account credentials and manage workspace security controls.</span>
          </div>
          <a class="cta-link" href="#">Update</a>
        </div>
        <div class="setting-row">
          <div>
            <strong>Billing method</strong>
            <span>Manage card or PayPal connection for the signed-in client workspace.</span>
          </div>
          <a class="cta-link" href="billing-method.html">Manage</a>
        </div>
        <div class="setting-row">
          <div>
            <strong>Workspace reset</strong>
            <span>Clear saved project data and return to a clean workspace state.</span>
          </div>
          <a class="cta-link" href="dashboard.html" data-reset-workspace>Reset</a>
        </div>
      </div>
    </section>
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
