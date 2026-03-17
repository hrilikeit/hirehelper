<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a class="brand brand-footer" href="{{ route('home') }}" aria-label="HireHelper.ai home">
                <img src="{{ asset('assets/img/logo-footer.svg') }}" alt="HireHelper.ai" class="brand-logo" />
            </a>
            <p>HireHelper.ai helps companies hire specialized freelance developers and designers, structure delivery, and keep communication and payments organized in one place.</p>
            <p class="footer-closing">Built for teams that want clarity from the first brief to the final handoff.</p>
            @include('site.partials.payment-badges')
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
