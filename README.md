# Grand Horizon Hotel Management System

Enterprise hotel management software built with Laravel, Blade, TypeScript, Tailwind CSS, and Vite as a server-rendered multi-page application.

## Overview

The system covers hotel configuration, branches, staff and roles, guests, rooms, reservations, availability, check-in/check-out, stays, folios, invoices, payments, refunds, housekeeping, maintenance, restaurant POS, inventory, purchasing, expenses, notifications, audit logs, reports, and PDF/CSV exports.

## Requirements

- PHP 8.2+ with PDO MySQL, Mbstring, OpenSSL, and Intl extensions
- Composer
- MySQL 8+ or MariaDB 10.6+
- Node.js 20+ and npm
- A web server or Laravel Herd

## Installation

```bash
composer install
npm install
copy .env.example .env       # Windows
cp .env.example .env         # macOS/Linux
php artisan key:generate
php artisan migrate --seed
npm run build
```

For development, run `php artisan serve` and `npm run dev` in separate terminals.

## Environment configuration

Configure `APP_URL`, the `DB_*` values, and a secure `APP_KEY`. For email notifications:

```env
MAIL_MAILER=resend
RESEND_API_KEY=your_resend_api_key
MAIL_FROM_ADDRESS=reservations@your-verified-domain.example
MAIL_FROM_NAME="Grand Horizon Hotel"
QUEUE_CONNECTION=database
```

Use a verified Resend domain for real guest email. Never commit `.env` or API keys. The `onboarding@resend.dev` sender is for development and may have recipient restrictions and poor deliverability.

## Database

```bash
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed   # local only; destroys local data
```

Never edit an already-applied production migration; create a new migration for schema changes.

## Queues and scheduler

Reservation confirmations and report-export notifications use the queue:

```bash
php artisan queue:work database --sleep=3 --tries=3
```

The scheduler marks overdue confirmed reservations as `NO_SHOW` daily. Run it with:

```bash
php artisan schedule:work
```

Or configure a server cron entry to run `php artisan schedule:run` every minute. Useful diagnostics:

```bash
php artisan reservations:mark-no-shows --dry-run
php artisan hotel:send-operational-alerts
php artisan queue:failed
php artisan queue:retry all
php artisan schedule:list
```

### Windows automatic startup

Register the queue worker and scheduler once from an **Administrator PowerShell** window:

```powershell
Set-Location D:\wamp64\www\hotel
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\scripts\register-background-services.ps1
```

The installer registers Windows logon automation. If Task Scheduler is restricted by policy, it falls back to the current user's startup registry. The services run hidden and write diagnostic output to `storage/logs/`.

## Demo accounts

Run the seeders to create demo users and roles. Credentials are defined in `DatabaseSeeder`. Change all demo passwords before deployment.

## Development and testing

```bash
php artisan route:list
php artisan optimize:clear
npm run dev
npm run build
php artisan test
```

Add feature, authorization, financial-integrity, concurrency, and cross-branch isolation tests for new workflows.

## Security and deployment

Keep secrets, logs, private uploads, `vendor/`, and `node_modules/` out of Git. Use policies, permissions, CSRF protection, branch scoping, private file storage, transactions, and row locks for critical mutations. Production must use `APP_DEBUG=false`, HTTPS, secure cookies, protected logs/uploads, configured backups, queues, and scheduler monitoring.

Back up the MySQL database and private uploads separately, outside the public web root, and test restoration regularly.

## Troubleshooting email

Confirm the Resend key and verified sender, run `php artisan config:clear`, ensure a queue worker is running, inspect `php artisan queue:failed`, and check the Resend dashboard. SPF, DKIM, and DMARC records improve deliverability; Gmail may still place messages in Spam.

## License

This is private hotel-management software. Add the organization’s licensing and deployment policy before public distribution.
