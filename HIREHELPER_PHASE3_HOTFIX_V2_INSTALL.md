# HireHelper Phase 3 Hotfix v2

This hotfix fixes Filament 5 compatibility issues in the admin resources:
- `navigationGroup` property type updated to `string | UnitEnum | null`
- `FreelancerResource::infolist()` updated from `Infolist` signature to `Schema` signature
- invalid old `Filament\Infolists\Infolist` import removed

## Install on Mac

```bash
cd ~/Downloads
unzip hirehelper-phase3-hotfix-v2.zip -d hirehelper-phase3-hotfix-v2-temp

rsync -av ~/Downloads/hirehelper-phase3-hotfix-v2-temp/hirehelper-phase3-hotfix-v2/ ~/Desktop/hirehelper/

cd ~/Desktop/hirehelper
php artisan optimize:clear
composer dump-autoload
php artisan serve
```

## Install on server

```bash
cd /home/trustreview/web/hirehelper.ai/app_repo
git pull origin main
/usr/bin/php8.4 artisan optimize:clear
/usr/bin/php8.4 /home/trustreview/.composer/composer dump-autoload
/usr/bin/php8.4 artisan config:cache
/usr/bin/php8.4 artisan route:cache
/usr/bin/php8.4 artisan view:cache
```
