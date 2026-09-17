<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function recordPayment(array $data, int $userId): Payment
    {
        return DB::transaction(function () use ($data, $userId) {
            $invoice = Invoice::query()
                ->lockForUpdate()
                ->findOrFail($data['invoice_id']);
            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new \InvalidArgumentException('Payment amount must be greater than zero.');
            }

            if ($amount > (float) $invoice->balance_due) {
                throw new \InvalidArgumentException('Payment amount cannot exceed the invoice balance.');
            }

            $payment = Payment::create([
                'branch_id' => $invoice->branch_id,
                'invoice_id' => $invoice->id,
                'guest_id' => $invoice->guest_id,
                'payment_method_id' => $data['payment_method_id'],
                'amount' => $amount,
                'reference_number' => $data['reference_number'] ?? null,
                'transaction_date' => now(),
                'received_by' => $userId,
                'status' => 'COMPLETED',
                'notes' => $data['notes'] ?? null,
            ]);

            // Recalculate invoice balance
            $invoice->recalculateTotals();

            AuditService::log('PAYMENT_RECORDED', $payment, null, $payment->toArray());

            return $payment;
        });
    }
}
