# HireHelper Phase 3 — Admin Panel Roles, Offers, Freelancers, User Management

This package adds a real internal admin structure for your Filament panel.

## What is included

- Roles:
  - superadmin
  - admin
  - project_manager
  - sales_manager
  - client

- Access logic:
  - **Superadmin**: full access to everything.
  - **Admin**: operations access to clients, freelancers, projects, offers.
  - **Project manager**: accepted / delivery-stage projects and offers.
  - **Sales manager**: freelancer personas and pre-acceptance pipeline.

- Admin sections:
  - `/admin/users` — internal team users
  - `/admin/clients` — client accounts
  - `/admin/freelancers` — freelancer personas
  - `/admin/client-projects` — hiring projects
  - `/admin/project-offers` — offers

- Freelancer personas now support:
  - name
  - title
  - headline
  - country
  - city
  - hourly rate
  - reviews
  - rating
  - completed jobs
  - skills
  - tools
  - links
  - internal notes
  - ownership

## Install on Mac / local

Merge with Terminal, not Finder replace:

```bash
cd ~/Downloads
unzip hirehelper-phase3-adminpanel.zip -d hirehelper-phase3-temp

rsync -av ~/Downloads/hirehelper-phase3-temp/hirehelper-phase3-adminpanel/ ~/Desktop/hirehelper/

cd ~/Desktop/hirehelper
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=AdminBootstrapSeeder
php artisan serve
```

## Install on server

```bash
cd /home/trustreview/web/hirehelper.ai/app_repo
git pull origin main
/usr/bin/php8.4 artisan optimize:clear
/usr/bin/php8.4 artisan migrate --force
/usr/bin/php8.4 artisan db:seed --class=AdminBootstrapSeeder --force
/usr/bin/php8.4 artisan config:cache
/usr/bin/php8.4 artisan route:cache
/usr/bin/php8.4 artisan view:cache
```

Then restart services:

```bash
systemctl restart php8.4-fpm
systemctl reload nginx
```

## Important first login note

If your old Filament admin user had no role, the seeder will promote the first user to `superadmin` if no internal users exist yet.

If you still cannot access `/admin`, set your admin email manually in DB:

```sql
UPDATE users SET role = 'superadmin', is_active = 1 WHERE email = 'YOUR_ADMIN_EMAIL';
```

## Main routes to test

```text
/admin
/admin/users
/admin/clients
/admin/freelancers
/admin/client-projects
/admin/project-offers
```

## Recommended next phase

After this package works, build:
- freelancer signup/auth
- proposal marketplace
- accepted-contract dashboard for PM
- public freelancer profile page
- notifications and email flow
