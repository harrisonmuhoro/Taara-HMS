<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\GenerateReportExport;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        Gate::authorize('reports.view');

        [$startDate, $endDate] = $this->dateRange($request);
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;

        $metrics = $this->reportService->getDashboardMetrics($branchId, $startDate, $endDate);
        
        return view('reports.index', compact('metrics', 'startDate', 'endDate'));
    }

    public function revenue(Request $request)
    {
        Gate::authorize('reports.view');

        [$startDate, $endDate] = $this->dateRange($request);
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;

        $revenueData = $this->reportService->getRevenueData($branchId, $startDate, $endDate);
        
        // Group by category (simplified for now as invoices)
        $invoices = Invoice::with('guest')
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->whereBetween('issued_at', [$startDate, $endDate])
            ->latest('issued_at')
            ->paginate(15)
            ->withQueryString();

        return view('reports.revenue', compact('revenueData', 'invoices', 'startDate', 'endDate'));
    }

    public function inventory(Request $request)
    {
        Gate::authorize('reports.view');
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;

        $totalValuation = $this->reportService->getInventoryValuation($branchId);
        $lowStockProducts = $this->reportService->getLowStockProducts($branchId);

        return view('reports.inventory', compact('totalValuation', 'lowStockProducts'));
    }

    public function exportRevenue(Request $request)
    {
        Gate::authorize('reports.view');
        [$startDate, $endDate] = $this->dateRange($request);
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $invoices = Invoice::with('guest')
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->where('status', 'PAID')->whereBetween('issued_at', [$startDate, $endDate])
            ->latest('issued_at')->get();

        return response()->streamDownload(function () use ($invoices) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Invoice', 'Date', 'Guest', 'Amount']);
            foreach ($invoices as $invoice) {
                fputcsv($output, [$invoice->invoice_number, $invoice->issued_at?->toDateTimeString(), $invoice->guest?->full_name ?? 'N/A', $invoice->grand_total]);
            }
            fclose($output);
        }, 'revenue-report-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportInventory()
    {
        Gate::authorize('reports.view');
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $products = $this->reportService->getLowStockProducts($branchId);

        return response()->streamDownload(function () use ($products) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Product', 'SKU', 'Current Stock', 'Reorder Level', 'Unit']);
            foreach ($products as $product) {
                fputcsv($output, [$product->name, $product->sku, $product->current_stock, $product->reorder_level, $product->unit_measure]);
            }
            fclose($output);
        }, 'inventory-report-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportRevenuePdf(Request $request)
    {
        Gate::authorize('reports.view');
        [$startDate, $endDate] = $this->dateRange($request);
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $invoices = Invoice::with('guest')->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->where('status', 'PAID')->whereBetween('issued_at', [$startDate, $endDate])->latest('issued_at')->get();

        return Pdf::loadView('reports.pdf.revenue', compact('invoices', 'startDate', 'endDate'))
            ->download('revenue-report-' . now()->format('Y-m-d-His') . '.pdf');
    }

    public function exportInventoryPdf()
    {
        Gate::authorize('reports.view');
        $branchId = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
        $lowStockProducts = $this->reportService->getLowStockProducts($branchId);
        $totalValuation = $this->reportService->getInventoryValuation($branchId);

        return Pdf::loadView('reports.pdf.inventory', compact('lowStockProducts', 'totalValuation'))
            ->download('inventory-report-' . now()->format('Y-m-d-His') . '.pdf');
    }

    public function queueExport(Request $request, string $type)
    {
        Gate::authorize('reports.view');
        abort_unless(in_array($type, ['revenue', 'inventory'], true), 404);
        [$startDate, $endDate] = $type === 'revenue' ? $this->dateRange($request) : [null, null];
        GenerateReportExport::dispatch($type, auth()->id(), auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id, $startDate, $endDate);

        return back()->with('success', 'The report export has been queued. You will receive a notification when it is ready.');
    }

    public function downloadQueuedExport(Request $request, string $filename)
    {
        Gate::authorize('reports.view');
        abort_unless((bool) preg_match('/^report-' . preg_quote((string) auth()->id(), '/') . '-\d{14}-(revenue|inventory)\.csv$/', $filename), 404);
        abort_unless(Storage::disk('local')->exists("report-exports/{$filename}"), 404);

        return Storage::disk('local')->download("report-exports/{$filename}", $filename);
    }

    private function dateRange(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        return [
            $validated['start_date'] ?? Carbon::now()->startOfMonth()->format('Y-m-d'),
            $validated['end_date'] ?? Carbon::now()->endOfMonth()->format('Y-m-d'),
        ];
    }
}
