<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Start Hiring | HireHelper.ai</title>
  <meta name="description" content="Tell HireHelper.ai what you need to build, improve, or redesign. Share your goals, timeline, and priorities to begin a structured hiring process." />
  <meta name="theme-color" content="#f6f8fc" />
  <link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml" />
  <link rel="canonical" href="https://hirehelper.ai/client/register" />
  <meta property="og:title" content="Start Hiring | HireHelper.ai" />
  <meta property="og:description" content="Tell HireHelper.ai what you need to build, improve, or redesign. Share your goals, timeline, and priorities to begin a structured hiring process." />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://hirehelper.ai/client/register" />
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>

<body class="page-form" data-page="home">
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
      
    <section class="page-hero page-hero-compact">
      <div class="container">
        <div class="page-hero-copy" data-reveal>
          <span class="eyebrow">Start Hiring</span>
          <h1>Tell us what you need</h1>
          <p class="lead">Use this short project intake to describe the work, the outcome you are aiming for, and the kind of support you need. A strong start helps everyone move faster.</p>
        </div>
      </div>
    </section>
    <section class="section section-form-shell">
      <div class="container form-layout">
        <aside class="form-aside" data-reveal>
          <div class="aside-card">
            <h2>What helps us evaluate faster</h2>
            <ul class='bullet-list'><li><span class='bullet-icon'><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="8.25"/><path d="m8.75 12 2.25 2.25 4.75-4.75"/></svg></span><span>A clear outcome, even if the scope is still evolving</span></li><li><span class='bullet-icon'><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="8.25"/><path d="m8.75 12 2.25 2.25 4.75-4.75"/></svg></span><span>Useful context about the product, system, or workflow</span></li><li><span class='bullet-icon'><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="8.25"/><path d="m8.75 12 2.25 2.25 4.75-4.75"/></svg></span><span>Timeline pressure, launch dates, and decision constraints</span></li><li><span class='bullet-icon'><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><circle cx="12" cy="12" r="8.25"/><path d="m8.75 12 2.25 2.25 4.75-4.75"/></svg></span><span>The team members who will be involved in the work</span></li></ul>
          </div>
          <div class="aside-card">
            <h3>What happens after you submit</h3>
            <p>We review each request carefully. If the project is a fit, the next step is a focused conversation about scope, priorities, and the right type of support.</p>
          </div>
          <div class="aside-card subtle-card">
            <img src="assets/img/contact-studio.svg" alt="" />
          </div>
        </aside>
        <div class="form-panel" data-reveal>
          @if ($errors->any())
          <div class="form-alert form-alert-error">
            <strong>Please fix the following:</strong>
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif
          <form class="site-form inquiry-form" data-inquiry-form novalidate method="POST" action="{{ route('hire.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-section-title">Project request</div>
            <div class="grid grid-form">
              <div class="field">
                <label for="category">What are you hiring for?</label>
                <p class="field-help">Select the main area of work.</p>
                <select id="category" name="category" required>
                  <option value="" disabled @selected(! old('category'))>Please select</option>
                  <option value="Web Development" @selected(old('category') === 'Web Development')>Web Development</option>
                  <option value="Mobile Development" @selected(old('category') === 'Mobile Development')>Mobile Development</option>
                  <option value="E-Commerce" @selected(old('category') === 'E-Commerce')>E-Commerce</option>
                  <option value="UI/UX Design" @selected(old('category') === 'UI/UX Design')>UI/UX Design</option>
                  <option value="Product Support" @selected(old('category') === 'Product Support')>Product Support</option>
                  <option value="Not Sure Yet" @selected(old('category') === 'Not Sure Yet')>Not Sure Yet</option>
                </select>
              </div>
              <div class="field">
                <label for="project-title">Project title</label>
                <input id="project-title" name="projectTitle" type="text" placeholder="State the project in one clear line." value="{{ old('projectTitle') }}" required />
              </div>
            </div>

            <div class="field">
              <label for="needs">What needs to happen?</label>
              <p class="field-help">Describe the current situation, what needs to change, and what success looks like.</p>
              <textarea id="needs" name="needs" rows="5" minlength="40" required>{{ old('needs') }}</textarea>
            </div>

            <div class="grid grid-form">
              <div class="field">
                <label for="outcome">What outcome matters most?</label>
                <select id="outcome" name="outcome" required>
                  <option value="" disabled @selected(! old('outcome'))>Please select</option>
                  <option value="Launch a new product or feature" @selected(old('outcome') === 'Launch a new product or feature')>Launch a new product or feature</option>
                  <option value="Improve performance or reliability" @selected(old('outcome') === 'Improve performance or reliability')>Improve performance or reliability</option>
                  <option value="Redesign a workflow or interface" @selected(old('outcome') === 'Redesign a workflow or interface')>Redesign a workflow or interface</option>
                  <option value="Fix delivery bottlenecks" @selected(old('outcome') === 'Fix delivery bottlenecks')>Fix delivery bottlenecks</option>
                  <option value="Increase conversion or revenue" @selected(old('outcome') === 'Increase conversion or revenue')>Increase conversion or revenue</option>
                  <option value="Support ongoing product execution" @selected(old('outcome') === 'Support ongoing product execution')>Support ongoing product execution</option>
                </select>
              </div>
              <div class="field">
                <label for="timeline">Timeline</label>
                <select id="timeline" name="timeline" required>
                  <option value="" disabled @selected(! old('timeline'))>Please select</option>
                  <option value="As soon as possible" @selected(old('timeline') === 'As soon as possible')>As soon as possible</option>
                  <option value="Within 2 weeks" @selected(old('timeline') === 'Within 2 weeks')>Within 2 weeks</option>
                  <option value="Within 1 month" @selected(old('timeline') === 'Within 1 month')>Within 1 month</option>
                  <option value="1 to 3 months" @selected(old('timeline') === '1 to 3 months')>1 to 3 months</option>
                  <option value="More than 3 months" @selected(old('timeline') === 'More than 3 months')>More than 3 months</option>
                  <option value="Flexible" @selected(old('timeline') === 'Flexible')>Flexible</option>
                </select>
              </div>
            </div>

            <div class="grid grid-form">
              <div class="field">
                <label for="budget">Budget preference</label>
                <select id="budget" name="budget" required>
                  <option value="" disabled @selected(! old('budget'))>Please select</option>
                  <option value="Fixed scope discussion" @selected(old('budget') === 'Fixed scope discussion')>Fixed scope discussion</option>
                  <option value="Hourly engagement" @selected(old('budget') === 'Hourly engagement')>Hourly engagement</option>
                  <option value="Monthly support" @selected(old('budget') === 'Monthly support')>Monthly support</option>
                  <option value="Need guidance" @selected(old('budget') === 'Need guidance')>Need guidance</option>
                </select>
              </div>
              <div class="field">
                <label for="team">Who will work with the freelancer?</label>
                <select id="team" name="team" required>
                  <option value="" disabled @selected(! old('team'))>Please select</option>
                  <option value="Founder or business owner" @selected(old('team') === 'Founder or business owner')>Founder or business owner</option>
                  <option value="Product manager" @selected(old('team') === 'Product manager')>Product manager</option>
                  <option value="Marketing or growth team" @selected(old('team') === 'Marketing or growth team')>Marketing or growth team</option>
                  <option value="Internal engineering team" @selected(old('team') === 'Internal engineering team')>Internal engineering team</option>
                  <option value="Agency team" @selected(old('team') === 'Agency team')>Agency team</option>
                  <option value="Not defined yet" @selected(old('team') === 'Not defined yet')>Not defined yet</option>
                </select>
              </div>
            </div>

            <div class="field">
              <label for="context">Anything the freelancer should know before the first conversation?</label>
              <p class="field-help">Share context such as technical stack, user type, team setup, constraints, dependencies, or launch dates.</p>
              <textarea id="context" name="context" rows="4">{{ old('context') }}</textarea>
            </div>

            <div class="grid grid-form">
              <div class="field">
                <label for="name">Your name</label>
                <input id="name" name="name" type="text" autocomplete="name" value="{{ old('name') }}" required />
              </div>
              <div class="field">
                <label for="email">Work email</label>
                <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required />
              </div>
            </div>

            <div class="grid grid-form">
              <div class="field">
                <label for="company">Company</label>
                <input id="company" name="company" type="text" autocomplete="organization" value="{{ old('company') }}" />
              </div>
              <div class="field">
                <label for="website">Website</label>
                <input id="website" name="website" type="url" placeholder="https://example.com" value="{{ old('website') }}" />
              </div>
            </div>

            <div class="field">
              <label for="source">How did you hear about HireHelper.ai?</label>
              <select id="source" name="source">
                <option value="" disabled @selected(! old('source'))>Please select</option>
                <option value="Search" @selected(old('source') === 'Search')>Search</option>
                <option value="Referral" @selected(old('source') === 'Referral')>Referral</option>
                <option value="Social" @selected(old('source') === 'Social')>Social</option>
                <option value="Partner" @selected(old('source') === 'Partner')>Partner</option>
                <option value="Returning client" @selected(old('source') === 'Returning client')>Returning client</option>
                <option value="Other" @selected(old('source') === 'Other')>Other</option>
              </select>
            </div>

            <div class="field">
              <label for="hire-attachments">Optional files</label>
              <p class="field-help">Attach documents like a scope, screenshots, deck, or brief. Files from website forms are sent to <strong>support@hirehelper.ai</strong>.</p>
              <input id="hire-attachments" name="attachments[]" type="file" multiple />
            </div>

            <div class="form-actions">
              <button class="button button-primary button-large" type="submit">Submit Project Request <span class="button-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg></span></button>
              <a class="button button-secondary button-large" href="contact.html">Need to talk first? Contact Us</a>
            </div>
            <p class="form-note">We read every request carefully. If the project is a fit, the next step is a focused conversation about scope, priorities, and the right type of support. Files from this form are sent to support@hirehelper.ai.</p>
          </form>
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
