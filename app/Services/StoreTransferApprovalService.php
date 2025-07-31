<?php

namespace App\Services;

use App\Models\Account;
use App\Models\StoreTransferOrder;
use Illuminate\Support\Facades\Log;

class StoreTransferApprovalService
{
    protected $accountingService;

    public function __construct(AccountingEntryService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Process the approval of a store transfer order
     */
    public function processApproval(StoreTransferOrder $storeTransferOrder, array $validated): void
    {
        if ($validated['source'] === 'source') {
            $this->processSourceApproval($storeTransferOrder, $validated);
        } else {
            $this->processDestinationApproval($storeTransferOrder, $validated);
        }
    }

    /**
     * Process source approval
     */
    private function processSourceApproval(StoreTransferOrder $storeTransferOrder, array $validated): void
    {
        if ($validated['stage'] === 'store') {
            $storeTransferOrder->source_status = $validated['status'];
            $storeTransferOrder->source_store_approval_by = auth()->id();
            $storeTransferOrder->source_store_approval_date = now();
        } elseif ($validated['stage'] === 'branch') {
            $storeTransferOrder->source_status = $validated['status'];
            $storeTransferOrder->source_branch_approval_by = auth()->id();
            $storeTransferOrder->source_branch_approval_date = now();
        }
        
        $storeTransferOrder->save();
    }

    /**
     * Process destination approval
     */
    private function processDestinationApproval(StoreTransferOrder $storeTransferOrder, array $validated): void
    {
        if ($validated['stage'] === 'store') {
            $storeTransferOrder->destination_status = $validated['status'];
            $storeTransferOrder->destination_store_approval_by = auth()->id();
            $storeTransferOrder->destination_store_approval_date = now();
            
            // Auto-approve if same branch and status is pending
            if ($this->shouldAutoApprove($storeTransferOrder, $validated)) {
                $storeTransferOrder->destination_status = 'approved';
            }
        } elseif ($validated['stage'] === 'branch') {
            $storeTransferOrder->destination_status = $validated['status'];
            $storeTransferOrder->destination_branch_approval_by = auth()->id();
            $storeTransferOrder->destination_branch_approval_date = now();
        }
        
        $storeTransferOrder->save();
    }

    /**
     * Check if transfer should be auto-approved
     */
    private function shouldAutoApprove(StoreTransferOrder $storeTransferOrder, array $validated): bool
    {
        return $storeTransferOrder->destination_branch_id === $storeTransferOrder->source_branch_id 
            && $validated['status'] === 'pending';
    }

    /**
     * Check if accounting entries should be created
     */
    public function shouldCreateAccountingEntries(StoreTransferOrder $storeTransferOrder, array $validated): bool
    {
        return $validated['source'] === 'destination' 
            && $validated['status'] === 'approved'
            && $storeTransferOrder->destination_status === 'approved';
    }

    /**
     * Create accounting entries for the transfer
     */
    public function createAccountingEntries(StoreTransferOrder $storeTransferOrder): void
    {
        try {
            $items = $storeTransferOrder->items()->get();
            $entries = [];
            $description = "Store Transfer Order #{$storeTransferOrder->order_number}";

            foreach ($items as $item) {
                $amount = $item->unit_price * $item->quantity;
                
                // Debit Inventory at destination
                $entries[] = [
                    'journal_type_id' => 1, // Debit
                    'amount' => $amount,
                    'description' => "Inventory received for product #{$item->product_id}",
                    'account_id' => $this->getInventoryAccountId(),
                    'account_no' => $this->getInventoryAccountNo(),
                ];
                
                // Credit Inventory at source
                $entries[] = [
                    'journal_type_id' => 2, // Credit
                    'amount' => $amount,
                    'description' => "Inventory sent for product #{$item->product_id}",
                    'account_id' => $this->getInventoryAccountId(),
                    'account_no' => $this->getInventoryAccountNo(),
                ];
            }

            $data = [
                'description' => $description,
                'transaction_date' => $storeTransferOrder->transfer_date ?? now(),
                'store_id' => $storeTransferOrder->destination_store_id,
                'vendor_id' => null,
                'entries' => $entries,
            ];

            $this->accountingService->createInventoryEntries($data);
            
            Log::info('Accounting entries created for store transfer', [
                'order_id' => $storeTransferOrder->id,
                'entries_count' => count($entries)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to create accounting entries for store transfer', [
                'order_id' => $storeTransferOrder->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get inventory account ID
     */
    private function getInventoryAccountId(): ?string
    {
        // TODO: Implement account resolution logic
        // This should return the actual inventory account ID from your accounts table
        $account = Account::where('name', 'Inventory')->first();
        return $account->id ?? null;
    }

    /**
     * Get inventory account number
     */
    private function getInventoryAccountNo(): ?string
    {
        // This should return the actual inventory account number from your accounts table
        $account = Account::where('code', 'INV')->first();
        return $account->code ?? null;
    }

    /**
     * Find transfer order by ID and source
     */
    public function findTransferOrder(string $id, string $source): ?StoreTransferOrder
    {
        if ($source === 'source') {
            return StoreTransferOrder::where('id', $id)
                ->where('source_status', 'outgoing')
                ->first();
        }
        
        return StoreTransferOrder::where('id', $id)
            ->where('destination_status', 'incoming')
            ->first();
    }
} 