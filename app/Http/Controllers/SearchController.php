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
        $validated = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100']]);
        $term = $validated['q'];
        $user = $request->user();
        $branchScope = fn ($query) => $query->when(! $user->isSuperAdmin(), fn ($q) => $q->where('branch_id', $user->branch_id));

        $guests = Guest::query()->tap($branchScope)->where(function ($query) use ($term) {
            $query->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%");
        })->limit(10)->get();
        $reservations = Reservation::with('guest')->tap($branchScope)->where(function ($query) use ($term) {
            $query->where('reservation_number', 'like', "%{$term}%")
                ->orWhereHas('guest', fn ($guest) => $guest->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%"));
        })->latest()->limit(10)->get();
        $rooms = Room::with('roomType')->tap($branchScope)->where(function ($query) use ($term) {
            $query->where('room_number', 'like', "%{$term}%")->orWhereHas('roomType', fn ($type) => $type->where('name', 'like', "%{$term}%"));
        })->limit(10)->get();
        $employees = $user->hasPermission('users.view')
            ? Employee::with('branch')->tap($branchScope)->where(function ($query) use ($term) {
                $query->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('employee_number', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%");
            })->limit(10)->get()
            : collect();
        $invoices = $user->hasPermission('invoices.view')
            ? Invoice::with('guest')->tap($branchScope)->where(function ($query) use ($term) {
                $query->where('invoice_number', 'like', "%{$term}%")->orWhereHas('guest', fn ($guest) => $guest->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%"));
            })->latest()->limit(10)->get()
            : collect();
        $products = $user->hasPermission('inventory.view')
            ? Product::tap($branchScope)->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%");
            })->limit(10)->get()
            : collect();
        $maintenance = $user->hasPermission('maintenance.view')
            ? MaintenanceTicket::with('room')->tap($branchScope)->where(function ($query) use ($term) {
                $query->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
            })->latest()->limit(10)->get()
            : collect();

        return view('search.index', compact('term', 'guests', 'reservations', 'rooms', 'employees', 'invoices', 'products', 'maintenance'));
    }
}
