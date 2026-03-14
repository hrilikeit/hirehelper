<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reports · HireHelper.ai</title>
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
<a href="../app/messages.html">Messages</a>
<a href="../app/reports.html" class="active">Reports</a>
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
        <h1>Reports</h1>
        <p>The client reporting area summarizes spend, hours, pending offers, and workspace health after registration and login.</p>
      </div>

    </div>


  <div class="report-grid">
    <div class="report-card"><small>This week spend</small><strong>$0.00</strong><div class="muted small">No billed hours yet</div></div>
    <div class="report-card"><small>Active contracts</small><strong>1</strong><div class="muted small">Ready for time tracking</div></div>
    <div class="report-card"><small>Pending offers</small><strong>1</strong><div class="muted small">Awaiting next action</div></div>
    <div class="report-card"><small>Billing status</small><strong>Action</strong><div class="muted small">Verify billing to remove warnings</div></div>
  </div>

  <div class="spacer"></div>

  <div class="grid-2">
    <section class="project-card chart-card">
      <h3 style="font-size:30px;letter-spacing:-.04em;margin:0">Hours trend</h3>
      <p class="muted">The chart presents workspace hours and invoice totals in a calm, minimal layout.</p>
      <div class="bar-chart">
        <div class="bar" style="height:24px" data-label="Mon"></div>
        <div class="bar" style="height:24px" data-label="Tue"></div>
        <div class="bar" style="height:24px" data-label="Wed"></div>
        <div class="bar" style="height:24px" data-label="Thu"></div>
        <div class="bar" style="height:24px" data-label="Fri"></div>
        <div class="bar" style="height:24px" data-label="Sat"></div>
      </div>
    </section>

    <section class="project-card">
      <h3 style="font-size:30px;letter-spacing:-.04em;margin:0 0 14px">Billing reminders</h3>
      <div class="setting-list">
        <div class="setting-row">
          <div>
            <strong>Verify billing method</strong>
            <span>Remove pause warnings and keep contracts uninterrupted.</span>
          </div>
          <a class="cta-link" href="billing-method.html">Open</a>
        </div>
        <div class="setting-row">
          <div>
            <strong>Review weekly caps</strong>
            <span>Make sure each freelancer has the right weekly hour limit before activation.</span>
          </div>
          <a class="cta-link" href="invite-offer.html">Edit</a>
        </div>
        <div class="setting-row">
          <div>
            <strong>Client dashboard check</strong>
            <span>Make sure the project list, offers, and activity feed stay aligned with the new UI.</span>
          </div>
          <a class="cta-link" href="dashboard-live.html">Review</a>
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
