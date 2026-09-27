# M-Pesa Advance Booking Deposit Integration

This document outlines the changes made to integrate M-Pesa for advance booking deposits in the Taara Hotel (formerly Grand Horizon Hotel) Management System.

## 1. Database Schema
- Created and executed a new migration: `2026_09_27_123000_add_reservation_id_to_mpesa_transactions_table.php`
- This migration adds a `reservation_id` foreign key to the `mpesa_transactions` table, enabling the linking of specific M-Pesa transactions directly to a room reservation for deposit purposes.

## 2. Eloquent Models
- **`app/Models/MpesaTransaction.php`**: Added a `reservation()` method to establish a `BelongsTo` relationship with the `Reservation` model.
- **`app/Models/Reservation.php`**: Added an `mpesaTransactions()` method to establish a `HasMany` relationship with the `MpesaTransaction` model.

## 3. Controllers & Backend Logic
- **`app/Http/Controllers/MpesaController.php`**: 
  - Updated `initiateStkPush` to accept and process an optional `reservation_id` when prompting the user for payment.
  - Modified `stkCallback` to locate the associated `Reservation` upon a successful M-Pesa transaction and automatically update the `deposit_amount` (and record a note in `special_requests` or a similar tracking field).
  - Adjusted `queryStatus` to return the `reservation_id` to the frontend for seamless redirection or status updates.
- **`app/Http/Controllers/ReservationsController.php`**: 
  - Updated the `store` method's validation rules to safely accept `deposit_amount` from the reservation creation form.

## 4. Background Services Standardization
- Standardized the project naming to "Taara Hotel" across `dev.bat` and `scripts/register-background-services.ps1` to ensure background queue workers (which handle M-Pesa callbacks) operate correctly under the new name.

## 5. Frontend Integration
- **Reservation Creation (`resources/views/reservations/create.blade.php`)**: Added an optional initial deposit amount field.
- **Reservation Details (`resources/views/reservations/show.blade.php`)**: Added a "Pay Deposit via M-Pesa" action in the financial summary. The action collects the guest phone number and amount, sends an STK push, polls the transaction status, and refreshes the reservation after a successful callback.

## 6. Payment Safety
- STK requests validate Kenyan phone-number formats and reject cancelled reservations.
- Reservation payments cannot exceed the outstanding balance.
- Callback handling locks the transaction and ignores duplicate successful callbacks, preventing deposits from being counted twice.

## 7. Verification
- All three M-Pesa migrations are applied.
- Blade view cache compilation succeeds.
- PHPUnit passes: 34 tests, 91 assertions.
- Pint still reports existing formatting violations across the wider repository; no broad formatting rewrite was applied.
