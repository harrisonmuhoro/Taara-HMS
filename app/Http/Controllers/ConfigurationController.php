<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\BookingSource;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Floor;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Http\Request;
use App\Services\AuditService;

class ConfigurationController extends Controller
{
    public function index()
    {
        $this->authorize('settings.view');

        $branches = Branch::with(['departments', 'floors', 'roomTypes'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->whereKey(auth()->user()->branch_id))
            ->orderBy('name')
            ->get();
        $branchIds = $branches->pluck('id');

        return view('configuration.index', [
            'branches' => $branches,
            'departments' => Department::whereIn('branch_id', $branchIds)->with('branch')->orderBy('name')->get(),
            'floors' => Floor::whereIn('branch_id', $branchIds)->with('branch')->orderBy('floor_number')->get(),
            'roomTypes' => RoomType::whereIn('branch_id', $branchIds)->with('branch')->orderBy('name')->get(),
            'amenities' => Amenity::orderBy('name')->get(),
            'bookingSources' => BookingSource::whereIn('branch_id', $branchIds)->with('branch')->orderBy('name')->get(),
        ]);
    }

    public function storeBranch(Request $request)
    {
        $this->authorize('settings.manage');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:branches,code'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
        $data['hotel_id'] = Hotel::query()->value('id');
        $data['status'] = 'active';
        $branch = Branch::create($data);
        AuditService::log('BRANCH_CREATED', $branch, null, $branch->toArray());
        return back()->with('success', 'Branch created successfully.');
    }

    public function storeDepartment(Request $request)
    {
        $this->authorize('settings.manage');
        $data = $request->validate(['branch_id' => ['required', 'integer'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string']]);
        $this->ensureBranchAccess((int) $data['branch_id']);
        $department = Department::create($data);
        AuditService::log('DEPARTMENT_CREATED', $department, null, $department->toArray());
        return back()->with('success', 'Department created successfully.');
    }

    public function storeFloor(Request $request)
    {
        $this->authorize('settings.manage');
        $data = $request->validate(['branch_id' => ['required', 'integer'], 'floor_number' => ['required', 'integer', 'min:0'], 'name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string']]);
        $this->ensureBranchAccess((int) $data['branch_id']);
        $floor = Floor::create($data);
        AuditService::log('FLOOR_CREATED', $floor, null, $floor->toArray());
        return back()->with('success', 'Floor created successfully.');
    }

    public function storeRoomType(Request $request)
    {
        $this->authorize('settings.manage');
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_rate' => ['required', 'numeric', 'min:0'],
            'max_adults' => ['required', 'integer', 'min:1'],
            'max_children' => ['required', 'integer', 'min:0'],
            'bed_type' => ['required', 'string', 'max:100'],
            'bed_count' => ['required', 'integer', 'min:1'],
        ]);
        $this->ensureBranchAccess((int) $data['branch_id']);
        $data['status'] = 'active';
        $roomType = RoomType::create($data);
        AuditService::log('ROOM_TYPE_CREATED', $roomType, null, $roomType->toArray());
        return back()->with('success', 'Room type created successfully.');
    }

    public function storeAmenity(Request $request)
    {
        $this->authorize('settings.manage');
        $amenity = Amenity::create($request->validate(['name' => ['required', 'string', 'max:255', 'unique:amenities,name'], 'description' => ['nullable', 'string']]));
        AuditService::log('AMENITY_CREATED', $amenity, null, $amenity->toArray());
        return back()->with('success', 'Amenity created successfully.');
    }

    public function storeBookingSource(Request $request)
    {
        $this->authorize('settings.manage');
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:DIRECT,OTA,TRAVEL_AGENT,CORPORATE'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
        $this->ensureBranchAccess((int) $data['branch_id']);
        $data['status'] = 'active';
        $source = BookingSource::create($data);
        AuditService::log('BOOKING_SOURCE_CREATED', $source, null, $source->toArray());
        return back()->with('success', 'Booking source created successfully.');
    }

    private function ensureBranchAccess(int $branchId): void
    {
        abort_unless(
            auth()->user()->isSuperAdmin() || $branchId === (int) auth()->user()->branch_id,
            403
        );
        abort_unless(Branch::whereKey($branchId)->exists(), 422);
    }
}
