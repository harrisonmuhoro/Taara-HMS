<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Services\AuditService;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Role::class);
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($validated);
        AuditService::log('ROLE_CREATED', $role, null, $role->toArray());

        return back()->with('success', 'Role created successfully.');
    }

    public function permissions(Role $role)
    {
        $this->authorize('update', $role);

        // Don't allow editing Super Admin permissions
        if ($role->name === 'Super Administrator') {
            return redirect()->route('roles.index')->with('error', 'Super Administrator permissions cannot be modified.');
        }

        $permissions = Permission::orderBy('name')->get();
        
        // Group permissions by prefix (e.g., 'reservations.view' -> 'reservations')
        $groupedPermissions = $permissions->groupBy(function($perm) {
            return explode('.', $perm->name)[0];
        });

        return view('roles.permissions', compact('role', 'groupedPermissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        if ($role->name === 'Super Administrator') {
            return back()->with('error', 'Cannot modify Super Administrator.');
        }

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $selectedPermissions = Permission::whereIn('id', $validated['permissions'] ?? [])->get();
        abort_unless(
            auth()->user()->isSuperAdmin() || $selectedPermissions->every(fn (Permission $permission) => auth()->user()->hasPermission($permission->name)),
            403,
            'You cannot grant permissions that you do not possess.'
        );

        $oldPermissions = $role->permissions()->pluck('permissions.id')->all();
        $permissionIds = $selectedPermissions->modelKeys();
        $role->permissions()->sync($permissionIds);
        AuditService::log('ROLE_PERMISSIONS_UPDATED', $role, ['permission_ids' => $oldPermissions], ['permission_ids' => $permissionIds]);

        return redirect()->route('roles.index')->with('success', 'Role permissions updated.');
    }
}
