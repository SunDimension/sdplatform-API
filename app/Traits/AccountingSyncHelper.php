<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait AccountingSyncHelper
{
    /**
     * Insert accounting record with sync metadata
     */
    protected function insertAccountingRecord(string $table, array $data): void
    {
        // Add sync metadata to the record
        $data['sync_id'] = (string) Str::uuid();
        $data['location_id'] = config('app.location_id');
        $data['sync_status'] = 'pending'; // Mark as pending for sync
        $data['sync_version'] = 1;
        $data['last_synced_at'] = null;
        $data['created_at'] = $data['created_at'] ?? now();
        $data['updated_at'] = $data['updated_at'] ?? now();

        DB::table($table)->insert($data);
    }

    /**
     * Update accounting record with sync metadata
     */
    protected function updateAccountingRecord(string $table, array $where, array $data): void
    {
        // Mark as pending for sync
        $data['sync_status'] = 'pending';
        $data['sync_version'] = DB::raw('sync_version + 1');
        $data['updated_at'] = now();

        DB::table($table)->where($where)->update($data);
    }

    /**
     * Prepare accounting data for sync
     */
    protected function prepareAccountingDataForSync(string $table, string $primaryKey, $primaryValue): array
    {
        $record = DB::table($table)
            ->where($primaryKey, $primaryValue)
            ->first();

        if (!$record) {
            return [];
        }

        $data = (array) $record;

        return [
            'table_name' => $table,
            'primary_key' => $primaryKey,
            'data' => $data,
            'sync_metadata' => [
                'sync_id' => $data['sync_id'] ?? null,
                'location_id' => $data['location_id'] ?? config('app.location_id'),
                'sync_status' => $data['sync_status'] ?? 'pending',
                'accounting_type' => $this->getAccountingType($table),
                'table_name' => $table,
            ]
        ];
    }

    /**
     * Get accounting type from table name
     */
    protected function getAccountingType(string $table): string
    {
        return match($table) {
            'transactions' => 'Transaction',
            'journal_entry' => 'JournalEntry',
            'journal_lines' => 'JournalLine',
            'ledger_postings' => 'LedgerPosting',
            default => 'Unknown'
        };
    }
}