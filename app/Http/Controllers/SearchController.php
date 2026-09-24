<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\MaintenanceTicket;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'section' => ['nullable', 'in:quick,all,guests,reservations,rooms,staff,invoices,products,maintenance'],
        ]);
        $term = $validated['q'];
        $section = $validated['section'] ?? 'quick';
        $user = $request->user();
        $branchScope = fn ($query) => $query->when(! $user->isSuperAdmin(), fn ($q) => $q->where('branch_id', $user->branch_id));
        $shouldLoad = fn (string $name): bool => $section === 'all'
            || ($section === 'quick' && in_array($name, ['guests', 'reservations', 'rooms'], true))
            || $section === $name;

        $guests = $shouldLoad('guests') ? Guest::query()
            ->select(['id', 'branch_id', 'first_name', 'last_name', 'email', 'phone'])
            ->tap($branchScope)->where(function ($query) use ($term) {
            $query->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%");
        })->limit(5)->get() : collect();
        $reservations = $shouldLoad('reservations') ? Reservation::query()
            ->select(['id', 'branch_id', 'guest_id', 'reservation_number', 'status', 'created_at'])
            ->with('guest:id,first_name,last_name')->tap($branchScope)->where(function ($query) use ($term) {
            $query->where('reservation_number', 'like', "%{$term}%")
                ->orWhereHas('guest', fn ($guest) => $guest->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%"));
        })->latest()->limit(5)->get() : collect();
        $rooms = $shouldLoad('rooms') ? Room::query()
            ->select(['id', 'branch_id', 'room_type_id', 'room_number', 'operational_status'])
            ->with('roomType:id,name')->tap($branchScope)->where(function ($query) use ($term) {
            $query->where('room_number', 'like', "%{$term}%")->orWhereHas('roomType', fn ($type) => $type->where('name', 'like', "%{$term}%"));
        })->limit(5)->get() : collect();
        $employees = $user->hasPermission('users.view') && $shouldLoad('staff')
            ? Employee::query()->select(['id', 'branch_id', 'employee_number', 'first_name', 'last_name', 'email', 'position'])
                ->tap($branchScope)->where(function ($query) use ($term) {
                $query->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('employee_number', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%");
            })->limit(5)->get()
            : collect();
        $invoices = $user->hasPermission('invoices.view') && $shouldLoad('invoices')
            ? Invoice::query()->select(['id', 'branch_id', 'guest_id', 'invoice_number', 'status', 'created_at'])
                ->with('guest:id,first_name,last_name')->tap($branchScope)->where(function ($query) use ($term) {
                $query->where('invoice_number', 'like', "%{$term}%")->orWhereHas('guest', fn ($guest) => $guest->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%"));
            })->latest()->limit(5)->get()
            : collect();
        $products = $user->hasPermission('inventory.view') && $shouldLoad('products')
            ? Product::query()->select(['id', 'branch_id', 'name', 'sku', 'current_stock'])
                ->tap($branchScope)->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%");
            })->limit(5)->get()
            : collect();
        $maintenance = $user->hasPermission('maintenance.view') && $shouldLoad('maintenance')
            ? MaintenanceTicket::query()->select(['id', 'branch_id', 'room_id', 'title', 'status', 'created_at'])
                ->with('room:id,room_number')->tap($branchScope)->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
            })->latest()->limit(5)->get()
            : collect();

        return view('search.index', compact('term', 'section', 'guests', 'reservations', 'rooms', 'employees', 'invoices', 'products', 'maintenance'));
    }
}
