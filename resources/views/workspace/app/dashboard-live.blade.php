<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1" name="viewport"/>
<title>Client dashboard with project · HireHelper.ai</title>
<meta content="Post-login client workspace for HireHelper.ai" name="description"/>
<link href="{{ asset('workspace-assets/css/styles.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="app-shell">
<header class="topbar">
<div class="container topbar-inner">
<a aria-label="HireHelper.ai home" class="brand" href="../app/dashboard.html">
<img alt="HireHelper.ai" src="{{ asset('workspace-assets/img/logo.svg') }}"/>
</a>
<nav aria-label="Primary" class="primary-nav">
<a class="active" href="../app/dashboard.html">Projects</a>
<a href="../app/messages.html">Messages</a>
<a href="../app/reports.html">Reports</a>
<a href="../app/hire-flow.html">Hire</a>
</nav>
<div class="account-nav">
<button aria-label="Open menu" class="icon-button menu-toggle" data-menu-toggle="" type="button">☰</button>
<button aria-label="Notifications" class="icon-button" type="button">🔔</button>
<button aria-label="Support" class="icon-button" type="button">?</button>
<a class="account-pill" href="../app/settings.html">
<img alt="Account avatar" src="{{ asset('workspace-assets/img/avatar-jade.svg') }}"/>
<div class="meta">
<strong>My Account</strong>
<span>Client workspace</span>
</div>
</a>
</div>
</div>
<div class="mobile-menu" data-mobile-menu="">
<a href="../app/dashboard.html">Projects</a><a href="../app/messages.html">Messages</a><a href="../app/reports.html">Reports</a><a href="../app/hire-flow.html">Hire</a>
<a href="../app/settings.html">Settings</a>
</div>
</header>
<main class="page-main">
<div class="container">
<div class="notice-banner" data-dismissible-notice="">
<div style="display:flex;align-items:center;gap:12px"><span class="notice-icon">⚠</span><span><strong>Critical notice:</strong> all contracts are paused until the billing method is verified.</span></div>
<div style="display:flex;align-items:center;gap:18px"><a href="billing-method.html">Verify billing method</a><button class="link-button" data-dismiss-notice="" type="button">Dismiss</button></div>
</div>
<div class="page-heading">
<div>
<span class="badge"><span class="dot"></span> Signed in</span>
<h1>Client dashboard</h1>
<p>This state reflects the dashboard after the client saves a brief and reaches the pending or verification stage.</p>
</div>
<a class="button button-primary" href="hire-flow.html">New project</a>
</div>
<div class="dashboard-grid">
<section class="panel tall">
<div class="float-action"><a class="button button-primary button-small" href="hire-flow.html">New project</a></div>
<h3>Project drafts</h3>
<hr/>
<div class="project-row">
<div>
<div class="project-title" data-brief="title">HireHelper.ai client dashboard rebuild</div>
<div class="project-sub">Latest brief saved from the client-side flow.</div>
</div>
<span class="status-pill status-neutral">Live</span>
</div>
<div class="separator"></div>
<div class="inline-actions">
<a class="cta-link" href="hire-flow.html">Open project setup</a>
<a class="cta-link" href="project-pending.html">Open pending offer</a>
</div>
</section>
<section class="panel tall">
<h3>Projects</h3>
<hr/>
<div class="project-row">
<div style="display:flex;gap:14px">
<span class="project-bullet"></span>
<div>
<div class="project-title" data-brief="title">HireHelper.ai client dashboard rebuild</div>
<div class="project-sub">Freelancer: <span data-invite="freelancer">Ava Petrosyan</span></div>
</div>
</div>
<span class="status-pill status-pending">Pending</span>
</div>
<div class="separator"></div>
<a class="cta-link" href="project-pending.html">Open project terms and settings</a>
</section>
</div>
<div class="dashboard-grid">
<section class="panel">
<h3>My offers</h3>
<hr/>
<div class="offer-row">
<div class="avatar-line">
<img alt="Suggested freelancer" src="{{ asset('workspace-assets/img/avatar-ava.svg') }}"/>
<div>
<strong data-invite="freelancer">Ava Petrosyan</strong>
<span>Armenia</span>
</div>
</div>
<div style="text-align:right">
<div class="muted small">a few seconds ago</div>
<a class="cta-link" href="project-pending.html">View Offer</a>
</div>
</div>
</section>
<section class="panel">
<h3>Co-workers</h3>
<hr/>
<div class="illustration-wrap">
<img alt="Co-workers" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:140px"/>
<div class="muted small" style="margin-top:8px">Invite team members when the project is ready.</div>
</div>
</section>
<section class="panel">
<h3>Activity</h3>
<hr/>
<div class="activity-row">
<div>
<div class="project-title">Offer sent</div>
<div class="project-sub">A new offer was created for <span data-invite="freelancer">Ava Petrosyan</span>.</div>
</div>
<span class="status-pill status-neutral">Now</span>
</div>
</section>
</div>
</div>
</main>
<footer class="app-footer app-footer-minimal">
<div class="container footer-minimal">
<img alt="HireHelper.ai" src="{{ asset('workspace-assets/img/logo.svg') }}"/>
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
