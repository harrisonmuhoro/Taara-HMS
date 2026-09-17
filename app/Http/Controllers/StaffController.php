<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\AuditService;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Employee::class);

        $query = Employee::with(['department', 'user.roles'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(15)->withQueryString();

        return view('staff.index', compact('employees'));
    }

    public function create()
    {
        $this->authorize('create', Employee::class);

        $departments = Department::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('staff.form', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Employee::class);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date',
            
            // User Account Info
            'create_account' => 'boolean',
            'role_id' => 'required_if:create_account,1|nullable|exists:roles,id',
            'password' => 'required_if:create_account,1|nullable|min:8',
        ]);

        DB::transaction(function () use ($validated) {
            $empNumber = 'EMP-' . date('Ym') . '-' . str_pad(Employee::max('id') + 1, 3, '0', STR_PAD_LEFT);
            
            $employee = Employee::create([
                'branch_id' => auth()->user()->branch_id,
                'employee_number' => $empNumber,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'department_id' => $validated['department_id'],
                'position' => $validated['position'],
                'hire_date' => $validated['hire_date'],
                'employment_status' => 'active',
            ]);

            if (!empty($validated['create_account'])) {
                $user = User::create([
                    'branch_id' => auth()->user()->branch_id,
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'email' => $validated['email'] ?? strtolower($validated['first_name'] . '.' . $validated['last_name'] . '@hotel.com'),
                    'password' => Hash::make($validated['password']),
                    'status' => 'active',
                ]);

                $user->roles()->attach($validated['role_id']);
            }

            AuditService::log('STAFF_CREATED', $employee, null, $employee->toArray());
        });

        return redirect()->route('staff.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $staff)
    {
        $this->authorize('update', $staff);

        $staff->load('user.roles');
        $departments = Department::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('staff.form', [
            'employee' => $staff,
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, Employee $staff)
    {
        $this->authorize('update', $staff);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'employment_status' => 'required|in:active,inactive,terminated',
            
            // User Account Info
            'role_id' => 'nullable|exists:roles,id',
            'password' => 'nullable|min:8',
        ]);

        DB::transaction(function () use ($validated, $staff) {
            $oldValues = $staff->toArray();
            $staff->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'department_id' => $validated['department_id'],
                'position' => $validated['position'],
                'employment_status' => $validated['employment_status'],
            ]);

            if ($staff->user) {
                // Update existing user
                $staff->user->update([
                    'name' => $staff->full_name,
                    'email' => $validated['email'] ?? $staff->user->email,
                    'status' => $validated['employment_status'] === 'active' ? 'active' : 'inactive',
                ]);

                if (!empty($validated['password'])) {
                    $staff->user->update(['password' => Hash::make($validated['password'])]);
                }

                if (!empty($validated['role_id'])) {
                    $staff->user->roles()->sync([$validated['role_id']]);
                }
            } else if (!empty($validated['role_id'])) {
                // Create new user for existing employee
                $user = User::create([
                    'branch_id' => $staff->branch_id,
                    'employee_id' => $staff->id,
                    'name' => $staff->full_name,
                    'email' => $validated['email'] ?? strtolower($validated['first_name'] . '.' . $validated['last_name'] . '@hotel.com'),
                    'password' => Hash::make($validated['password'] ?? 'password123'),
                    'status' => $validated['employment_status'] === 'active' ? 'active' : 'inactive',
                ]);
                $user->roles()->attach($validated['role_id']);
            }

            AuditService::log('STAFF_UPDATED', $staff, $oldValues, $staff->fresh()->toArray());
        });

        return redirect()->route('staff.index')->with('success', 'Employee updated successfully.');
    }
}
