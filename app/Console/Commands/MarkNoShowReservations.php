<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkNoShowReservations extends Command
{
    protected $signature = 'reservations:mark-no-shows
                            {--date= : Mark confirmed arrivals before this YYYY-MM-DD date (defaults to today)}
                            {--dry-run : Show the reservations that would be updated without changing data}';

    protected $description = 'Mark confirmed reservations whose arrival date has passed as no-shows.';

    public function handle(): int
    {
        try {
            $cutoff = $this->option('date')
                ? Carbon::createFromFormat('Y-m-d', (string) $this->option('date'))->startOfDay()
                : today()->startOfDay();
        } catch (\Throwable) {
            $this->error('The --date option must use YYYY-MM-DD format.');

            return self::FAILURE;
        }

        $candidates = Reservation::query()
            ->where('status', 'CONFIRMED')
            ->whereDate('check_in_date', '<', $cutoff->toDateString())
            ->orderBy('id')
            ->get(['id', 'reservation_number', 'check_in_date']);

        if ($candidates->isEmpty()) {
            $this->info('No confirmed reservations are overdue for arrival.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->table(['Reservation', 'Arrival date'], $candidates->map(fn (Reservation $reservation) => [
                $reservation->reservation_number,
                $reservation->check_in_date->toDateString(),
            ]));
            $this->comment("{$candidates->count()} reservation(s) would be marked as no-show.");

            return self::SUCCESS;
        }

        $updated = 0;

        foreach ($candidates as $candidate) {
            $wasUpdated = DB::transaction(function () use ($candidate): bool {
                $reservation = Reservation::query()->lockForUpdate()->find($candidate->id);

                if (! $reservation || $reservation->status !== 'CONFIRMED' || $reservation->check_in_date->gte(today())) {
                    return false;
                }

                $oldValues = $reservation->only(['status']);
                $reservation->update(['status' => 'NO_SHOW']);

                AuditService::log(
                    'RESERVATION_MARKED_NO_SHOW',
                    $reservation,
                    $oldValues,
                    ['status' => 'NO_SHOW'],
                    $reservation->branch_id,
                );

                return true;
            });

            $updated += (int) $wasUpdated;
        }

        $this->info("Marked {$updated} reservation(s) as no-show.");

        return self::SUCCESS;
    }
}
