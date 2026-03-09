# HireHelper.ai Laravel Phase 2 install

This package is the next step on top of the Laravel setup and Phase 1 site conversion.

## What this phase adds

- client registration
- client login / logout
- database-backed client workspace
- project brief saving
- freelancer profiles
- project offer creation
- billing method saving
- pending contract flow
- active contract flow
- simple project messaging
- reports page backed by real data
- workspace settings page
- Filament admin resources for:
  - Clients
  - Freelancers
  - Projects
  - Offers

## Before you install

This package is designed to be merged into the existing Laravel project root.

Do **not** replace the whole project folder in Finder or your file manager.

Merge it into the existing Laravel app.

## Install on Mac / local

From your Laravel root:

```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed
php artisan config:clear
```

If you do not want to run all seeders, this also works:

```bash
php artisan db:seed --class=FreelancerSeeder
```

The workspace will also auto-create demo freelancers if the table is empty.

## Install on server

From the project root:

```bash
/usr/bin/php8.4 artisan optimize:clear
/usr/bin/php8.4 artisan migrate --force
/usr/bin/php8.4 artisan db:seed --force
/usr/bin/php8.4 artisan config:cache
/usr/bin/php8.4 artisan route:cache
/usr/bin/php8.4 artisan view:cache
```

If your server does not have a `db:seed --force` workflow yet, you can skip it because the workspace auto-seeds demo freelancers when needed.

## Main URLs

Public:
- `/`
- `/start-hiring.html`
- `/contact.html`

Workspace:
- `/client-workspace.html`
- `/client/register`
- `/client/login`
- `/app/welcome.html`
- `/app/dashboard.html`
- `/app/hire-flow.html`
- `/app/invite-offer.html`
- `/app/billing-method.html`
- `/app/project-pending.html`
- `/app/project-active.html`
- `/app/messages.html`
- `/app/reports.html`
- `/app/settings.html`

Admin:
- `/admin/clients`
- `/admin/freelancers`
- `/admin/client-projects`
- `/admin/project-offers`

## Recommended test flow

1. Register a new client account.
2. Open `/app/hire-flow.html`.
3. Save a project brief.
4. Continue to the offer page.
5. Choose a freelancer and save the offer.
6. Add a billing method.
7. Open the pending contract page.
8. Activate the contract.
9. Open messages and reports.
