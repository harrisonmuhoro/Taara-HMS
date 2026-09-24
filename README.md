# Taara Hotel Management System (Taara HMS)

<p align="center">
  <img src="public/taara-hms-mark.svg" alt="Taara HMS Logo" width="100"/>
</p>

An enterprise-grade, comprehensive Hotel Management software built for scalability, security, and efficiency. Designed as a server-rendered multi-page application, Taara HMS provides complete control over property management, from front-desk operations to back-office financial reporting.

---

## 🌟 Key Features

*   **🏢 Multi-Branch Configuration**: Manage multiple properties, departments, floors, room types, and amenities from a central dashboard.
*   **👥 Staff & Role Management**: Granular permissions (SuperAdmin, PosPolicy, StaffPolicy) ensuring strict access control across different operational tiers.
*   **🛏️ Reservations & Front Desk**: Real-time calendar views, dynamic check-in/check-out processes, stay folios, and seamless booking source tracking.
*   **🧹 Housekeeping & Maintenance**: Track room statuses, assign cleaning staff, and log maintenance tickets with comment tracking.
*   **💳 Finance & Billing**: Generate professional invoices, handle multi-currency payments, track expenses, and manage refunds seamlessly.
*   **🍔 Restaurant POS & Menu**: Integrated point-of-sale system for hotel restaurants with checkout to room or direct payment.
*   **📦 Inventory & Purchasing**: Full supply-chain management including suppliers, purchases, products, and stock adjustments.
*   **📊 Advanced Reporting**: Real-time revenue insights, inventory tracking, and asynchronous queued exports to PDF/CSV.
*   **🔔 Real-time Notifications & Audit Logs**: Full audit trails for security tracking and in-app staff notifications.

## 🛠️ Technology Stack

*   **Backend**: Laravel 11.x (PHP 8.3+)
*   **Frontend**: Blade Templating, TypeScript, Vanilla JS, Tailwind CSS v3
*   **Build Tool**: Vite
*   **Database**: MySQL 8+ / MariaDB 10.6+
*   **Authentication**: Laravel Breeze / Custom rate-limiting strategies
*   **Emailing**: Resend API

---

## 🔒 Security Posture

This system implements robust, modern security standards:
*   **Multi-Tier Rate Limiting**: 5 attempts per 15 minutes per Account, 20 attempts per 15 minutes per IP.
*   **Auto-Lockouts**: 15-minute soft locks on suspicious activity with Admin Manual Override endpoints.
*   **Security Headers**: Pre-configured `X-Frame-Options`, `X-XSS-Protection`, `Strict-Transport-Security`, and `Referrer-Policy`.
*   **Logging & Alerting**: Off-hours login tracking, IP tracking on failed attempts, and robust audit logging on all financial mutations.

---

## 💻 Installation & Setup

### 1. Prerequisites
- PHP 8.3+ with `PDO`, `Mbstring`, `OpenSSL`, and `Intl` extensions
- Composer 2.x
- MySQL 8+ or MariaDB
- Node.js 20+ & NPM

### 2. Clone and Install
```bash
git clone <your-repository-url> taara-hms
cd taara-hms

# Install PHP and Node dependencies
composer install
npm install
```

### 3. Environment Configuration
```bash
# Copy the example environment file
cp .env.example .env
# Windows users: copy .env.example .env

# Generate application key
php artisan key:generate
```
Open the `.env` file and configure your database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and mailer settings (e.g., `RESEND_API_KEY`).

### 4. Database Initialization
```bash
# Run migrations and seed the database with required default data/roles
php artisan migrate --seed
```

### 5. Build Assets & Run
```bash
# Build Vite assets
npm run build

# Start the local development server
php artisan serve
```

---

## ⚙️ Background Services (Queues & Scheduler)

Taara HMS relies on background processing for tasks like PDF exports, email dispatching, and marking 'No-Shows' automatically.

### Running Manually
Open a separate terminal and run the queue worker:
```bash
php artisan queue:work database --sleep=3 --tries=3
```
Run the scheduler manually (for testing):
```bash
php artisan schedule:work
```

### Windows Server Automatic Startup
If hosting on a Windows Server environment, register the background services to run automatically on logon. Run this from an **Administrator PowerShell** window:
```powershell
Set-Location D:\path\to\taara-hms
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\scripts\register-background-services.ps1
```

---

## 🧪 Testing

The project utilizes PHPUnit and Pest for robust testing of financial integrity and concurrency.
```bash
php artisan test
```

## 📜 License & Usage

This is a private, proprietary Hotel Management Software. Distribution, modification, and deployment must adhere to the internal organization's licensing policies.
