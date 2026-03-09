<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1" name="viewport"/>
<title>Client workspace · HireHelper.ai</title>
<meta content="Signed-in client workspace for HireHelper.ai" name="description"/>
<link href="{{ asset('workspace-assets/css/styles.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="app-shell">
<header class="topbar">
<div class="container topbar-inner">
<a aria-label="HireHelper.ai home" class="brand" href="app/dashboard.html">
<img alt="HireHelper.ai" src="{{ asset('workspace-assets/img/logo.svg') }}"/>
</a>
<nav aria-label="Primary" class="primary-nav">
<a class="active" href="app/dashboard.html">Projects</a>
<a href="app/messages.html">Messages</a>
<a href="app/reports.html">Reports</a>
<a href="app/hire-flow.html">Hire</a>
</nav>
<div class="account-nav">
<button aria-label="Open menu" class="icon-button menu-toggle" data-menu-toggle="" type="button">☰</button>
<button aria-label="Notifications" class="icon-button" type="button">🔔</button>
<button aria-label="Support" class="icon-button" type="button">?</button>
<a class="account-pill" href="app/settings.html">
<img alt="Account avatar" src="{{ asset('workspace-assets/img/avatar-jade.svg') }}"/>
<div class="meta">
<strong>My Account</strong>
<span>Client workspace</span>
</div>
</a>
</div>
</div>
<div class="mobile-menu" data-mobile-menu="">
<a href="app/dashboard.html">Projects</a>
<a href="app/messages.html">Messages</a>
<a href="app/reports.html">Reports</a>
<a href="app/hire-flow.html">Hire</a>
<a href="app/settings.html">Settings</a>
</div>
</header>
<main class="page-main">
<div class="container">
<section class="section-card intro-card">
<div>
<span class="eyebrow"><span class="dot"></span> Client workspace</span>
<h1>Client workspace with a streamlined project setup flow.</h1>
<p>This build focuses on the signed-in client experience. Footer links are removed, extra public pages are removed, and project setup lives on one clean page.</p>
<div class="inline-actions">
<a class="button button-primary" href="app/welcome.html">After registration</a>
<a class="button button-secondary" href="app/dashboard.html">After login</a>
<a class="button button-ghost" href="app/hire-flow.html">Open project setup</a>
</div>
<ul class="checklist">
<li><span class="tick">✓</span><span>Plain HTML, CSS, and JavaScript.</span></li>
<li><span class="tick">✓</span><span>Minimal footer without links.</span></li>
<li><span class="tick">✓</span><span>Single page for project brief.</span></li>
</ul>
</div>
<div>
<img alt="HireHelper.ai dashboard illustration" src="{{ asset('workspace-assets/img/hero.svg') }}"/>
</div>
</section>
<div class="spacer"></div>
<section>
<div class="page-heading">
<div>
<h2>Workspace pages</h2>
<p>Only the essential client-side screens remain.</p>
</div>
</div>
<div class="grid-auto">
<a class="nav-card" href="app/welcome.html"><h3>After registration</h3><p>Welcome screen with the next action to create a project.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/dashboard.html"><h3>Dashboard</h3><p>Clean signed-in dashboard before anything is hired.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/hire-flow.html"><h3>Project setup</h3><p>Write and save the brief on one page.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/invite-offer.html"><h3>Offer setup</h3><p>Set rate, weekly limit, and manual time preferences.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/billing-method.html"><h3>Billing setup</h3><p>Select a payment method before the contract starts.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/dashboard-live.html"><h3>Dashboard with live project</h3><p>State after a brief is saved and an offer is sent.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/project-pending.html"><h3>Pending contract</h3><p>Review terms and settings before activation.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/project-active.html"><h3>Active contract</h3><p>Time, payments, and contract actions.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/messages.html"><h3>Messages</h3><p>Simple project communication layout.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/reports.html"><h3>Reports</h3><p>Hours, billing, and workspace health.</p><span class="cta-link">Open</span></a>
<a class="nav-card" href="app/settings.html"><h3>Settings</h3><p>Notifications, reminders, and workspace preferences.</p><span class="cta-link">Open</span></a>
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