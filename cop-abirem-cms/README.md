# Kerith

> *"Thou shalt drink of the brook… and I have commanded the ravens to feed thee there."* — 1 Kings 17:4

Kerith is a church management system built with Laravel 12. It handles member records, tithes, offerings, donations, pledges, attendance, welfare, funeral contributions, and ministry finances — giving church administrators a single place to steward their congregation well.

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js 20+ / npm

## Installation

```bash
git clone <repo-url> kerith
cd kerith
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# Configure .env (DB credentials, APP_URL, mail, SMS)
php artisan migrate --seed
```

## Scheduler

Add a single cron entry to run scheduled tasks (birthday SMS, auto-backup):

```
* * * * * cd /path/to/kerith && php artisan schedule:run >> /dev/null 2>&1
```

## Configuration

After installation, log in as admin and visit **Settings** to configure:

- Church name, logo, address
- Currency and date format
- SMS gateway (GiantSMS)
- Backup schedule

## License

Proprietary. All rights reserved.
