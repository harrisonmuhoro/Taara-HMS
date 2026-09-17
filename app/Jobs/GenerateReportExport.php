<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\SystemNotificationMail;

class GenerateReportExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $type, public int $userId, public ?int $branchId, public ?string $startDate = null, public ?string $endDate = null) {}

    public function handle(): void
    {
        $filename = "report-{$this->userId}-" . now()->format('YmdHis') . "-{$this->type}.csv";
        $path = "report-exports/{$filename}";
        $stream = fopen('php://temp', 'w+');

        if ($this->type === 'revenue') {
            fputcsv($stream, ['Invoice', 'Date', 'Guest', 'Amount']);
            Invoice::with('guest')->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
                ->where('status', 'PAID')->when($this->startDate, fn ($q) => $q->whereBetween('issued_at', [$this->startDate, $this->endDate]))
                ->cursor()->each(fn ($invoice) => fputcsv($stream, [$invoice->invoice_number, $invoice->issued_at?->toDateTimeString(), $invoice->guest?->full_name ?? 'N/A', $invoice->grand_total]));
        } else {
            fputcsv($stream, ['Product', 'SKU', 'Current Stock', 'Reorder Level', 'Unit']);
            Product::when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))->whereColumn('current_stock', '<=', 'reorder_level')
                ->cursor()->each(fn ($product) => fputcsv($stream, [$product->name, $product->sku, $product->current_stock, $product->reorder_level, $product->unit_measure]));
        }

        rewind($stream);
        Storage::disk('local')->put($path, stream_get_contents($stream));
        fclose($stream);

        $message = "Your {$this->type} report export is ready to download.";
        Notification::create(['user_id' => $this->userId, 'type' => 'REPORT_EXPORT_READY', 'title' => 'Report export ready', 'message' => $message, 'reference_type' => 'report_export', 'reference_id' => null]);
        if ($user = User::find($this->userId)) {
            Mail::to($user->email)->queue(new SystemNotificationMail('Report export ready', $message, $filename));
        }
    }
}
