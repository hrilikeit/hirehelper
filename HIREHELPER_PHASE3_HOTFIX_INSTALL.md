Phase 3 hotfix for Filament 5 resource navigation group typing.

This fixes the fatal error:
Type of App\Filament\Resources\...::$navigationGroup must be UnitEnum|string|null.

Mac install:
1. Unzip this package.
2. Merge it into your Laravel project root with rsync:
   rsync -av ~/Downloads/hirehelper-phase3-hotfix/ ~/Desktop/hirehelper/
3. Clear caches and run the app:
   cd ~/Desktop/hirehelper
   php artisan optimize:clear
   composer dump-autoload
   php artisan serve

Server install:
1. Merge the files into the project root.
2. Run:
   cd /home/trustreview/web/hirehelper.ai/app_repo
   /usr/bin/php8.4 artisan optimize:clear
   /usr/bin/php8.4 artisan config:cache
   /usr/bin/php8.4 artisan route:cache
   /usr/bin/php8.4 artisan view:cache

Overwritten files:
- app/Filament/Resources/AdminUserResource.php
- app/Filament/Resources/ClientProjectResource.php
- app/Filament/Resources/FreelancerResource.php
- app/Filament/Resources/ProjectOfferResource.php
- app/Filament/Resources/UserResource.php
