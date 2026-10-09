<?php

namespace App\Console\Commands;

use App\Http\Controllers\MpesaController;
use App\Models\MpesaTransaction;
use App\Services\MpesaService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Throwable;

class ReconcilePendingMpesaTransactions extends Command
{
    protected $signature = 'mpesa:reconcile-pending {--minutes=2} {--limit=100}';

    protected $description = 'Reconcile pending M-Pesa STK transactions through the Daraja STK Query API';

    public function handle(MpesaService $mpesa, MpesaController $controller): int
    {
        $cutoff = now()->subMinutes(max(2, (int) $this->option('minutes')));
        $limit = max(1, min(500, (int) $this->option('limit')));
        $processed = 0;

        MpesaTransaction::query()
            ->where('transaction_type', 'STK_PUSH')
            ->where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->whereNotNull('checkout_request_id')
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->each(function (MpesaTransaction $transaction) use ($mpesa, $controller, &$processed): void {
                try {
                    $result = $mpesa->queryStkPush($transaction->checkout_request_id);
                    $resultCode = (int) ($result['ResultCode'] ?? -1);
                    $payload = [
                        'Body' => [
                            'stkCallback' => [
                                'ResultCode' => $resultCode,
                                'ResultDesc' => (string) ($result['ResultDesc'] ?? 'Reconciled by STK Query'),
                                'CheckoutRequestID' => $transaction->checkout_request_id,
                            ],
                        ],
                    ];

                    if ($resultCode === 0) {
                        $payload['Body']['stkCallback']['CallbackMetadata']['Item'] = [
                            ['Name' => 'Amount', 'Value' => (string) $transaction->amount],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'STK:'.$transaction->checkout_request_id],
                            ['Name' => 'TransactionDate', 'Value' => now()->format('YmdHis')],
                        ];
                    }

                    $response = $controller->stkCallback(Request::create('/api/mpesa/callback', 'POST', $payload));
                    if ($response->getStatusCode() < 300) {
                        $processed++;
                    }
                } catch (Throwable $exception) {
                    report($exception);
                    $this->warn("Reconciliation failed for transaction {$transaction->id}.");
                }
            });

        $this->info("Reconciled {$processed} pending transaction(s).");

        return self::SUCCESS;
    }
}
