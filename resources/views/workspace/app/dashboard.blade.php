<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1" name="viewport"/>
<title>Client dashboard · HireHelper.ai</title>
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
<div class="page-heading">
<div>
<span class="badge"><span class="dot"></span> Signed in</span>
<h1>Client dashboard</h1>
<p>This is the signed-in dashboard state. It gives clients one place to manage project drafts, live projects, co-workers, and recent activity after login.</p>
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
<div class="project-sub">Draft brief saved in the project setup page. Open it any time to update the scope.</div>
</div>
<span class="status-pill status-neutral">Draft</span>
</div>
<div class="separator"></div>
<p class="muted">Open the project setup page to complete or update the brief.</p>
<div style="margin-top:18px"><a class="cta-link" href="hire-flow.html">Open project setup</a></div>
</section>
<section class="panel tall">
<h3>Projects</h3>
<hr/>
<p class="empty">You do not have an active contract yet. Once an offer is sent and accepted, the project will appear here.</p>
<div class="illustration-wrap">
<img alt="Projects illustration" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:180px"/>
</div>
</section>
</div>
<div class="dashboard-grid">
<section class="panel">
<h3>My offers</h3>
<hr/>
<p class="empty">Offers will appear here after you invite a freelancer.</p>
</section>
<section class="panel">
<h3>Co-workers</h3>
<hr/>
<div class="illustration-wrap">
<img alt="Co-workers illustration" src="{{ asset('workspace-assets/img/megaphone.svg') }}" style="width:140px"/>
<div class="muted small" style="margin-top:8px">Shared workspace for internal collaborators.</div>
</div>
</section>
<section class="panel">
<h3>Activity</h3>
<hr/>
<p class="empty">Workspace notifications, proposal updates, and payment reminders will appear here.</p>
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
