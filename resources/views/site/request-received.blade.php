<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Project Request Received | HireHelper.ai</title>
  <meta name="description" content="Your project request has been received by HireHelper.ai. We will review it and follow up with the next step." />
  <meta name="theme-color" content="#f6f8fc" />
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml" />
  <link rel="canonical" href="https://hirehelper.ai/request-received.html" />
  <meta property="og:title" content="Project Request Received | HireHelper.ai" />
  <meta property="og:description" content="Your project request has been received by HireHelper.ai. We will review it and follow up with the next step." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://hirehelper.ai/request-received.html" />
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>

<body class="page-success" data-page="home">
  <div class="page-shell">
    
    <header class="site-header">
      <div class="container header-inner">
        <a class="brand" href="index.html" aria-label="HireHelper.ai home">
          <img src="assets/img/logo.svg" alt="HireHelper.ai" class="brand-logo" />
        </a>
        <nav class="desktop-nav" aria-label="Primary">
          <a class="nav-link" href="categories.html">Categories</a><a class="nav-link" href="how-it-works.html">How It Works</a><a class="nav-link" href="our-priorities.html">Our Priorities</a><a class="nav-link" href="help/index.html">Help</a><a class="nav-link" href="contact.html">Contact</a>
        </nav>
        <div class="header-actions">
          <a class="button button-secondary button-compact desktop-only" href="help/index.html">Help Center</a>
          <a class="button button-primary button-compact" href="/client/register">Start Hiring</a>
          <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
            <span></span><span></span>
            <span class="sr-only">Toggle navigation</span>
          </button>
        </div>
      </div>
      <div class="mobile-menu" id="mobile-menu" data-mobile-menu>
        <div class="container mobile-menu-inner">
          <nav class="mobile-nav" aria-label="Mobile">
            <a class="mobile-link" href="categories.html">Categories</a><a class="mobile-link" href="how-it-works.html">How It Works</a><a class="mobile-link" href="our-priorities.html">Our Priorities</a><a class="mobile-link" href="help/index.html">Help</a><a class="mobile-link" href="contact.html">Contact</a>
            <a class="mobile-link button button-primary" href="/client/register">Start Hiring</a>
          </nav>
        </div>
      </div>
    </header>
    
    <main>
      
    <section class="section section-centered">
      <div class="container narrow">
        <div class="success-card" data-reveal>
          <div class="icon-badge icon-badge-xl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="8.25"/><path d="m8.75 12 2.25 2.25 4.75-4.75"/></svg></div>
          <span class="eyebrow">Project Request Received</span>
          <h1>Thanks. Your project request is in.</h1>
          <p class="lead">We have received your request and will review it carefully. The next step is a focused follow-up based on your project goals, timeline, and the kind of specialist support you need.</p>
          <div class="success-summary" data-request-summary @if (! session('request_summary')) hidden @endif>
            <div class="summary-item"><span>Category</span><strong data-summary-field="category">{{ session('request_summary.category') }}</strong></div>
            <div class="summary-item"><span>Timeline</span><strong data-summary-field="timeline">{{ session('request_summary.timeline') }}</strong></div>
            <div class="summary-item"><span>Budget preference</span><strong data-summary-field="budget">{{ session('request_summary.budget') }}</strong></div>
            <div class="summary-item"><span>Work email</span><strong data-summary-field="email">{{ session('request_summary.email') }}</strong></div>
          </div>
          <div class="steps-card">
            <h2>What happens next</h2>
            <div class="steps-list">
              
    <article class="process-step" data-reveal>
      <div class="process-number">01</div>
      <div>
        <h3>We review your project request</h3>
        <p>We look at clarity, fit, urgency, and the type of support the work may require.</p>
      </div>
    </article>
    
              
    <article class="process-step" data-reveal>
      <div class="process-number">02</div>
      <div>
        <h3>We determine the most sensible next step</h3>
        <p>The next step depends on scope shape, timing, decision complexity, and role requirements.</p>
      </div>
    </article>
    
              
    <article class="process-step" data-reveal>
      <div class="process-number">03</div>
      <div>
        <h3>We follow up with a focused path forward</h3>
        <p>You will hear from us with guidance on the hiring path, project structure, or support model that makes the most sense.</p>
      </div>
    </article>
    
            </div>
          </div>
          <div class="button-row button-row-center">
            <a class="button button-primary" href="index.html">Back to Homepage</a>
            <a class="button button-secondary" href="help/index.html">Read the Help Center</a>
          </div>
        </div>
      </div>
    </section>
    
    </main>
    
    @include('site.partials.footer')
    
  </div>
  <div class="toast" data-toast data-flash-toast="{{ session('success') }}" aria-live="polite" aria-atomic="true"></div>
  <script src="assets/js/main.js" defer></script>
</body>
</html>
