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
*   **📱 M-Pesa Mobile Payments**: Native integration with Safaricom Daraja API for automated STK Push (Lipa Na M-Pesa Online) directly from invoice views, alongside C2B (Customer-to-Business) payment reconciliation.
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
*   **Payment Gateway**: Safaricom Daraja API (M-Pesa STK Push & C2B)
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
git clone https://github.com/harrisonmuhoro/Taara-HMS taara-hms
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
Open the `.env` file and configure your database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), mailer (`RESEND_API_KEY`), and M-Pesa settings:

```env
# Safaricom M-Pesa Daraja Configuration
MPESA_ENV=sandbox                                # sandbox or live
MPESA_CONSUMER_KEY=your_daraja_consumer_key
MPESA_CONSUMER_SECRET=your_daraja_consumer_secret
MPESA_SHORTCODE=your_business_shortcode          # e.g., 174379 for sandbox
MPESA_PASSKEY=your_lipa_na_mpesa_online_passkey
MPESA_CALLBACK_URL=https://your-domain.com/api/mpesa/callback

# C2B Configuration (Optional / Paybill / Till)
MPESA_C2B_VALIDATION_URL=https://your-domain.com/c2b/validate
MPESA_C2B_CONFIRMATION_URL=https://your-domain.com/c2b/confirm
```

> [!NOTE]
> For local testing with Safaricom Daraja API webhooks, use a tunnel tool such as ngrok or expose (`ngrok http 8000`) and set `MPESA_CALLBACK_URL` to your public forwarding URL.

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

## 📱 M-Pesa Integration (Daraja API)

Taara HMS includes out-of-the-box integration with Safaricom M-Pesa for automated guest bill settlements and mobile money collections:

### Features
*   **Lipa Na M-Pesa Online (STK Push)**: Front-desk agents and guests can trigger an instant M-Pesa PIN prompt directly from the interactive invoice page (`/finance/invoices/{id}`).
*   **Automated Payment Reconciliation**: When the guest confirms their PIN, Safaricom's webhook updates the transaction record, generates a completed payment entry against the invoice, and recalculates outstanding balances atomically.
*   **Customer-to-Business (C2B)**: Paybill/Till number validation and confirmation endpoints for real-time payment capture.
*   **Audit Trail & Logging**: All attempts, Safaricom checkout IDs, receipt numbers, and callback payloads are logged in the `mpesa_transactions` table.

### API & Webhook Endpoints

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/api/mpesa/stkpush/initiate` | Initiates an STK Push prompt to the guest's mobile number. |
| `POST` | `/api/mpesa/callback` | Safaricom STK Push asynchronous callback webhook. |
| `GET` | `/api/mpesa/c2b/register` | Utility endpoint to register C2B validation and confirmation URLs with Safaricom. |
| `POST` | `/c2b/validate` | C2B payment validation hook (*path formatted without `mpesa` per Safaricom requirements*). |
| `POST` | `/c2b/confirm` | C2B payment confirmation hook. |

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
