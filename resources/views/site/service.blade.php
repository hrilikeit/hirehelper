<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $service->name }} — {{ $freelancer->name }} | HireHelper.ai</title>
    <meta name="description" content="{{ $service->name }} by {{ $freelancer->name }} on HireHelper.ai." />
    <meta name="theme-color" content="#f6f8fc" />
    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml" />
    <link rel="canonical" href="{{ url('/services/' . $service->slug) }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
</head>
<body class="page-freelancer page-freelancer-profile" data-page="service-page">
@php
    $locationLabel = $freelancer->display_location ?: $freelancer->country ?: $freelancer->location ?: 'Available remotely';
    $skillItems = collect(is_array($freelancer->skills ?? null) ? $freelancer->skills : preg_split('/\s*,\s*/', (string) ($freelancer->skills ?? ''), -1, PREG_SPLIT_NO_EMPTY))
        ->merge(is_array($freelancer->tools ?? null) ? $freelancer->tools : preg_split('/\s*,\s*/', (string) ($freelancer->tools ?? ''), -1, PREG_SPLIT_NO_EMPTY))
        ->filter(fn ($item) => filled($item))
        ->map(fn ($item) => trim((string) $item))
        ->unique()
        ->take(8)
        ->values();
    $bioSource = trim((string) ($freelancer->bio ?: $freelancer->overview ?: ''));
    $bioParagraphs = $bioSource !== '' ? preg_split('/\n\s*\n/', $bioSource) : [];
    $years = (float) $freelancer->years_experience;
    $yearsLabel = $years > 0 ? (rtrim(rtrim(number_format($years, 1), '0'), '.') . ' ' . ($years == 1.0 ? 'Year' : 'Years')) : 'Not set';
    $ratingValue = $service->star_rating > 0
        ? rtrim(rtrim(number_format((float) $service->star_rating, 1), '0'), '.') . '/5 stars'
        : null;
    $subscribeUrl = auth()->check()
        ? route('services.subscribe', $service->slug)
        : url('/client/register?redirect=' . urlencode('/services/' . $service->slug));
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
                @if (auth()->check())
                    <form method="post" action="{{ route('services.subscribe', $service->slug) }}" style="display:inline">
                        @csrf
                        <button class="button button-primary button-compact" type="submit">Subscribe</button>
                    </form>
                @else
                    <a class="button button-primary button-compact" href="{{ $subscribeUrl }}">Subscribe</a>
                @endif
            </div>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div class="container" style="margin-top:20px"><div style="padding:14px 20px;background:#d4edda;border-radius:10px;color:#155724">{{ session('success') }}</div></div>
        @endif
        @if (session('error'))
            <div class="container" style="margin-top:20px"><div style="padding:14px 20px;background:#f8d7da;border-radius:10px;color:#721c24">{{ session('error') }}</div></div>
        @endif
        @if (session('info'))
            <div class="container" style="margin-top:20px"><div style="padding:14px 20px;background:#d1ecf1;border-radius:10px;color:#0c5460">{{ session('info') }}</div></div>
        @endif

        <section class="freelancer-profile-section">
            <div class="container">
                <div class="freelancer-layout" data-reveal>
                    <aside class="freelancer-card freelancer-sidebar">
                        <div class="freelancer-photo-shell">
                            <img src="{{ $freelancer->avatar_url }}" alt="{{ $freelancer->name }}" class="freelancer-photo" />
                        </div>
                        <h1 class="freelancer-name">{{ $freelancer->name }}</h1>
                        <p class="freelancer-country">{{ $locationLabel }}</p>
                        @if ($ratingValue)
                            <div class="freelancer-rating-line">{{ $ratingValue }}</div>
                        @endif
                        @if ($existingSubscription)
                            <div style="width:100%;padding:14px;background:#d4edda;border-radius:10px;text-align:center;color:#155724;font-weight:600">Subscribed</div>
                        @else
                            <a class="button button-primary button-large freelancer-hire" href="{{ $subscribeUrl }}" style="width:100%;text-align:center">Hire Now</a>
                        @endif
                    </aside>

                    <div class="freelancer-main-column">
                        <section class="freelancer-card freelancer-overview" data-reveal>
                            {{-- Service subscription info --}}
                            <div style="background:linear-gradient(135deg,#6d6af8 0%,#a78bfa 100%);border-radius:16px;padding:28px 32px;margin-bottom:28px;color:#fff">
                                <h2 style="font-size:28px;margin:0 0 4px;font-weight:800;letter-spacing:-.03em">{{ $service->name }}</h2>
                                <p style="margin:0 0 20px;opacity:.85">Monthly service by {{ $freelancer->name }}</p>
                                <div style="display:flex;gap:32px;flex-wrap:wrap;align-items:flex-end">
                                    <div>
                                        <div style="font-size:12px;text-transform:uppercase;letter-spacing:.06em;opacity:.7">Monthly price</div>
                                        <div style="font-size:32px;font-weight:800;letter-spacing:-.03em">${{ number_format((float) $service->monthly_price, 2) }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:12px;text-transform:uppercase;letter-spacing:.06em;opacity:.7">Active users</div>
                                        <div style="font-size:32px;font-weight:800;letter-spacing:-.03em">{{ $service->active_users }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:12px;text-transform:uppercase;letter-spacing:.06em;opacity:.7">Rating</div>
                                        <div style="font-size:32px;font-weight:800;letter-spacing:-.03em">{{ $ratingValue ?: '—' }}</div>
                                    </div>
                                    <div style="margin-left:auto">
                                        @if ($existingSubscription)
                                            <div style="padding:12px 28px;background:rgba(255,255,255,.2);border-radius:10px;font-weight:700">Subscribed</div>
                                        @elseif (auth()->check())
                                            <form method="post" action="{{ route('services.subscribe', $service->slug) }}">
                                                @csrf
                                                <button type="submit" style="padding:14px 36px;background:#fff;color:#6d6af8;border:none;border-radius:12px;font-size:16px;font-weight:700;cursor:pointer">Subscribe Now</button>
                                            </form>
                                        @else
                                            <a href="{{ $subscribeUrl }}" style="display:inline-block;padding:14px 36px;background:#fff;color:#6d6af8;border-radius:12px;font-size:16px;font-weight:700;text-decoration:none">Subscribe Now</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="freelancer-stats" aria-label="Freelancer profile statistics">
                                <div class="freelancer-stat">
                                    <span class="freelancer-stat-label">Hourly Rate</span>
                                    <strong class="freelancer-stat-value">${{ number_format((float) $freelancer->hourly_rate, ((float) $freelancer->hourly_rate) == floor((float) $freelancer->hourly_rate) ? 0 : 2) }} / Hr</strong>
                                </div>
                                <div class="freelancer-stat">
                                    <span class="freelancer-stat-label">Total Earned</span>
                                    <strong class="freelancer-stat-value">${{ number_format((float) $freelancer->total_earned, ((float) $freelancer->total_earned) == floor((float) $freelancer->total_earned) ? 0 : 2) }}</strong>
                                </div>
                                <div class="freelancer-stat">
                                    <span class="freelancer-stat-label">Experience</span>
                                    <strong class="freelancer-stat-value">{{ $yearsLabel }}</strong>
                                </div>
                            </div>

                            @if ($skillItems->isNotEmpty())
                                <h2 class="freelancer-section-title">Skills</h2>
                                <ul class="freelancer-skills" aria-label="Freelancer skills">
                                    @foreach ($skillItems as $skill)
                                        <li>{{ $skill }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <h2 class="freelancer-role">{{ $freelancer->title ?: 'Freelancer profile' }}</h2>
                            <div class="freelancer-summary-block">
                                @forelse ($bioParagraphs as $paragraph)
                                    <p class="freelancer-summary">{{ trim($paragraph) }}</p>
                                @empty
                                    <p class="freelancer-summary">This freelancer profile will be updated with more details soon.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="freelancer-card freelancer-reviews" data-reveal>
                            <div class="freelancer-reviews-head">
                                <div>
                                    <h2 class="freelancer-reviews-title">Reviews</h2>
                                    <p class="freelancer-reviews-copy">{{ $freelancer->review_count ?: $freelancer->reviews->count() }} reviews on file.</p>
                                </div>
                                @if (! $existingSubscription)
                                    <form method="post" action="{{ $subscribeUrl }}">
                                        @csrf
                                        <button class="button button-secondary button-compact" type="submit">Subscribe Now</button>
                                    </form>
                                @endif
                            </div>
                            <div class="freelancer-review-list">
                                @forelse ($freelancer->reviews as $review)
                                    <article class="freelancer-review-card">
                                        <h3>{{ $review->review_title }}</h3>
                                        <div class="freelancer-review-meta">
                                            <span>{{ optional($review->date_from)->format('F j, Y') ?: 'Start date not set' }} - {{ optional($review->date_to)->format('F j, Y') ?: 'End date not set' }}</span>
                                            <span>{{ number_format((int) $review->hours) }} Hours</span>
                                            <span>${{ number_format((float) $review->rate, ((float) $review->rate) == floor((float) $review->rate) ? 0 : 2) }} / hr</span>
                                            <span>{{ (int) $review->stars }}/5 stars</span>
                                        </div>
                                        <p>{{ $review->review_text }}</p>
                                    </article>
                                @empty
                                    <article class="freelancer-review-card">
                                        <h3>Reviews will appear here</h3>
                                        <p>This freelancer does not have public reviews yet.</p>
                                    </article>
                                @endforelse
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('site.partials.footer')
</div>
<script src="{{ asset('assets/js/main.js') }}" defer></script>
</body>
</html>
