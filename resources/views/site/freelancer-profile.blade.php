<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $freelancer->name }} | HireHelper.ai freelancer profile</title>
    <meta name="description" content="View {{ $freelancer->name }} on HireHelper.ai." />
    <meta name="theme-color" content="#f6f8fc" />
    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml" />
    <link rel="canonical" href="{{ filled($freelancer->slug) ? route('freelancers.show', ['slug' => $freelancer->slug]) : route('freelancers.show-id', ['freelancer' => $freelancer->id]) }}" />
    <meta property="og:title" content="{{ $freelancer->name }} | HireHelper.ai freelancer profile" />
    <meta property="og:description" content="View {{ $freelancer->name }} on HireHelper.ai." />
    <meta property="og:type" content="profile" />
    <meta property="og:url" content="{{ filled($freelancer->slug) ? route('freelancers.show', ['slug' => $freelancer->slug]) : route('freelancers.show-id', ['freelancer' => $freelancer->id]) }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
</head>
<body class="page-freelancer-profile" data-page="freelancer-profile">
@php
    $locationLabel = $freelancer->country ?: $freelancer->display_location ?: $freelancer->location ?: 'Available remotely';
    $skillItems = collect(is_array($freelancer->skills ?? null) ? $freelancer->skills : preg_split('/\s*,\s*/', (string) ($freelancer->skills ?? ''), -1, PREG_SPLIT_NO_EMPTY))
        ->merge(is_array($freelancer->tools ?? null) ? $freelancer->tools : preg_split('/\s*,\s*/', (string) ($freelancer->tools ?? ''), -1, PREG_SPLIT_NO_EMPTY))
        ->filter(fn ($item) => filled($item))
        ->map(fn ($item) => trim((string) $item))
        ->unique()
        ->take(8)
        ->values();
    $bioSource = trim((string) ($freelancer->bio ?: $freelancer->overview ?: '')); 
    $bioParagraphs = $bioSource !== '' ? preg_split('/
\s*
/', $bioSource) : [];
    $initials = collect(preg_split('/\s+/', trim((string) $freelancer->name), -1, PREG_SPLIT_NO_EMPTY))
        ->take(2)
        ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $years = (float) $freelancer->years_experience;
    $yearsLabel = $years > 0 ? (rtrim(rtrim(number_format($years, 1), '0'), '.') . ' ' . ($years == 1.0 ? 'Year' : 'Years')) : 'Not set';
@endphp
<div class="page-shell">
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="HireHelper.ai home">
                <img src="{{ asset('assets/img/logo.svg') }}" alt="HireHelper.ai" class="brand-logo" />
            </a>
            <nav class="desktop-nav" aria-label="Primary">
                <a class="nav-link" href="{{ route('categories') }}">Categories</a>
                <a class="nav-link" href="{{ route('how-it-works') }}">How It Works</a>
                <a class="nav-link" href="{{ route('our-priorities') }}">Our Priorities</a>
                <a class="nav-link" href="{{ route('help.index') }}">Help</a>
                <a class="nav-link" href="{{ route('contact.show') }}">Contact</a>
            </nav>
            <div class="header-actions">
                <a class="button button-secondary button-compact desktop-only" href="{{ route('help.index') }}">Help Center</a>
                <a class="button button-primary button-compact" href="{{ route('client.register') }}">Start Hiring</a>
                <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle>
                    <span></span><span></span>
                    <span class="sr-only">Toggle navigation</span>
                </button>
            </div>
        </div>
        <div class="mobile-menu" id="mobile-menu" data-mobile-menu>
            <div class="container mobile-menu-inner">
                <nav class="mobile-nav" aria-label="Mobile">
                    <a class="mobile-link" href="{{ route('categories') }}">Categories</a>
                    <a class="mobile-link" href="{{ route('how-it-works') }}">How It Works</a>
                    <a class="mobile-link" href="{{ route('our-priorities') }}">Our Priorities</a>
                    <a class="mobile-link" href="{{ route('help.index') }}">Help</a>
                    <a class="mobile-link" href="{{ route('contact.show') }}">Contact</a>
                    <a class="mobile-link button button-primary" href="{{ route('client.register') }}">Start Hiring</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <section class="section freelancer-profile-section">
            <div class="container">
                <div class="freelancer-profile-grid" data-reveal>
                    <aside class="freelancer-profile-card">
                        <div class="freelancer-avatar-frame">
                            <img src="{{ $freelancer->avatar_url }}" alt="{{ $freelancer->name }}" class="freelancer-avatar-image" />
                            <span class="freelancer-avatar-badge">{{ $initials }}</span>
                        </div>

                        <div class="freelancer-profile-card-copy">
                            <h1>{{ $freelancer->name }}</h1>
                            <p class="freelancer-location">{{ $locationLabel }}</p>
                            @if (filled($freelancer->average_rating))
                                <div class="freelancer-rating-pill">{{ rtrim(rtrim(number_format((float) $freelancer->average_rating, 1), '0'), '.') }}/5 stars</div>
                            @endif
                        </div>

                        <a class="button button-primary freelancer-profile-cta" href="{{ $hireUrl }}">Hire Now</a>
                    </aside>

                    <section class="freelancer-details-panel">
                        <div class="freelancer-stat-grid">
                            <article class="freelancer-stat-card">
                                <span>Hourly rate</span>
                                <strong>${{ number_format((float) $freelancer->hourly_rate, ((float) $freelancer->hourly_rate) == floor((float) $freelancer->hourly_rate) ? 0 : 2) }}/Hr</strong>
                            </article>
                            <article class="freelancer-stat-card">
                                <span>Total earned</span>
                                <strong>${{ number_format((float) $freelancer->total_earned, ((float) $freelancer->total_earned) == floor((float) $freelancer->total_earned) ? 0 : 2) }}</strong>
                            </article>
                            <article class="freelancer-stat-card">
                                <span>Experience</span>
                                <strong>{{ $yearsLabel }}</strong>
                            </article>
                        </div>

                        @if ($skillItems->isNotEmpty())
                            <div class="freelancer-skills-block">
                                <h2>Skills</h2>
                                <div class="freelancer-chip-row">
                                    @foreach ($skillItems as $skill)
                                        <span class="freelancer-skill-chip">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="freelancer-copy-block">
                            <h2>{{ $freelancer->title ?: 'Freelancer profile' }}</h2>
                            @forelse ($bioParagraphs as $paragraph)
                                <p>{{ trim($paragraph) }}</p>
                            @empty
                                <p>This freelancer profile will be updated with more details soon.</p>
                            @endforelse
                        </div>
                    </section>
                </div>

                <section class="freelancer-reviews-panel" data-reveal>
                    <div class="freelancer-reviews-header">
                        <div>
                            <h2>Reviews</h2>
                            <p>{{ $freelancer->review_count ?: $freelancer->reviews->count() }} reviews on file.</p>
                        </div>
                        <a class="button button-secondary button-compact" href="{{ $hireUrl }}">Hire Now</a>
                    </div>

                    <div class="freelancer-review-stack">
                        @forelse ($freelancer->reviews as $review)
                            <article class="freelancer-review-card">
                                <h3>{{ $review->review_title }}</h3>
                                <div class="freelancer-review-meta">
                                    <span>{{ optional($review->date_from)->format('F j, Y') ?: 'Start date not set' }}</span>
                                    <span>–</span>
                                    <span>{{ optional($review->date_to)->format('F j, Y') ?: 'End date not set' }}</span>
                                    <span>·</span>
                                    <span>{{ number_format((int) $review->hours) }} Hours</span>
                                    <span>·</span>
                                    <span>${{ number_format((float) $review->rate, ((float) $review->rate) == floor((float) $review->rate) ? 0 : 2) }} / hr</span>
                                    <span>·</span>
                                    <span>{{ (int) $review->stars }}/5 stars</span>
                                </div>
                                <p>{{ $review->review_text }}</p>
                            </article>
                        @empty
                            <article class="freelancer-review-card">
                                <h3>Reviews will appear here</h3>
                                <p>This freelancer does not have public reviews yet. You can still continue to the client registration flow and start the hiring process.</p>
                            </article>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a class="brand brand-footer" href="{{ route('home') }}" aria-label="HireHelper.ai home">
                    <img src="{{ asset('assets/img/logo-footer.svg') }}" alt="HireHelper.ai" class="brand-logo" />
                </a>
                <p>HireHelper.ai helps companies hire specialized freelance developers and designers, structure delivery, and keep communication and payments organized in one place.</p>
                <p class="footer-closing">Built for teams that want clarity from the first brief to the final handoff.</p>
            </div>
            <div>
                <h3>Company</h3>
                <ul>
                    <li><a href="{{ route('how-it-works') }}">How It Works</a></li>
                    <li><a href="{{ route('our-priorities') }}">Our Priorities</a></li>
                    <li><a href="{{ route('contact.show') }}">Contact</a></li>
                    <li><a href="{{ route('help.index') }}">Help Center</a></li>
                    <li><a href="{{ route('terms') }}">Terms and Conditions</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('sitemap') }}">Sitemap</a></li>
                </ul>
            </div>
            <div>
                <h3>Categories</h3>
                <ul>
                    <li><a href="{{ route('services.web-development') }}">Web Development</a></li>
                    <li><a href="{{ route('services.mobile-development') }}">Mobile Development</a></li>
                    <li><a href="{{ route('services.ecommerce') }}">E-Commerce</a></li>
                    <li><a href="{{ route('services.ui-ux-design') }}">UI/UX Design</a></li>
                </ul>
            </div>
            <div>
                <h3>Guidance</h3>
                <ul>
                    <li><a href="{{ route('help.getting-started-as-a-client') }}">Getting Started as a Client</a></li>
                    <li><a href="{{ route('help.how-to-write-a-strong-project-brief') }}">How to Write a Strong Project Brief</a></li>
                    <li><a href="{{ route('help.how-to-review-fit-and-compare-specialists') }}">How to Review Fit and Compare Specialists</a></li>
                    <li><a href="{{ route('help.making-an-offer-and-starting-work') }}">Making an Offer and Starting Work</a></li>
                    <li><a href="{{ route('client.register') }}">Start Hiring</a></li>
                </ul>
            </div>
        </div>
        <div class="container footer-meta">
            <span>HireHelper.ai</span>
            <span>&copy; <span data-current-year></span> HireHelper.ai</span>
        </div>
    </footer>
</div>
<script src="{{ asset('assets/js/main.js') }}" defer></script>
</body>
</html>
