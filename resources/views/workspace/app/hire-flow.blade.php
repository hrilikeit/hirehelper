<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1" name="viewport"/>
<title>Project setup · HireHelper.ai</title>
<meta content="Project setup page for HireHelper.ai" name="description"/>
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
<a href="../app/dashboard.html">Projects</a>
<a href="../app/messages.html">Messages</a>
<a href="../app/reports.html">Reports</a>
<a href="../app/hire-flow.html">Hire</a>
<a href="../app/settings.html">Settings</a>
</div>
</header>
<main class="page-main">
<div class="container">
<div class="breadcrumbs">
<a href="../index.html">Workspace home</a><span>›</span><span>Project setup</span>
</div>
<div class="page-heading">
<div>
<span class="badge"><span class="dot"></span> Project setup</span>
<h1>Project brief</h1>
<p>Capture the work details in one clean page.</p>
</div>
<a class="button button-secondary" href="dashboard.html">Back to dashboard</a>
</div>
<div class="hire-layout">
<section class="hire-brief-card">
<h2>Project brief</h2>
<p>Only the fields that help define the project are kept here.</p>
<form data-brief-form="" data-hire-flow-form="">
<div class="form-group">
<label class="form-label" for="jobTitle">Project title</label>
<input class="input" data-brief-field="title" id="jobTitle" name="jobTitle" placeholder="HireHelper.ai client dashboard rebuild" required=""/>
</div>
<div class="form-group">
<label class="form-label" for="jobDescription">What needs to be done</label>
<textarea class="textarea" data-brief-field="description" id="jobDescription" name="jobDescription" placeholder="Design and implement the signed-in client experience for HireHelper.ai. The scope includes dashboard UX, project brief setup, billing setup, and contract management." required=""></textarea>
</div>
<div class="form-row">
<div class="form-group">
<label class="form-label" for="experience">Experience level</label>
<select class="select" data-brief-field="experience" id="experience" name="experience">
<option>Entry</option>
<option selected="">Intermediate</option>
<option>Expert</option>
</select>
</div>
<div class="form-group">
<label class="form-label" for="timeframe">Timeframe</label>
<select class="select" data-brief-field="timeframe" id="timeframe" name="timeframe">
<option>Less than 1 week</option>
<option selected="">Less than 1 month</option>
<option>1–3 months</option>
<option>3–6 months</option>
<option>More than 6 months</option>
</select>
</div>
<div class="form-group">
<label class="form-label" for="specialty">Specialty</label>
<select class="select" data-brief-field="specialty" id="specialty" name="specialty">
<option>Front-end development</option>
<option>Back-end development</option>
<option selected="">Full stack development</option>
<option>Mobile app development</option>
<option>UI/UX design</option>
<option>E-commerce development</option>
</select>
</div>
</div>
<div class="form-actions">
<a class="link-button" href="dashboard.html">‹ Back</a>
<button class="button button-primary" type="submit">Save brief</button>
</div>
<div class="save-note" data-save-note="">Brief saved</div>
</form>
</section>
<aside class="hire-summary-card">
<h2>Current brief</h2>
<p>Only the essential project details are shown here.</p>

<div class="brief-mini">
<div class="mini-row">
<span class="mini-label">Current title</span>
<strong data-brief="title">HireHelper.ai client dashboard rebuild</strong>
</div>
<div class="mini-row">
<span class="mini-label">Experience</span>
<strong data-brief="experience">Intermediate</strong>
</div>
<div class="mini-row">
<span class="mini-label">Timeframe</span>
<strong data-brief="timeframe">Less than 1 month</strong>
</div>
<div class="mini-row">
<span class="mini-label">Specialty</span>
<strong data-brief="specialty">Full stack development</strong>
</div>
</div>
</aside>
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