<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('audit_logs.view');

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = AuditLog::with(['user', 'branch'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', auth()->user()->branch_id))
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('action', 'like', "%{$search}%")
                        ->orWhere('entity_type', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($validated['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($validated['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        $actions = AuditLog::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', auth()->user()->branch_id))
            ->distinct()->orderBy('action')->pluck('action');

        return view('audit-logs.index', compact('logs', 'actions'));
    }

    public function export(Request $request)
    {
        $this->authorize('audit_logs.view');

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $logs = AuditLog::with(['user', 'branch'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', auth()->user()->branch_id))
            ->when($validated['search'] ?? null, function ($query, $search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('action', 'like', "%{$search}%")
                        ->orWhere('entity_type', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($validated['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($validated['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($validated['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest('created_at')
            ->get();

        return response()->streamDownload(function () use ($logs) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Time', 'Action', 'Entity', 'Entity ID', 'User', 'Branch', 'IP Address']);
            foreach ($logs as $log) {
                fputcsv($output, [
                    $log->created_at?->toDateTimeString(),
                    $log->action,
                    class_basename($log->entity_type),
                    $log->entity_id,
                    $log->user?->name ?? 'System',
                    $log->branch?->name ?? 'All branches',
                    $log->ip_address,
                ]);
            }
            fclose($output);
        }, 'audit-logs-' . now()->format('Y-m-d-His') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
