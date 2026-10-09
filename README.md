# Taara Hotel Management System (Taara HMS)

<p align="center">
  <img src="public/taara-hms-mark.svg" alt="Taara HMS Logo" width="110"/>
</p>

<p align="center">
  <strong>An enterprise-grade, multi-branch Hotel Management System built for hospitality excellence, financial precision, and operational agility.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP Version" />
  <img src="https://img.shields.io/badge/Laravel-11.x%20%2F%2012.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel Framework" />
  <img src="https://img.shields.io/badge/TailwindCSS-v3-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Vite-Bundler-646CFF?style=flat-square&logo=vite&logoColor=white" alt="Vite" />
  <img src="https://img.shields.io/badge/M--Pesa-Daraja%202.0-00A651?style=flat-square" alt="M-Pesa Integration" />
  <img src="https://img.shields.io/badge/Queue-Redis%20%2F%20Database-DC382D?style=flat-square&logo=redis&logoColor=white" alt="Redis Queue" />
  <img src="https://img.shields.io/badge/Testing-PHPUnit%20%7C%20Pest-4E73DF?style=flat-square" alt="Testing" />
  <img src="https://img.shields.io/badge/License-Proprietary-gray?style=flat-square" alt="License" />
</p>

---

## 📖 Overview

**Taara HMS** is an all-in-one hospitality management platform designed to unify hotel front-desk operations, multi-room reservations, housekeeping, food and beverage (F&B) point of sale, inventory procurement, financial accounting, and guest communications under a single, cohesive dashboard.

Engineered with performance, data integrity, and strict access control in mind, Taara HMS handles complex multi-property hotel chains down to boutique single-location properties, featuring automated room-folio accounting and native Safaricom M-Pesa integration.

---

## 🌟 Key Functional Modules

### 🏢 1. Multi-Branch & Property Hierarchy
* **Branch Isolation**: Manage multiple physical properties or hotel branches (`Branch`), with cross-branch data access guards.
* **Property Topography**: Structure buildings into Departments, Floors, Room Types (e.g. Deluxe, Executive Suite, Penthouse), and assign configurable Amenities.
* **Custom Number Sequencing**: Atomic document sequence generator (`DocumentNumberService`) guaranteeing gapless, unique numbering across Invoices, Receipts, Stays, and Orders.

### 🛏️ 2. Reservations & Front Desk
* **Interactive Booking Calendar**: Visual timeline of room occupancy, incoming check-ins, departures, and maintenance holds.
* **Booking Deposit Engine**: Configurable deposit requirements with holding deadlines, automatic hold expiration, deposit waivers, and extension requests.
* **Seamless Front-Desk Workflows**:
  * **Check-In**: Guest verification, document attachment (ID/Passport), dynamic room allocation, and folio initialization.
  * **Check-Out**: Automated folio balance validation, payment clearance checks, keycard return, and instant room status update to "Dirty/Needs Cleaning".
* **Guest Profiles & Documents**: Guest CRM tracking stay histories, preferences, total expenditure, and secure encrypted document storage.

### 🧹 3. Housekeeping & Facility Maintenance
* **Room State Machine**: Real-time room status tracking (`clean`, `dirty`, `inspecting`, `maintenance`, `out_of_order`).
* **Task Assignment & Supervision**: Assign cleaning personnel to rooms, track turnaround times, and log supervisor inspections.
* **Maintenance Ticketing**: Log maintenance requests categorized by severity and department, assign technicians, track repair lifecycles, and maintain threaded resolution comments.

### 🍔 4. Restaurant POS & F&B Operations
* **Point of Sale Terminal (`/restaurant/pos`)**: High-speed, responsive touch-friendly POS designed for restaurant cashiers and waitstaff.
* **Menu Engineering**: Catalog dishes and beverages with custom categories, pricing, descriptions, and availability toggles.
* **Flexible POS Settlement**: Settle diner checks via:
  * **Direct Cash & Credit Card**
  * **Charge-to-Room (Folio Posting)**: Directly post food and beverage tabs to the guest's active hotel stay folio.
  * **Instant M-Pesa STK Push**: Send a real-time mobile payment prompt directly to the customer's phone from the POS terminal.

### 💳 5. Billing, Finance & Expense Control
* **Stay Folio Accounting**: Transactional accounting recording room tariffs, mini-bar, restaurant bills, laundry, and extra services in real time.
* **Invoicing & PDF Receipts**: Generate itemized, professional invoices with printable PDF exports powered by DomPDF.
* **Multi-Method Payments**: Record and reconcile Cash, Bank Wire, Credit Card, and Mobile Money transactions.
* **Expense Management**: Multi-tiered expense requests with receipt/invoice attachments, department tagging, and manager approval workflows.
* **Refund Control**: Audit-backed refund processing tied to original payment receipts.

### 📱 6. Safaricom M-Pesa Integration (Daraja 2.0)
* **Lipa Na M-Pesa Online (STK Push)**:
  * Trigger automated PIN prompts directly to guest mobile devices from invoice pages and POS checkouts.
  * Live status polling modal communicating with Daraja API asynchronously.
* **Customer-to-Business (C2B) Collections**: Validation and confirmation endpoints for Paybill and Till Number transactions.
* **Automated Reconciliation**: Scheduled background commands reconcile pending STK push requests and sync payment balances atomically.
* **STK Anti-Abuse Controls**: Built-in rate limits (5 pushes/min per user, 3 pushes/10-min per phone number) and webhook IP verification middleware (`mpesa.webhook`).

### 📦 7. Inventory & Supply Chain
* **Product Catalog & Stock Tracking**: Track operational supplies, food items, toiletries, and cleaning chemicals with low-stock alerts.
* **Purchasing Flow**: Supplier management, purchase order generation, goods-received workflows, and unit cost adjustments.
* **Stock Adjustments**: Log physical count discrepancies, shrinkage, and damage write-offs with audit justifications.

### 📊 8. Analytics & Queued Reporting
* **Executive Dashboards**: Occupancy rates, revenue trends, ADR (Average Daily Rate), RevPAR (Revenue Per Available Room), and POS sales charts powered by Chart.js.
* **Asynchronous Heavy Exports**: Large CSV and PDF data exports run in background queues to keep the web application lightning fast.

### 🔒 9. Enterprise Security & RBAC
* **Role-Based Access Control (RBAC)**: Fine-grained permissions grouped by modules (`dashboard`, `guests`, `rooms`, `reservations`, `stays`, `folios`, `invoices`, `payments`, `restaurant`, `inventory`, `housekeeping`, `maintenance`, `reports`, `staff`, `settings`, `audit`).
* **Multi-Tier Rate Limiting**: 5 failed login attempts per 15 minutes per account; 20 attempts per 15 minutes per IP.
* **Admin Security Override**: Emergency account unlock endpoint (`/admin/unlock-user`) for authorized administrators.
* **CSV Formula Injection Guards**: Strict sanitization of user-submitted data during report generation.
* **Session Invalidation**: Instant session termination and token revocation upon user deactivation (`EnsureUserIsActive`).
* **Comprehensive Audit Trail**: Immutable logging (`AuditLog`) of all financial events, reservation changes, and user status modifications.

---

## 🛠️ Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend Framework** | Laravel 11.x / 12.x on PHP 8.3+ |
| **Frontend Architecture**| Blade Templating, Alpine.js, Alpine Focus, Vanilla JS, TypeScript |
| **Styling & Design** | Tailwind CSS v3, Tailwind Forms, PostCSS, Autoprefixer |
| **Asset Bundler** | Vite 8.x (`laravel-vite-plugin`) |
| **Database Engines** | MySQL 8.0+ / MariaDB 10.6+ (compatible with PostgreSQL & SQLite) |
| **Caching & Queues** | Redis (via `phpredis`) / Database Queue Driver |
| **Queue Monitoring** | Laravel Horizon |
| **Document Generation** | DomPDF (`barryvdh/laravel-dompdf`) |
| **Email Service** | Resend API (`resend/resend-php`) |
| **Automated Backups** | Spatie Laravel Backup (`spatie/laravel-backup`) |
| **Mobile Money** | Safaricom Daraja API (STK Push, C2B Paybill/Till) |
| **Testing Suite** | PHPUnit 12.x, Pest PHP, Laravel Dusk / Browser Testing |

---

## 👥 Default Seeded Accounts & Roles

When you seed the database (`php artisan migrate --seed`), default operational personas and roles are generated for immediate evaluation:

> **Default Password for all seeded accounts**: `password123`

| Email | Name | Role | Department | Key Capabilities |
| :--- | :--- | :--- | :--- | :--- |
| `admin@example.test` | Alexander Vance | **Super Administrator** | Administration | Full unconstrained platform & RBAC access |
| `manager@example.test` | Sophia Otieno | **Hotel Manager** | Administration | Property oversight, expense approvals, reports |
| `reception@example.test` | David Wanjiku | **Front Desk / Receptionist**| Front Desk | Check-ins, check-outs, folios, payments |
| `housekeeping@example.test`| Grace Mwangi | **Housekeeping Supervisor** | Housekeeping | Room status updates, task assignment, inspections |
| `accountant@example.test` | Michael Kamau | **Accountant / Finance** | Finance | Invoicing, payments, refunds, expense approvals |
| `restaurant@example.test` | Brian Ochieng | **Restaurant Cashier** | Food & Beverage | POS terminal, orders, room charges, M-Pesa |
| `inventory@example.test` | Esther Njeri | **Inventory Officer** | Inventory | Stock movements, purchases, supplier management |
| `maintenance@example.test` | Joseph Kipchumba | **Maintenance Staff** | Maintenance | Ticket resolution, comments, facility upkeep |

---

## 🚀 Installation & Local Development

### 1. System Prerequisites
* **PHP**: 8.3 or higher with extensions: `pdo_mysql`, `mbstring`, `openssl`, `intl`, `curl`, `gd`, `zip`
* **Composer**: 2.x
* **Database**: MySQL 8.0+ or MariaDB 10.6+
* **Node.js**: Node 20.x or higher with NPM
* **Redis** (optional, recommended for production queues & caching)

### 2. Clone the Repository
```bash
git clone https://github.com/harrisonmuhoro/Taara-HMS.git taara-hms
cd taara-hms
```

### 3. Install Backend & Frontend Dependencies
```bash
composer install
npm install
```

### 4. Configure Environment
```bash
# On Unix / macOS
cp .env.example .env

# On Windows PowerShell
copy .env.example .env

# Generate application key
php artisan key:generate
```

Open `.env` and configure your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_management
DB_USERNAME=root
DB_PASSWORD=your_database_password
```

### 5. Run Migrations & Seed Database
```bash
php artisan migrate --seed
```

### 6. Build Assets & Start Development Server

Run both Vite asset compilation and the Laravel web server:

```bash
# In terminal 1: Start frontend Vite server
npm run dev

# In terminal 2: Start PHP development server
php artisan serve
```

Alternatively, use the built-in composer dev runner:
```bash
composer dev
```

Visit the application at `http://localhost:8000`.

---

## ⚙️ Environment Configuration Reference

### Safaricom M-Pesa Daraja Configuration
Add the following credentials to your `.env` file to enable mobile payments:

```env
# M-Pesa Environment ('sandbox' or 'live')
MPESA_ENV=sandbox
MPESA_CONSUMER_KEY=your_daraja_consumer_key
MPESA_CONSUMER_SECRET=your_daraja_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
MPESA_CALLBACK_URL=https://your-public-domain.com/api/mpesa/callback

# C2B Validation & Confirmation Webhook URLs (Path must not include 'mpesa')
MPESA_C2B_VALIDATION_URL=https://your-public-domain.com/c2b/validate
MPESA_C2B_CONFIRMATION_URL=https://your-public-domain.com/c2b/confirm

# Comma-separated Safaricom IP whitelist (Optional in development, recommended in production)
MPESA_WEBHOOK_IPS=196.201.214.200,196.201.214.206,196.201.213.114
```

> [!TIP]
> **Local Webhook Testing**: When testing M-Pesa STK push or C2B callbacks locally, expose port 8000 via a secure tunnel (e.g. `ngrok http 8000` or Expose) and update `MPESA_CALLBACK_URL` with your public HTTPS forwarding address.

### Email Service (Resend)
```env
MAIL_MAILER=resend
RESEND_API_KEY=re_123456789abcdef
MAIL_FROM_ADDRESS="reservations@yourhotel.com"
MAIL_FROM_NAME="Taara Hotel Management System"
```

### Backup Configuration
```env
BACKUP_ARCHIVE_PASSWORD=secure-archive-passphrase
BACKUP_DISKS=local
BACKUP_NOTIFY_EMAIL=admin@yourhotel.com
```

---

## 📡 API & M-Pesa Webhook Endpoints

| Method | Endpoint | Middleware | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/mpesa/stkpush/initiate` | `auth`, `can:payments.collect`, `throttle:mpesa-stk` | Triggers Lipa Na M-Pesa Online STK Push for an invoice or POS order |
| `GET` | `/api/mpesa/status/{checkoutRequestId}` | `auth`, `throttle:mpesa-status` | Real-time status query for an active STK transaction |
| `GET` | `/api/mpesa/c2b/register` | `auth`, `verified` | Registers C2B Validation and Confirmation URLs with Safaricom |
| `POST` | `/api/mpesa/callback` | `mpesa.webhook` | Asynchronous webhook receiver for STK Push callback from Safaricom |
| `POST` | `/c2b/validate` | `mpesa.webhook` | Safaricom C2B real-time account validation hook |
| `POST` | `/c2b/confirm` | `mpesa.webhook` | Safaricom C2B payment confirmation & ledger posting hook |

---

## ⏱️ Background Workers & Scheduled Tasks

Taara HMS relies on background processing for queued reports, email notifications, automatic no-show cancellations, and transaction reconciliation.

### Artisan Scheduled Commands
Registered in [routes/console.php](file:///d:/wamp64/www/hotel/routes/console.php):

| Schedule | Command | Functionality |
| :--- | :--- | :--- |
| **Every Minute** | `mpesa:reconcile-pending --minutes=2` | Reconciles unconfirmed STK push transactions against Daraja API |
| **Daily at 01:00**| `reservations:mark-no-shows` | Auto-cancels unconfirmed holds and marks past-due arrivals as No-Show |
| **Daily at 02:00**| `backup:run --only-db` | Performs automated encrypted database backup |
| **Daily at 02:30**| `backup:clean` | Purges aged backup archives according to retention policy |
| **Daily at 06:00**| `hotel:send-operational-alerts` | Dispatches morning briefing of check-ins, VIPs, and maintenance alerts |

### Running Workers Locally
```bash
# Run queue worker for PDF exports and emails
php artisan queue:work database --sleep=3 --tries=3

# Run scheduler worker
php artisan schedule:work
```

### Windows Server Service Deployment
For persistent operation on Windows Server or WampServer installations, use the included automation scripts in `scripts/`:

```powershell
# Open Administrator PowerShell
Set-Location D:\wamp64\www\hotel
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\scripts\register-background-services.ps1
```

This registers Windows Scheduled Tasks for both the queue worker and the scheduler with automatic restart upon failure.

---

## 🛡️ Security Architecture

* **Multi-Layer Throttling**: Global request throttling combined with pinpoint rate limiters on payment endpoints and authentication routes.
* **Tenant & Branch Boundaries**: Controllers and queries validate branch ownership before mutations to prevent cross-tenant data leaks.
* **Formula Injection Prevention**: CSV export data streams strip or quote leading formula triggers (`=`, `+`, `-`, `@`) preventing spreadsheet exploits.
* **HTTP Security Headers**: Pre-configured middleware injects `HSTS`, `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and restrictive referrer policies.
* **Audit Trails**: Every security, financial, and reservation event is persisted with actor user ID, client IP address, and timestamp.

---

## 🧪 Testing & Code Quality

Taara HMS includes an extensive test suite validating security policies, financial calculations, check-in/out state machines, and rate-limit boundaries.

```bash
# Run entire test suite
php artisan test

# Run security-specific test suite
php artisan test tests/Feature/Security

# Run code style fixer (Laravel Pint)
./vendor/bin/pint --test
```

---

## 📂 Project Directory Structure

```text
hotel/
├── app/
│   ├── Console/Commands/       # Scheduled CLI commands (No-shows, M-Pesa reconciliation)
│   ├── Http/
│   │   ├── Controllers/        # Domain controllers (FrontDesk, POS, Finance, etc.)
│   │   └── Middleware/         # Security headers, webhook verification, active user checks
│   ├── Models/                 # Eloquent entities (Reservation, Folio, Invoice, Stay, etc.)
│   └── Services/               # Domain business logic (CheckIn, CheckOut, M-Pesa, Inventory)
├── bootstrap/                  # Framework bootstrapping & middleware pipeline
├── config/                     # Application configurations (Backup, Services, Horizon)
├── database/
│   ├── factories/              # Test factories
│   ├── migrations/             # Schema definitions
│   └── seeders/                # Comprehensive demo database seeders
├── public/                     # Public web assets and SVG logos
├── resources/
│   ├── css/                    # Tailwind CSS configuration & stylesheets
│   ├── js/                     # Frontend modules & Alpine components
│   └── views/                  # Blade templates (Dashboard, FrontDesk, POS, Invoices)
├── routes/
│   ├── api.php                 # M-Pesa callbacks & REST hooks
│   ├── auth.php                # Authentication routes
│   ├── console.php             # Scheduled task cron definitions
│   └── web.php                 # Core application routes
├── scripts/                    # Windows background service registration scripts
└── tests/                      # Feature, Unit, and Security test suites
```

---

## 📜 License & Intellectual Property

This software and related documentation are proprietary and confidential. Unauthorized copying, distribution, modification, or deployment of this software without explicit authorization from the copyright holder is strictly prohibited.

---

<p align="center">
  Crafted with care for world-class hospitality management.
</p>
