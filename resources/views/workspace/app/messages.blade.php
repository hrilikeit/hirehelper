<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Messages · HireHelper.ai</title>
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
          <a href="../app/dashboard.html">Projects</a>
<a href="../app/messages.html" class="active">Messages</a>
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
        <h1>Messages</h1>
        <p>The signed-in client inbox keeps all project communication, status updates, and hiring actions in one quieter workspace.</p>
      </div>

    </div>

  <div class="split-layout">
    <section class="project-card">
      <div class="avatar-line" style="margin-bottom:18px">
        <img src="{{ asset('workspace-assets/img/avatar-ava.svg') }}" alt="Freelancer avatar">
        <div>
          <strong data-invite="freelancer">Ava Petrosyan</strong>
          <span data-invite="role">Full stack developer</span>
        </div>
      </div>
      <div class="separator"></div>
      <div class="chat-card" style="background:#f8fbff;border-style:dashed">
        <strong>Workspace note</strong>
        <p class="muted" style="margin:8px 0 0">This page is ready for the post-login client message flow. Once an offer is accepted, the conversation thread lives here alongside files, clarifications, and weekly check-ins.</p>
      </div>
      <div style="display:grid;gap:16px;margin-top:18px">
        <div class="setting-row" style="align-items:flex-start">
          <div>
            <strong>Client</strong>
            <span>Welcome to the project room. I want to keep the dashboard clean, modern, and easy to scan for new users.</span>
          </div>
          <span class="badge">Today</span>
        </div>
        <div class="setting-row" style="align-items:flex-start">
          <div>
            <strong>Freelancer</strong>
            <span>Understood. I will start by reviewing the dashboard hierarchy, the post-job flow, and the billing screens so the client side feels consistent.</span>
          </div>
          <span class="badge">Today</span>
        </div>
        <div class="setting-row" style="align-items:flex-start">
          <div>
            <strong>System</strong>
            <span>Billing verification is recommended before the contract goes live.</span>
          </div>
          <span class="badge">Alert</span>
        </div>
      </div>
    </section>

    <aside class="sidebar-card">
      <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 16px">Project snapshot</h3>
      <div class="setting-list">
        <div class="setting-row">
          <div>
            <strong data-brief="title">HireHelper.ai client dashboard rebuild</strong>
            <span>Current specialty: <span data-brief="specialty">Full stack development</span></span>
          </div>
        </div>
        <div class="setting-row">
          <div>
            <strong>Timeframe</strong>
            <span data-brief="timeframe">Less than 1 month</span>
          </div>
        </div>
        <div class="setting-row">
          <div>
            <strong>Rate</strong>
            <span data-invite="rate">$35 / hr</span>
          </div>
        </div>
      </div>
      <div class="inline-actions" style="margin-top:20px">
        <a class="button button-primary" href="project-active.html">Open contract</a>
        <a class="button button-secondary" href="reports.html">View reports</a>
      </div>
    </aside>
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
