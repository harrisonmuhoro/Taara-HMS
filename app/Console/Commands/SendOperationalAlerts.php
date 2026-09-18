<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\Stay;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SendOperationalAlerts extends Command
{
    protected $signature = 'hotel:send-operational-alerts';

    protected $description = 'Create daily in-app reminders for arrivals, departures, and low stock.';

    public function handle(): int
    {
        $created = 0;
        $created += $this->notifyUsers(
            users: $this->usersWithAnyPermission(['reservations.view', 'stays.check_in']),
            type: 'ARRIVAL_REMINDER',
            title: 'Arrivals tomorrow',
            message: fn (Reservation $reservation) => "{$reservation->guest?->first_name} {$reservation->guest?->last_name} arrives tomorrow in Room {$reservation->room?->room_number}.",
            records: Reservation::with(['guest', 'room'])
                ->where('status', 'CONFIRMED')
                ->whereDate('check_in_date', today()->addDay())
                ->get(),
            referenceType: 'reservation',
        );

        $created += $this->notifyUsers(
            users: $this->usersWithPermission('stays.check_out'),
            type: 'DEPARTURE_REMINDER',
            title: 'Departures today',
            message: fn (Stay $stay) => "{$stay->guest?->first_name} {$stay->guest?->last_name} is scheduled to check out today from Room {$stay->room?->room_number}.",
            records: Stay::with(['guest', 'room'])
                ->where('status', 'ACTIVE')
                ->whereDate('expected_check_out', today())
                ->get(),
            referenceType: 'stay',
        );

        $created += $this->notifyUsers(
            users: $this->usersWithPermission('inventory.view'),
            type: 'LOW_STOCK_ALERT',
            title: 'Low-stock inventory',
            message: fn (Product $product) => "{$product->name} is low on stock ({$product->current_stock} {$product->unit} remaining).",
            records: Product::query()->where('status', 'active')->whereColumn('current_stock', '<=', 'reorder_level')->get(),
            referenceType: 'product',
        );

        $this->info("Created {$created} operational alert(s).");

        return self::SUCCESS;
    }

    private function usersWithPermission(string $permission): Collection
    {
        return User::query()->where('status', 'active')->whereHas('roles.permissions', fn ($query) => $query->where('name', $permission))->get();
    }

    private function usersWithAnyPermission(array $permissions): Collection
    {
        return User::query()->where('status', 'active')->whereHas('roles.permissions', fn ($query) => $query->whereIn('name', $permissions))->get();
    }

    private function notifyUsers(Collection $users, string $type, string $title, callable $message, Collection $records, string $referenceType): int
    {
        $created = 0;

        foreach ($records as $record) {
            foreach ($users as $user) {
                if (! $user->isSuperAdmin() && isset($record->branch_id) && (int) $record->branch_id !== (int) $user->branch_id) {
                    continue;
                }

                $alreadyExists = Notification::query()
                    ->where('user_id', $user->id)
                    ->where('type', $type)
                    ->where('reference_type', $referenceType)
                    ->where('reference_id', $record->getKey())
                    ->whereDate('created_at', today())
                    ->exists();

                if (! $alreadyExists) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => $type,
                        'title' => $title,
                        'message' => $message($record),
                        'reference_type' => $referenceType,
                        'reference_id' => $record->getKey(),
                        'is_read' => false,
                        'created_at' => now(),
                    ]);
                    $created++;
                }
            }
        }

        return $created;
    }
}
