<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceTicket;
use App\Models\MaintenanceCategory;
use App\Models\MaintenanceComment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', MaintenanceTicket::class);

        $query = MaintenanceTicket::with(['room', 'category', 'reporter', 'assignee'])
            ->where('branch_id', auth()->user()->branch_id)
            ->latest('reported_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(15)->withQueryString();

        $stats = [
            'open' => MaintenanceTicket::where('branch_id', auth()->user()->branch_id)->where('status', 'OPEN')->count(),
            'in_progress' => MaintenanceTicket::where('branch_id', auth()->user()->branch_id)->where('status', 'IN_PROGRESS')->count(),
        ];

        return view('maintenance.index', compact('tickets', 'stats'));
    }

    public function create()
    {
        $this->authorize('create', MaintenanceTicket::class);

        $rooms = Room::where('branch_id', auth()->user()->branch_id)
                     ->orderBy('room_number')->get();
        $categories = MaintenanceCategory::orderBy('name')->get();

        return view('maintenance.create', compact('rooms', 'categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', MaintenanceTicket::class);

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'category_id' => 'required|exists:maintenance_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
        ]);

        MaintenanceTicket::create(array_merge($validated, [
            'branch_id' => auth()->user()->branch_id,
            'reported_by' => auth()->id(),
            'status' => 'OPEN',
            'reported_at' => now(),
        ]));

        return redirect()->route('maintenance.index')->with('success', 'Maintenance ticket created successfully.');
    }

    public function show(MaintenanceTicket $maintenance)
    {
        $this->authorize('view', $maintenance);

        $maintenance->load(['room', 'category', 'reporter', 'assignee', 'comments.user']);
        
        $maintenanceStaff = User::whereHas('roles', function($q) {
            $q->where('name', 'like', '%maintenance%');
        })->where('branch_id', auth()->user()->branch_id)->get();

        return view('maintenance.show', compact('maintenance', 'maintenanceStaff'));
    }

    public function update(Request $request, MaintenanceTicket $maintenance)
    {
        $this->authorize('update', $maintenance);

        $validated = $request->validate([
            'status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED,CLOSED,CANCELLED',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $updates = $validated;
        if ($validated['status'] === 'RESOLVED' && $maintenance->status !== 'RESOLVED') {
            $updates['resolved_at'] = now();
        }
        if ($validated['status'] === 'CLOSED' && $maintenance->status !== 'CLOSED') {
            $updates['closed_at'] = now();
        }

        $maintenance->update($updates);

        return back()->with('success', 'Ticket updated successfully.');
    }

    public function addComment(Request $request, MaintenanceTicket $maintenance)
    {
        $this->authorize('update', $maintenance);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $maintenance->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Comment added.');
    }
}
