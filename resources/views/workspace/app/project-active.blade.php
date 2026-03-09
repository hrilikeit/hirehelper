<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Active contract · HireHelper.ai</title>
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
    <div style="display:flex;align-items:center;gap:12px"><span class="notice-icon">⚠</span><span><strong>Critical notice:</strong> billing should be verified to keep contract payments running smoothly.</span></div>
    <div style="display:flex;align-items:center;gap:18px"><a href="billing-method.html">Verify billing method</a><button class="link-button" type="button" data-dismiss-notice>Dismiss</button></div>
  </div>

  <div class="breadcrumbs">
    <a href="dashboard-live.html">Dashboard</a><span>›</span><span>Project</span>
  </div>

  <div class="dual-col">
    <section class="project-card">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap">
        <div>
          <h1 style="font-size:48px;letter-spacing:-.05em;margin:0 0 20px" data-brief="title">HireHelper.ai client dashboard rebuild</h1>
          <div class="inline-actions">
            <span class="badge">Time & payments</span>
            <a class="cta-link" href="project-pending.html">Terms & settings</a>
          </div>
        </div>
        <a class="cta-link" href="#">Pay bonus</a>
      </div>

      <div class="separator"></div>
      <div class="timesheet-grid">
        <div class="timesheet-stat"><small>This week</small><strong>00:00 hrs</strong><div class="muted small">of <span data-invite="weekly">20 hrs / week</span> limit</div></div>
        <div class="timesheet-stat"><small>Last week</small><strong>00:00 hrs</strong><div class="muted small">$0.00</div></div>
        <div class="timesheet-stat"><small>Since start</small><strong>00:00 hrs</strong><div class="muted small">$0.00</div></div>
      </div>

      <div class="separator"></div>
      <div style="display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap">
        <h2 style="font-size:30px;letter-spacing:-.04em;margin:0">Timesheet this week</h2>
        <div class="muted small">Amount: $0.00</div>
      </div>
      <div class="day-strip" style="margin-top:22px">
        <div class="day-box"><div class="day">Sun</div><div class="hours">00:00</div></div>
        <div class="day-box"><div class="day">Mon</div><div class="hours">00:00</div></div>
        <div class="day-box"><div class="day">Tue</div><div class="hours">00:00</div></div>
        <div class="day-box"><div class="day">Wed</div><div class="hours">00:00</div></div>
        <div class="day-box"><div class="day">Thu</div><div class="hours">00:00</div></div>
        <div class="day-box"><div class="day">Fri</div><div class="hours">00:00</div></div>
        <div class="day-box"><div class="day">Sat</div><div class="hours">00:00</div></div>
      </div>

      <div class="separator"></div>
      <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <h2 style="font-size:30px;letter-spacing:-.04em;margin:0">All timesheets and earnings</h2>
        <span class="badge">Last 30 days</span>
      </div>
      <table class="table" style="margin-top:14px">
        <thead><tr><th>Date</th><th>Description</th><th>Status</th><th>Amount</th><th>Invoice</th></tr></thead>
        <tbody><tr><td colspan="5" class="muted">No transaction meets your selected criteria yet.</td></tr></tbody>
      </table>
    </section>

    <aside class="sidebar-card side-profile">
      <img src="{{ asset('workspace-assets/img/avatar-ava.svg') }}" alt="Freelancer">
      <h3 data-invite="freelancer">Ava Petrosyan</h3>
      <div class="place">Armenia</div>
      <a class="cta-link" href="messages.html">Send a message</a>
      <div class="status-block">
        <div>Start date: <strong data-now>Today</strong></div>
        <div>Contract status: <span class="status-pill status-active">Active</span></div>
      </div>
      <div class="action-inline">
        <span>Pause contract</span>
        <span class="toggle"></span>
      </div>
      <a class="button button-primary" href="#">Pay</a>
      <a class="button button-secondary" href="#">End Contract</a>
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
