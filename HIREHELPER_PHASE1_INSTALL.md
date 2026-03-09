# HireHelper.ai Laravel Phase 1 install

This package converts the provided public-site HTML and client-workspace HTML into Laravel views, adds database-backed public forms, and adds two Filament admin resources:

- Hire Requests
- Contact Messages

## What this phase includes

- public website pages from the design package
- client workspace prototype pages from the workspace package
- working Laravel routes for all provided pages
- `start-hiring` form saved to database
- `contact` form saved to database
- Filament resources to review/manage both submission types
- custom 404 page
- robots.txt and sitemap.xml

## What this phase does not do yet

These are good Phase 2 items:

- real client/freelancer authentication flows
- persistent projects, contracts, messages, reports, and billing logic
- payment gateway integration
- freelancer marketplace matching engine
- notifications, emails, and dashboards with real data

## How to install

From your Laravel project root:

1. Extract this package into the Laravel root so the folders merge.
2. Run:

```bash
php artisan optimize:clear
php artisan migrate
php artisan config:clear
```

If your server aliases `php` to the wrong version, use the full binary:

```bash
/usr/bin/php8.4 artisan optimize:clear
/usr/bin/php8.4 artisan migrate
/usr/bin/php8.4 artisan config:clear
```

## Main URLs

Public site:

- /
- /how-it-works.html
- /our-priorities.html
- /categories.html
- /start-hiring.html
- /contact.html
- /help/index.html

Workspace prototype:

- /client-workspace.html
- /app/dashboard.html
- /app/hire-flow.html
- /app/messages.html

Admin:

- /admin/hire-requests
- /admin/contact-messages
