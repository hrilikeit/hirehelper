<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1" name="viewport"/>
<title>Create offer · HireHelper.ai</title>
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
<a href="../app/dashboard.html">Projects</a>
<a href="../app/messages.html">Messages</a>
<a href="../app/reports.html">Reports</a>
<a class="active" href="../app/hire-flow.html">Hire</a>
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
<a href="../index.html">Workspace home</a><span>›</span><a href="hire-flow.html">Project setup</a><span>›</span><span>Create offer</span>
</div>
<div class="wizard-card compact" style="padding-top:30px">
<div class="wizard-header" style="margin-bottom:8px">
<img alt="HireHelper.ai" src="{{ asset('workspace-assets/img/logo.svg') }}"/>
<h1 class="wizard-title" style="font-size:42px">Create an offer</h1>
<p class="wizard-subtitle">Set the hourly rate, weekly cap, and manual time preferences, then continue to billing.</p>
</div>
<form data-invite-form="" data-next="billing-method.html">
<div class="form-row">
<div class="form-group">
<label class="form-label" for="freelancer">Freelancer</label>
<input class="input" id="freelancer" name="freelancer" required="" value="Ava Petrosyan"/>
</div>
<div class="form-group">
<label class="form-label" for="role">Role</label>
<input class="input" id="role" name="role" required="" value="Full stack developer"/>
</div>
</div>
<div class="form-group">
<label class="form-label">Project</label>
<div class="input" style="display:flex;align-items:center;height:auto;min-height:56px"><span data-brief="title">HireHelper.ai client dashboard rebuild</span></div>
</div>
<h2 style="font-size:32px;letter-spacing:-.04em;margin:26px 0 16px;text-align:left">Rate and weekly limit</h2>
<div class="form-row">
<div class="form-group">
<label class="form-label" for="rate">Rate</label>
<div style="display:grid;grid-template-columns:1fr 78px">
<input class="input" id="rate" min="1" name="rate" required="" step="1" type="number" value="35"/>
<div class="input" style="border-left:none;border-radius:0 16px 16px 0;display:grid;place-items:center;font-weight:700;background:#f3f6ff">$ / hr</div>
</div>
</div>
<div class="form-group">
<label class="form-label" for="weekly">Weekly limit</label>
<div style="display:grid;grid-template-columns:1fr 110px 170px">
<input class="input" id="weekly" min="1" name="weekly" required="" step="1" type="number" value="20"/>
<div class="input" style="border-left:none;border-radius:0 0 0 0;display:grid;place-items:center;font-weight:700;background:#f3f6ff">hrs/week</div>
<div class="input" data-weekly-max="" style="border-left:none;border-radius:0 16px 16px 0;display:grid;place-items:center;background:#fbfcff">$700.00 max / week</div>
</div>
</div>
</div>
<div class="checkbox-grid">
<label class="checkbox-line">
<input checked="" name="manual" type="checkbox"/>
<span><strong>Allow manual time</strong><br/><span class="muted">Let the freelancer log time manually if you want to support non-tracker work.</span></span>
</label>
<label class="checkbox-line">
<input name="multiOffer" type="checkbox"/>
<span><strong>Send offers to more freelancers for this project</strong><br/><span class="muted">Keep this off if you only want to proceed with a single specialist.</span></span>
</label>
</div>
<div class="form-actions">
<a class="link-button" href="hire-flow.html">‹ Back</a>
<button class="button button-primary" type="submit">Continue</button>
</div>
</form>
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
