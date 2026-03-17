<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Page Not Found | HireHelper.ai</title>
  <meta name="description" content="The page you are looking for does not exist or has moved. Continue from the homepage, start hiring page, or Help Center." />
  <meta name="theme-color" content="#f6f8fc" />
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml" />
  <link rel="canonical" href="https://hirehelper.ai/404.html" />
  <meta property="og:title" content="Page Not Found | HireHelper.ai" />
  <meta property="og:description" content="The page you are looking for does not exist or has moved. Continue from the homepage, start hiring page, or Help Center." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://hirehelper.ai/404.html" />
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>

<body class="" data-page="home">
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
          <span class="eyebrow">404</span>
          <h1>Page not found</h1>
          <p class="lead">The page you are looking for does not exist or has moved. Use the links below to continue.</p>
          <div class="button-row button-row-center">
            <a class="button button-primary" href="index.html">Go to Homepage</a>
            <a class="button button-secondary" href="/client/register">Start Hiring</a>
            <a class="button button-secondary" href="help/index.html">Visit Help Center</a>
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
