<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pending contract · HireHelper.ai</title>
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
  <div class="notice-banner" data-dismissible-notice>
    <div style="display:flex;align-items:center;gap:12px"><span class="notice-icon">⚠</span><span><strong>Critical notice:</strong> all contracts are paused until the billing method is verified.</span></div>
    <div style="display:flex;align-items:center;gap:18px"><a href="billing-method.html">Verify billing method</a><button class="link-button" type="button" data-dismiss-notice>Dismiss</button></div>
  </div>

  <div class="breadcrumbs">
    <a href="dashboard-live.html">Dashboard</a><span>›</span><span>Project</span>
  </div>

  <div class="dual-col">
    <section class="project-card">
      <h1 style="font-size:48px;letter-spacing:-.05em;margin:0 0 20px" data-brief="title">HireHelper.ai client dashboard rebuild</h1>
      <div class="inline-actions" style="margin-bottom:18px">
        <span class="badge">Terms & settings</span>
      </div>
      <div class="separator"></div>
      <h2 style="font-size:30px;letter-spacing:-.04em;margin:0 0 18px">Rate and limits</h2>
      <div class="data-list">
        <div class="data-item"><small>Hourly rate</small><strong data-invite="rate">$35 / hr</strong></div>
        <div class="data-item"><small>Weekly limit</small><strong data-invite="weekly">20 hrs / week</strong></div>
        <div class="data-item"><small>Manual time</small><strong>Allowed</strong></div>
        <div class="data-item"><small>Started by</small><strong>Client workspace</strong></div>
        <div class="data-item"><small>Status</small><strong>Pending</strong></div>
        <div class="data-item"><small>Offer created</small><strong data-now>Today</strong></div>
      </div>

      <div class="separator"></div>
      <h2 style="font-size:30px;letter-spacing:-.04em;margin:0 0 12px">Work description</h2>
      <p class="muted" data-brief="description">Design and implement the signed-in client experience for HireHelper.ai, including dashboard UX, project brief setup, billing setup, and contract management.</p>

      <div class="separator"></div>
      <div class="inline-actions">
        <a class="button button-secondary" href="invite-offer.html">Modify offer</a>
        <a class="button button-primary" href="project-active.html">Open active contract</a>
      </div>
    </section>

    <aside class="sidebar-card side-profile">
      <img src="{{ asset('workspace-assets/img/avatar-ava.svg') }}" alt="Freelancer">
      <h3 data-invite="freelancer">Ava Petrosyan</h3>
      <div class="place">Armenia</div>
      <a class="cta-link" href="messages.html">Send a message</a>
      <div class="status-block">
        <div>Offer date: <strong data-now>Today</strong></div>
        <div>Contract status: <span class="status-pill status-pending">Pending</span></div>
      </div>
      <a class="button button-primary" href="invite-offer.html">Modify Offer</a>
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
