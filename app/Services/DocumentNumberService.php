<?php

namespace App\Services;

use App\Models\NumberSequence;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Prefixes per document type. Single source of truth for numbering.
     */
    private const PREFIXES = [
        'RES' => 'RES',
        'FOL' => 'FOL',
        'INV' => 'INV',
        'ORD' => 'ORD',
        'EXP' => 'EXP',
    ];

/**
     * Generate the next document number for a branch + document type.
     *
     * MUST be called inside an open database transaction: the sequence row
     * is locked for update, serialising concurrent number generation for the
     * same branch/type. Rolled-back transactions return their number to
     * the pool (no gaps guaranteed, but uniqueness under concurrency is).
     *
     * @throws \InvalidArgumentException for an unknown document type.
     */
    public function next(int $branchId, string $documentType): string
    {
        if (! isset(self::PREFIXES[$documentType])) {
            throw new \InvalidArgumentException("Unknown document type [{$documentType}].");
        }

$prefix = self::PREFIXES[$documentType];
        $year = now()->format('Y');

// First() with lockForUpdate, creating the row if it does not exist.
        // A unique constraint on (branch_id, document_type) makes the
        // create-then-read race safe: the loser of the insert gets a
        // duplicate-key error and retries the select with the lock held.
        $sequence = NumberSequence::where('branch_id', $branchId)
            ->where('document_type', $documentType)
            ->lockForUpdate()
            ->first();

if (! $sequence) {
            try {
                $sequence = NumberSequence::create([
                    'branch_id' => $branchId,
                    'document_type' => $documentType,
                    'current_value' => 0,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Another concurrent request created it first: re-read with lock.
                if ($e->errorInfo[1] ?? 0 !== 1062) {
                    throw $e;
                }
                $sequence = NumberSequence::where('branch_id', $branchId)
                    ->where('document_type', $documentType)
                    ->lockForUpdate()
                    ->firstOrFail();
            }
        }

$next = $sequence->current_value + 1;
        $sequence->current_value = $next;
        $sequence->save();

return sprintf('%s-%s-%08d', $prefix, $year, $next);
    }
}
