# HireHelper update package - start here

This package contains the changed files for the requested update.
Copy these files into the root of your existing Laravel project, then run the commands in the text files included here.

## What is included
- Public Start Hiring links now go to `/client/register`
- Client registration form now removes `Company` and `Phone`
- Project brief page no longer auto-fills the title and description
- Offer page now uses:
  - freelancer email
  - rate
  - weekly limit (default 40)
- Removed the `Signed in` badge from the client dashboard pages
- Removed the extra password/access block from settings
- Added invoice details page
- Billing methods page now supports:
  - add PayPal / Visa / Mastercard
  - set primary billing method
  - remove billing method
- Admin panel now has the ability to delete freelancers
- Added safe database support for:
  - freelancer contact email
  - soft delete on freelancers
  - storing freelancer email on project offers
  - invoice details table

## Files included in this package
- changed controllers
- changed models
- changed Blade views
- changed routes
- changed JS/CSS
- new database migrations
- admin freelancer delete update

## Important
This zip is a **patch package**.
It contains the changed files only, not the entire production repository.

If you want to update your existing project safely:
1. unzip this package
2. copy these files into your local Laravel project root
3. run the local commands in `01_LOCAL_UPDATE_COMMANDS.txt`
4. test locally
5. push to git using `02_GIT_PUSH_COMMANDS.txt`
6. deploy to server using `03_SERVER_DEPLOY_COMMANDS.txt`

## Local pages to test before pushing
- `/client/register`
- `/app/hire-flow.html` (after client login)
- `/app/invite-offer.html` (after client login)
- `/app/billing-method.html` (after client login)
- `/app/invoice-details.html` (after client login)
- `/app/settings.html` (after client login)
- `/admin/freelancers` (after admin login)

## Notes
- The server deploy flow clears caches, runs migrations, fixes permissions, recreates the public storage symlink, and restarts PHP/Nginx. That is the safest deployment flow to avoid the usual Laravel 500 errors.
- If any command fails, stop there and do not continue to the next command.
