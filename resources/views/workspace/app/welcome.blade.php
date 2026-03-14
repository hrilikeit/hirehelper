<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1" name="viewport"/>
<title>After registration · HireHelper.ai</title>
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
<div class="breadcrumbs">
<a href="../index.html">Workspace home</a><span>›</span><span>After registration</span>
</div>
<section class="section-card intro-card">
<div>
<span class="eyebrow"><span class="dot"></span> Welcome to HireHelper.ai</span>
<h1>Your client workspace is ready.</h1>
<p>You are now inside the post-registration experience. The next step is to open one simple project setup page so you can define the work without extra steps.</p>
<ul class="checklist">
<li><span class="tick">✓</span><span>Define the scope clearly so the project is ready to move forward.</span></li>
<li><span class="tick">✓</span><span>Save the project details in one place without extra steps.</span></li>
<li><span class="tick">✓</span><span>Track everything from one client dashboard after login.</span></li>
</ul>
<div class="inline-actions" style="margin-top:24px">
<a class="button button-primary" href="hire-flow.html">Create project</a>
<a class="button button-secondary" href="dashboard.html">Skip to dashboard</a>
</div>
</div>
<div>
<img alt="Client onboarding illustration" src="{{ asset('workspace-assets/img/hero.svg') }}"/>
</div>
</section>
<div class="spacer"></div>
<section>
<div class="grid-3">
<div class="feature-card">
<div class="icon-chip">01</div>
<h3>Start with the brief</h3>
<p>Capture the essential details once: scope, level, timeframe, and specialty.</p>
</div>
<div class="feature-card">
<div class="icon-chip">02</div>
<h3>Save the project</h3>
<p>Use one page to save the brief and keep the project details ready for the next step.</p>
</div>
<div class="feature-card">
<div class="icon-chip">03</div>
<h3>Manage the project</h3>
<p>Everything moves into the dashboard: messages, reports, pending contracts, active work, and billing reminders.</p>
</div>
</div>
</section>
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
