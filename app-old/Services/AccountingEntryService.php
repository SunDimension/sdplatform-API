<?php

namespace App\Services;

use App\Models\TransactionJournalEntry;
use App\Models\TransactionJournalEntryDetail;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Tax;
use Illuminate\Support\Facades\DB;
use App\Models\StoreItem;
use App\Models\SalesOrder;
use App\Models\SalesReceipt;
use App\Models\PostInflow;
use App\Models\PostOutflow;
use App\Models\ReturnItem;
use App\Models\FinancialPeriod;

class AccountingEntryService
{
    /**
     * Create accounting entries for inventory operations
     */
    public function createInventoryEntries($data)
    {
        return DB::transaction(function () use ($data) {
            // Create journal entry
            $journalEntry = TransactionJournalEntry::create([
                'description' => $data['description'],
                'payment_date' => $data['transaction_date'],
                'store_id' => $data['store_id'],
                'vendor_id' => $data['vendor_id'] ?? null,
                'created_by' => auth()->id()
            ]);

            // Create journal entry details
            foreach ($data['entries'] as $entry) {
                TransactionJournalEntryDetail::create([
                    'transaction_journal_entry_id' => $journalEntry->id,
                    'journal_type_id' => $entry['journal_type_id'],
                    'amount' => $entry['amount'],
                    'description' => $entry['description'],
                    'account_id' => $entry['account_id'],
                    'account_no' => $entry['account_no'],
                    'created_by' => auth()->id()
                ]);
            }

            // Create transaction records
            $this->createTransactions($data);

            return $journalEntry;
        });
    }

    /**
     * Create accounting entries for sales
     */
    public function createSalesEntries($saleData)
    {
        $entries = [];
        $totalAmount = $saleData['total_amount'];
        $taxAmount = 0;
        $discountAmount = $saleData['discount'] ?? 0;

        // Calculate tax if applicable
        if (isset($saleData['tax_id'])) {
            $tax = Tax::find($saleData['tax_id']);
            if ($tax && $tax->type === 'vat') {
                $taxAmount = ($totalAmount - $discountAmount) * ($tax->rate / 100);
            }
        }

        // Sales revenue entry
        $entries[] = [
            'journal_type_id' => 1, // Debit
            'amount' => $totalAmount,
            'description' => 'Sales Revenue',
            'account_id' => $this->getAccountId('Sales Revenue'),
            'account_no' => $this->getAccountNo('Sales Revenue')
        ];

        // Tax payable entry if applicable
        if ($taxAmount > 0) {
            $entries[] = [
                'journal_type_id' => 1, // Debit
                'amount' => $taxAmount,
                'description' => 'VAT Payable',
                'account_id' => $this->getAccountId('VAT Payable'),
                'account_no' => $this->getAccountNo('VAT Payable')
            ];
        }

        // Discount entry if applicable
        if ($discountAmount > 0) {
            $entries[] = [
                'journal_type_id' => 2, // Credit
                'amount' => $discountAmount,
                'description' => 'Sales Discount',
                'account_id' => $this->getAccountId('Sales Discount'),
                'account_no' => $this->getAccountNo('Sales Discount')
            ];
        }

        // Cost of goods sold entry
        $entries[] = [
            'journal_type_id' => 2, // Credit
            'amount' => $saleData['cost_of_goods'],
            'description' => 'Cost of Goods Sold',
            'account_id' => $this->getAccountId('Cost of Goods Sold'),
            'account_no' => $this->getAccountNo('Cost of Goods Sold')
        ];

        // Inventory entry
        $entries[] = [
            'journal_type_id' => 1, // Debit
            'amount' => $saleData['cost_of_goods'],
            'description' => 'Inventory',
            'account_id' => $this->getAccountId('Inventory'),
            'account_no' => $this->getAccountNo('Inventory')
        ];

        return $this->createInventoryEntries([
            'description' => 'Sales Entry - ' . $saleData['reference'],
            'transaction_date' => $saleData['transaction_date'],
            'store_id' => $saleData['store_id'],
            'entries' => $entries
        ]);
    }

    /**
     * Create accounting entries for purchases
     */
    public function createPurchaseEntries($purchaseData)
    {
        $entries = [];
        $totalAmount = $purchaseData['total_amount'];
        $taxAmount = 0;
        $discountAmount = $purchaseData['discount'] ?? 0;

        // Calculate tax if applicable
        if (isset($purchaseData['tax_id'])) {
            $tax = Tax::find($purchaseData['tax_id']);
            if ($tax && $tax->type === 'vat') {
                $taxAmount = ($totalAmount - $discountAmount) * ($tax->rate / 100);
            }
        }

        // Inventory entry
        $entries[] = [
            'journal_type_id' => 1, // Debit
            'amount' => $totalAmount - $taxAmount,
            'description' => 'Inventory',
            'account_id' => $this->getAccountId('Inventory'),
            'account_no' => $this->getAccountNo('Inventory')
        ];

        // Tax entry if applicable
        if ($taxAmount > 0) {
            $entries[] = [
                'journal_type_id' => 1, // Debit
                'amount' => $taxAmount,
                'description' => 'Input VAT',
                'account_id' => $this->getAccountId('Input VAT'),
                'account_no' => $this->getAccountNo('Input VAT')
            ];
        }

        // Accounts payable entry
        $entries[] = [
            'journal_type_id' => 2, // Credit
            'amount' => $totalAmount,
            'description' => 'Accounts Payable',
            'account_id' => $this->getAccountId('Accounts Payable'),
            'account_no' => $this->getAccountNo('Accounts Payable')
        ];

        // Discount entry if applicable
        if ($discountAmount > 0) {
            $entries[] = [
                'journal_type_id' => 1, // Debit
                'amount' => $discountAmount,
                'description' => 'Purchase Discount',
                'account_id' => $this->getAccountId('Purchase Discount'),
                'account_no' => $this->getAccountNo('Purchase Discount')
            ];
        }

        return $this->createInventoryEntries([
            'description' => 'Purchase Entry - ' . $purchaseData['reference'],
            'transaction_date' => $purchaseData['transaction_date'],
            'store_id' => $purchaseData['store_id'],
            'vendor_id' => $purchaseData['vendor_id'],
            'entries' => $entries
        ]);
    }

    /**
     * Create accounting entries for stock transfers
     */
    public function createStockTransferEntries($transferData)
    {
        $entries = [];

        // Source store inventory reduction
        $entries[] = [
            'journal_type_id' => 2, // Credit
            'amount' => $transferData['amount'],
            'description' => 'Inventory Transfer Out',
            'account_id' => $this->getAccountId('Inventory'),
            'account_no' => $this->getAccountNo('Inventory'),
            'store_id' => $transferData['source_store_id'], // Source store for this entry
            'branch_id' => $transferData['source_branch_id'] ?? null,
            'transaction_date' => $transferData['transaction_date'],
            'transcode' => 'STK_TRF_OUT', // Stock Transfer Out code
            'transtype' => 'TRANSFER_OUT',
            'naration' => 'Inventory Transfer Out to ' . $transferData['destination_store_id']
        ];

        // Destination store inventory increase
        $entries[] = [
            'journal_type_id' => 1, // Debit
            'amount' => $transferData['amount'],
            'description' => 'Inventory Transfer In',
            'account_id' => $this->getAccountId('Inventory'),
            'account_no' => $this->getAccountNo('Inventory'),
            'store_id' => $transferData['destination_store_id'], // Destination store for this entry
            'branch_id' => $transferData['destination_branch_id'] ?? null,
            'transaction_date' => $transferData['transaction_date'],
            'transcode' => 'STK_TRF_IN', // Stock Transfer In code
            'transtype' => 'TRANSFER_IN',
            'naration' => 'Inventory Transfer In from ' . $transferData['source_store_id']
        ];

        return $this->createInventoryEntries([
            'description' => 'Stock Transfer - ' . $transferData['reference'],
            'transaction_date' => $transferData['transaction_date'],
            'store_id' => $transferData['destination_store_id'], // Use destination store for journal entry header
            'branch_id' => $transferData['destination_branch_id'] ?? null,
            'vendor_id' => null, // Transfers don't involve vendors
            'entries' => $entries,
            // Add fields needed for Transaction creation
            'financial_period_id' => $transferData['financial_period_id'] ?? $this->getCurrentFinancialPeriodId(),
            'transcode' => 'STK_TRF', // Stock Transfer code
            'transtype' => 'TRANSFER',
            'naration' => 'Stock Transfer between stores'
        ]);
    }

    /**
     * Create accounting entries for returns
     */
    public function createReturnEntries($returnData)
    {
        $entries = [];
        $totalAmount = $returnData['total_amount'];
        $taxAmount = 0;

        // Calculate tax if applicable
        if (isset($returnData['tax_id'])) {
            $tax = Tax::find($returnData['tax_id']);
            if ($tax && $tax->type === 'vat') {
                $taxAmount = $totalAmount * ($tax->rate / 100);
            }
        }

        // Sales returns entry
        $entries[] = [
            'journal_type_id' => 2, // Credit
            'amount' => $totalAmount - $taxAmount,
            'description' => 'Sales Returns',
            'account_id' => $this->getAccountId('Sales Returns'),
            'account_no' => $this->getAccountNo('Sales Returns')
        ];

        // Tax entry if applicable
        if ($taxAmount > 0) {
            $entries[] = [
                'journal_type_id' => 2, // Credit
                'amount' => $taxAmount,
                'description' => 'VAT Payable',
                'account_id' => $this->getAccountId('VAT Payable'),
                'account_no' => $this->getAccountNo('VAT Payable')
            ];
        }

        // Inventory entry
        $entries[] = [
            'journal_type_id' => 1, // Debit
            'amount' => $returnData['cost_of_goods'],
            'description' => 'Inventory',
            'account_id' => $this->getAccountId('Inventory'),
            'account_no' => $this->getAccountNo('Inventory')
        ];

        // Cost of goods sold entry
        $entries[] = [
            'journal_type_id' => 2, // Credit
            'amount' => $returnData['cost_of_goods'],
            'description' => 'Cost of Goods Sold',
            'account_id' => $this->getAccountId('Cost of Goods Sold'),
            'account_no' => $this->getAccountNo('Cost of Goods Sold')
        ];

        return $this->createInventoryEntries([
            'description' => 'Return Entry - ' . $returnData['reference'],
            'transaction_date' => $returnData['transaction_date'],
            'store_id' => $returnData['store_id'],
            'entries' => $entries
        ]);
    }

    /**
     * Create transaction records
     */
    private function createTransactions($data)
    {
        foreach ($data['entries'] as $entry) {
            $transactionDate = $entry['transaction_date'] ?? $data['transaction_date'];
            
            Transaction::create([
                'financial_period_id' => $entry['financial_period_id'] ?? 
                                       $data['financial_period_id'] ?? 
                                       $this->getFinancialPeriodId($transactionDate),
                'transaction_date' => $transactionDate,
                'transcode' => $entry['transcode'] ?? $data['transcode'] ?? 'INV',
                'transtype' => $entry['transtype'] ?? $data['transtype'] ?? 'Inventory',
                'naration' => $entry['description'],
                'debit' => $entry['journal_type_id'] == 1 ? $entry['amount'] : 0,
                'credit' => $entry['journal_type_id'] == 2 ? $entry['amount'] : 0,
                'amount' => $entry['amount'],
                'store_id' => $entry['store_id'] ?? $data['store_id'],
                'branch_id' => $entry['branch_id'] ?? $data['branch_id'] ?? null,
                'account_no' => $entry['account_no'],
                'account_id' => $entry['account_id'],
                'created_by' => auth()->id()
            ]);

        }
    }

    /**
     * Get account ID by name
     */
    private function getAccountId($name)
    {
        $account = Account::where('name', $name)->first();
        return $account ? $account->id : null;
    }

    /**
     * Get account number by name
     */
    private function getAccountNo($name)
    {
        $account = Account::where('name', $name)->first();
        return $account ? $account->code : null;
    }

    /**
     * Get financial period ID for a given date
     */
    private function getFinancialPeriodId($date)
    {
        $financialPeriod = \App\Models\FinancialPeriod::where('date_from', '<=', $date)
            ->where('date_to', '>=', $date)
            ->where('is_active', true)
            ->first();
        
        return $financialPeriod ? $financialPeriod->id : null;
    }

    /**
     * Generate accounting entries for a SalesOrder
     * 
     * @param SalesOrder $salesOrder The sales order to generate entries for
     * @param array $itemsSold Array of ItemSold models
     * @return JournalEntry
     */
    public function generateSalesOrderEntries($salesOrder, $itemsSold)
    {
        return DB::transaction(function () use ($salesOrder, $itemsSold) {
            // Create journal entry for the sales order
            $journalEntry = TransactionJournalEntry::create([
                'description' => "Sales Order #{$salesOrder->sales_order_number}",
                'payment_date' => $salesOrder->sales_date ?? now(),
                'store_id' => $salesOrder->store_id,
                'branch_id' => $salesOrder->branch_id,
                'created_by' => auth()->id()
            ]);

            $totalAmount = 0;
            $totalCost = 0;

            // Create journal entry details for each item sold
            foreach ($itemsSold as $itemSold) {
                $amount = $itemSold->amount;
                $totalAmount += $amount;

                // Get the product's cost price from StoreItem
                $storeItem = StoreItem::where('create_item_id', $itemSold->product_id)
                    ->where('store_id', $itemSold->store_id)
                    ->first();

                if ($storeItem) {
                    $costAmount = $storeItem->cost_price * $itemSold->quantity;
                    $totalCost += $costAmount;

                    // Debit Cost of Goods Sold
                    TransactionJournalEntryDetail::create([
                        'transaction_journal_entry_id' => $journalEntry->id,
                        'journal_type_id' => 1, // Assuming 1 is for debit
                        'amount' => $costAmount,
                        'description' => "COGS for {$itemSold->product->name}",
                        'account_id' => $this->getAccountId('Cost of Goods Sold'),
                        'account_no' => $this->getAccountNo('Cost of Goods Sold'),
                        'created_by' => auth()->id()
                    ]);

                    // Credit Inventory
                    TransactionJournalEntryDetail::create([
                        'transaction_journal_entry_id' => $journalEntry->id,
                        'journal_type_id' => 2, // Assuming 2 is for credit
                        'amount' => $costAmount,
                        'description' => "Inventory reduction for {$itemSold->product->name}",
                        'account_id' => $this->getAccountId('Inventory'),
                        'account_no' => $this->getAccountNo('Inventory'),
                        'created_by' => auth()->id()
                    ]);
                }

                // Debit Accounts Receivable (if credit sale) or Cash/Bank (if cash sale)
                if ($salesOrder->payment_type === 'Credit') {
                    TransactionJournalEntryDetail::create([
                        'transaction_journal_entry_id' => $journalEntry->id,
                        'journal_type_id' => 1, // Debit
                        'amount' => $amount,
                        'description' => "Accounts Receivable for {$itemSold->product->name}",
                        'account_id' => $this->getAccountId('Accounts Receivable'),
                        'account_no' => $this->getAccountNo('Accounts Receivable'),
                        'created_by' => auth()->id()
                    ]);
                } else {
                    TransactionJournalEntryDetail::create([
                        'transaction_journal_entry_id' => $journalEntry->id,
                        'journal_type_id' => 1, // Debit
                        'amount' => $amount,
                        'description' => "Cash/Bank for {$itemSold->product->name}",
                        'account_id' => $this->getAccountId($salesOrder->payment_type === 'Bank' ? 'Bank' : 'Cash'),
                        'account_no' => $this->getAccountNo($salesOrder->payment_type === 'Bank' ? 'Bank' : 'Cash'),
                        'created_by' => auth()->id()
                    ]);
                }

                // Credit Sales Revenue
                TransactionJournalEntryDetail::create([
                    'transaction_journal_entry_id' => $journalEntry->id,
                    'journal_type_id' => 2, // Credit
                    'amount' => $amount,
                    'description' => "Sales Revenue for {$itemSold->product->name}",
                    'account_id' => $this->getAccountId('Sales Revenue'),
                    'account_no' => $this->getAccountNo('Sales Revenue'),
                    'created_by' => auth()->id()
                ]);
            }

            // Create transaction records for all journal entry details
            $this->createTransactions([
                'entries' => [
                    // COGS entries
                    [
                        'journal_type_id' => 1, // Debit
                        'amount' => $totalCost,
                        'description' => "Cost of Goods Sold for Sales Order #{$salesOrder->sales_order_number}",
                        'account_id' => $this->getAccountId('Cost of Goods Sold'),
                        'account_no' => $this->getAccountNo('Cost of Goods Sold'),
                        'transaction_date' => $salesOrder->sales_date ?? now(),
                        'store_id' => $salesOrder->store_id,
                        'branch_id' => $salesOrder->branch_id,
                        'transcode' => 'SO',
                        'transtype' => 'Sales'
                    ],
                    [
                        'journal_type_id' => 2, // Credit
                        'amount' => $totalCost,
                        'description' => "Inventory reduction for Sales Order #{$salesOrder->sales_order_number}",
                        'account_id' => $this->getAccountId('Inventory'),
                        'account_no' => $this->getAccountNo('Inventory'),
                        'transaction_date' => $salesOrder->sales_date ?? now(),
                        'store_id' => $salesOrder->store_id,
                        'branch_id' => $salesOrder->branch_id,
                        'transcode' => 'SO',
                        'transtype' => 'Sales'
                    ],
                    // Sales Revenue entry
                    [
                        'journal_type_id' => 2, // Credit
                        'amount' => $totalAmount,
                        'description' => "Sales Revenue for Sales Order #{$salesOrder->sales_order_number}",
                        'account_id' => $this->getAccountId('Sales Revenue'),
                        'account_no' => $this->getAccountNo('Sales Revenue'),
                        'transaction_date' => $salesOrder->sales_date ?? now(),
                        'store_id' => $salesOrder->store_id,
                        'branch_id' => $salesOrder->branch_id,
                        'transcode' => 'SO',
                        'transtype' => 'Sales'
                    ],
                    // Accounts Receivable or Cash/Bank entry
                    [
                        'journal_type_id' => 1, // Debit
                        'amount' => $totalAmount,
                        'description' => $salesOrder->payment_type === 'Credit' ? 
                            "Accounts Receivable for Sales Order #{$salesOrder->sales_order_number}" :
                            "Cash/Bank for Sales Order #{$salesOrder->sales_order_number}",
                        'account_id' => $salesOrder->payment_type === 'Credit' ? 
                            $this->getAccountId('Accounts Receivable') :
                            $this->getAccountId($salesOrder->payment_type === 'Bank' ? 'Bank' : 'Cash'),
                        'account_no' => $salesOrder->payment_type === 'Credit' ? 
                            $this->getAccountNo('Accounts Receivable') :
                            $this->getAccountNo($salesOrder->payment_type === 'Bank' ? 'Bank' : 'Cash'),
                        'transaction_date' => $salesOrder->sales_date ?? now(),
                        'store_id' => $salesOrder->store_id,
                        'branch_id' => $salesOrder->branch_id,
                        'transcode' => 'SO',
                        'transtype' => 'Sales'
                    ]
                ]
            ]);

            return $journalEntry;
        });
    }

    /**
     * Generate accounting entries for a SalesReceipt
     * 
     * @param SalesReceipt $salesReceipt The sales receipt to generate entries for
     * @return JournalEntry
     */
    public function generateSalesReceiptEntries($salesReceipt)
    {
        return DB::transaction(function () use ($salesReceipt) {
            // Create journal entry for the sales receipt
            $journalEntry = TransactionJournalEntry::create([
                'description' => "Sales Receipt #{$salesReceipt->sales_receipt_number}",
                'payment_date' => $salesReceipt->payment_date ?? now(),
                'store_id' => $salesReceipt->store_id,
                'branch_id' => $salesReceipt->branch_id,
                'created_by' => auth()->id()
            ]);

            // Debit Cash/Bank
            TransactionJournalEntryDetail::create([
                'transaction_journal_entry_id' => $journalEntry->id,
                'journal_type_id' => 1, // Debit
                'amount' => $salesReceipt->amount_paid,
                'description' => "Payment received for Sales Receipt #{$salesReceipt->sales_receipt_number}",
                'account_id' => $this->getAccountId($salesReceipt->payment_type === 'Bank' ? 'Bank Accounts' : 'Cash Accounts'),
                'account_no' => $this->getAccountNo($salesReceipt->payment_type === 'Bank' ? 'Bank Accounts' : 'Cash Accounts'),
                'created_by' => auth()->id()
            ]);

            // Credit Accounts Receivable
            TransactionJournalEntryDetail::create([
                'transaction_journal_entry_id' => $journalEntry->id,
                'journal_type_id' => 2, // Credit
                'amount' => $salesReceipt->amount_paid,
                'description' => "Accounts Receivable reduction for Sales Receipt #{$salesReceipt->sales_receipt_number}",
                'account_id' => $this->getAccountId('Accounts Receivable'),
                'account_no' => $this->getAccountNo('Accounts Receivable'),
                'created_by' => auth()->id()
            ]);

            // Create transaction records for both entries
            $this->createTransactions([
                'entries' => [
                    [
                        'journal_type_id' => 2, // Credit
                        'amount' => $salesReceipt->amount_paid,
                        'description' => "Accounts Receivable reduction for Sales Receipt #{$salesReceipt->sales_receipt_number}",
                        'account_id' => $this->getAccountId('Accounts Receivable'),
                        'account_no' => $this->getAccountNo('Accounts Receivable'),
                        'transaction_date' => $salesReceipt->payment_date ?? now(),
                        'store_id' => $salesReceipt->store_id,
                        'branch_id' => $salesReceipt->branch_id,
                        'transcode' => $salesReceipt->sales_receipt_number,
                        'transtype' => 'Payment'
                    ],
                    [
                        'journal_type_id' => 1, // Debit
                        'amount' => $salesReceipt->amount_paid,
                        'description' => "Payment received for Sales Receipt #{$salesReceipt->sales_receipt_number}",
                        'account_id' => $this->getAccountId($salesReceipt->payment_type === 'Bank' ? 'Bank Accounts' : 'Cash Accounts'),
                        'account_no' => $this->getAccountNo($salesReceipt->payment_type === 'Bank' ? 'Bank Accounts' : 'Cash Accounts'),
                        'transaction_date' => $salesReceipt->payment_date ?? now(),
                        'store_id' => $salesReceipt->store_id,
                        'branch_id' => $salesReceipt->branch_id,
                        'transcode' => $salesReceipt->sales_receipt_number,
                        'transtype' => 'Payment'
                    ]
                ]
            ]);

            return $journalEntry;
        });
    }

    /**
     * Generate accounting entries for a PostInflow
     * 
     * @param PostInflow $postInflow The post inflow to generate entries for
     * @return JournalEntry
     */
    public function generatePostInflowEntries($postInflow)
    {
        return DB::transaction(function () use ($postInflow) {
            // Create journal entry for the post inflow
            $journalEntry = TransactionJournalEntry::create([
                'description' => "Post Inflow #{$postInflow->id} - {$postInflow->narration}",
                'payment_date' => $postInflow->inflow_date ?? now(),
                'store_id' => $postInflow->store_id ?? null,
                'branch_id' => $postInflow->branch_id ?? null,
                'created_by' => auth()->id()
            ]);

            // Debit Bank/Cash account
            TransactionJournalEntryDetail::create([
                'transaction_journal_entry_id' => $journalEntry->id,
                'journal_type_id' => 1, // Debit
                'amount' => $postInflow->amount,
                'description' => "Bank deposit for Post Inflow #{$postInflow->id}",
                'account_id' => $this->getAccountId('Bank'),
                'account_no' => $this->getAccountNo('Bank'),
                'created_by' => auth()->id()
            ]);

            // Credit Suspense Account or Customer Account
            if ($postInflow->customer_id) {
                // If customer is assigned, credit customer account
                TransactionJournalEntryDetail::create([
                    'transaction_journal_entry_id' => $journalEntry->id,
                    'journal_type_id' => 2, // Credit
                    'amount' => $postInflow->amount,
                    'description' => "Customer credit for Post Inflow #{$postInflow->id}",
                    'account_id' => $this->getAccountId('Accounts Receivable'),
                    'account_no' => $this->getAccountNo('Accounts Receivable'),
                    'created_by' => auth()->id()
                ]);
            } else {
                // If no customer assigned, credit suspense account
                TransactionJournalEntryDetail::create([
                    'transaction_journal_entry_id' => $journalEntry->id,
                    'journal_type_id' => 2, // Credit
                    'amount' => $postInflow->amount,
                    'description' => "Suspense account for Post Inflow #{$postInflow->id}",
                    'account_id' => $this->getAccountId('Suspense Account'),
                    'account_no' => $this->getAccountNo('Suspense Account'),
                    'created_by' => auth()->id()
                ]);
            }

            // Create transaction records for both entries
            $this->createTransactions([
                'entries' => [
                    [
                        'journal_type_id' => 1, // Debit
                        'amount' => $postInflow->amount,
                        'description' => "Bank deposit for Post Inflow #{$postInflow->id}",
                        'account_id' => $this->getAccountId('Bank'),
                        'account_no' => $this->getAccountNo('Bank'),
                        'transaction_date' => $postInflow->inflow_date ?? now(),
                        'store_id' => $postInflow->store_id ?? null,
                        'branch_id' => $postInflow->branch_id ?? null,
                        'transcode' => 'PI',
                        'transtype' => 'Inflow'
                    ],
                    [
                        'journal_type_id' => 2, // Credit
                        'amount' => $postInflow->amount,
                        'description' => $postInflow->customer_id ? 
                            "Customer credit for Post Inflow #{$postInflow->id}" :
                            "Suspense account for Post Inflow #{$postInflow->id}",
                        'account_id' => $postInflow->customer_id ? 
                            $this->getAccountId('Accounts Receivable') :
                            $this->getAccountId('Suspense Account'),
                        'account_no' => $postInflow->customer_id ? 
                            $this->getAccountNo('Accounts Receivable') :
                            $this->getAccountNo('Suspense Account'),
                        'transaction_date' => $postInflow->inflow_date ?? now(),
                        'store_id' => $postInflow->store_id ?? null,
                        'branch_id' => $postInflow->branch_id ?? null,
                        'transcode' => 'PI',
                        'transtype' => 'Inflow'
                    ]
                ]
            ]);

            return $journalEntry;
        });
    }

    /**
     * Generate accounting entries for a PostOutflow
     * 
     * @param PostOutflow $postOutflow The post outflow to generate entries for
     * @return JournalEntry
     */
    public function generatePostOutflowEntries($postOutflow)
    {
        return DB::transaction(function () use ($postOutflow) {
            // Create journal entry for the post outflow
            $journalEntry = TransactionJournalEntry::create([
                'description' => "Post Outflow #{$postOutflow->id} - {$postOutflow->narration}",
                'payment_date' => $postOutflow->outflow_date ?? now(),
                'store_id' => $postOutflow->store_id ?? null,
                'branch_id' => $postOutflow->branch_id ?? null,
                'created_by' => auth()->id()
            ]);

            // Debit Customer Account or Suspense Account
            if ($postOutflow->customer_id) {
                // If customer is assigned, debit customer account
                TransactionJournalEntryDetail::create([
                    'transaction_journal_entry_id' => $journalEntry->id,
                    'journal_type_id' => 1, // Debit
                    'amount' => $postOutflow->amount,
                    'description' => "Customer debit for Post Outflow #{$postOutflow->id}",
                    'account_id' => $this->getAccountId('Accounts Receivable'),
                    'account_no' => $this->getAccountNo('Accounts Receivable'),
                    'created_by' => auth()->id()
                ]);
            } else {
                // If no customer assigned, debit suspense account
                TransactionJournalEntryDetail::create([
                    'transaction_journal_entry_id' => $journalEntry->id,
                    'journal_type_id' => 1, // Debit
                    'amount' => $postOutflow->amount,
                    'description' => "Suspense account for Post Outflow #{$postOutflow->id}",
                    'account_id' => $this->getAccountId('Suspense Account'),
                    'account_no' => $this->getAccountNo('Suspense Account'),
                    'created_by' => auth()->id()
                ]);
            }

            // Credit Bank/Cash account
            TransactionJournalEntryDetail::create([
                'transaction_journal_entry_id' => $journalEntry->id,
                'journal_type_id' => 2, // Credit
                'amount' => $postOutflow->amount,
                'description' => "Bank withdrawal for Post Outflow #{$postOutflow->id}",
                'account_id' => $this->getAccountId('Bank'),
                'account_no' => $this->getAccountNo('Bank'),
                'created_by' => auth()->id()
            ]);

            // Create transaction records for both entries
            $this->createTransactions([
                'entries' => [
                    [
                        'journal_type_id' => 1, // Debit
                        'amount' => $postOutflow->amount,
                        'description' => $postOutflow->customer_id ? 
                            "Customer debit for Post Outflow #{$postOutflow->id}" :
                            "Suspense account for Post Outflow #{$postOutflow->id}",
                        'account_id' => $postOutflow->customer_id ? 
                            $this->getAccountId('Accounts Receivable') :
                            $this->getAccountId('Suspense Account'),
                        'account_no' => $postOutflow->customer_id ? 
                            $this->getAccountNo('Accounts Receivable') :
                            $this->getAccountNo('Suspense Account'),
                        'transaction_date' => $postOutflow->outflow_date ?? now(),
                        'store_id' => $postOutflow->store_id ?? null,
                        'branch_id' => $postOutflow->branch_id ?? null,
                        'transcode' => 'PO',
                        'transtype' => 'Outflow'
                    ],
                    [
                        'journal_type_id' => 2, // Credit
                        'amount' => $postOutflow->amount,
                        'description' => "Bank withdrawal for Post Outflow #{$postOutflow->id}",
                        'account_id' => $this->getAccountId('Bank'),
                        'account_no' => $this->getAccountNo('Bank'),
                        'transaction_date' => $postOutflow->outflow_date ?? now(),
                        'store_id' => $postOutflow->store_id ?? null,
                        'branch_id' => $postOutflow->branch_id ?? null,
                        'transcode' => 'PO',
                        'transtype' => 'Outflow'
                    ]
                ]
            ]);

            return $journalEntry;
        });
    }

    /**
     * Generate accounting entries for a ReturnItem
     * 
     * @param ReturnItem $returnItem The return item to generate entries for
     * @return JournalEntry
     */
    public function generateReturnItemEntries($returnItem)
    {
        return DB::transaction(function () use ($returnItem) {
            // Calculate total return amount
            $totalReturnAmount = $returnItem->returnDetails->sum(function ($detail) {
                return $detail->return_quantity * $detail->unit_price;
            });

            // Create journal entry for the return item
            $journalEntry = TransactionJournalEntry::create([
                'description' => "Return Item #{$returnItem->id} - Customer Return",
                'payment_date' => $returnItem->return_date ?? now(),
                'store_id' => $returnItem->store_id ?? null,
                'branch_id' => $returnItem->branch_id ?? null,
                'created_by' => auth()->id()
            ]);

            // Debit Sales Returns (reduces revenue)
            TransactionJournalEntryDetail::create([
                'transaction_journal_entry_id' => $journalEntry->id,
                'journal_type_id' => 1, // Debit
                'amount' => $totalReturnAmount,
                'description' => "Sales Returns for Return Item #{$returnItem->id}",
                'account_id' => $this->getAccountId('Sales Returns'),
                'account_no' => $this->getAccountNo('Sales Returns'),
                'created_by' => auth()->id()
            ]);

            // Credit Accounts Receivable (if credit sale) or Cash/Bank (if cash sale)
            if ($returnItem->salesReceipt && $returnItem->salesReceipt->salesOrder) {
                $salesOrder = $returnItem->salesReceipt->salesOrder;

                if ($salesOrder->payment_type === 'Credit') {
                    // Credit Accounts Receivable for credit sales
                    TransactionJournalEntryDetail::create([
                        'transaction_journal_entry_id' => $journalEntry->id,
                        'journal_type_id' => 2, // Credit
                        'amount' => $totalReturnAmount,
                        'description' => "Accounts Receivable credit for Return Item #{$returnItem->id}",
                        'account_id' => $this->getAccountId('Accounts Receivable'),
                        'account_no' => $this->getAccountNo('Accounts Receivable'),
                        'created_by' => auth()->id()
                    ]);
                } else {
                    // Credit Cash/Bank for cash sales
                    TransactionJournalEntryDetail::create([
                        'transaction_journal_entry_id' => $journalEntry->id,
                        'journal_type_id' => 2, // Credit
                        'amount' => $totalReturnAmount,
                        'description' => "Cash/Bank credit for Return Item #{$returnItem->id}",
                        'account_id' => $this->getAccountId('Bank'),
                        'account_no' => $this->getAccountNo('Bank'),
                        'created_by' => auth()->id()
                    ]);
                }
            } else {
                // Default to Cash/Bank if no sales receipt or order found
                TransactionJournalEntryDetail::create([
                    'transaction_journal_entry_id' => $journalEntry->id,
                    'journal_type_id' => 2, // Credit
                    'amount' => $totalReturnAmount,
                    'description' => "Cash/Bank credit for Return Item #{$returnItem->id}",
                    'account_id' => $this->getAccountId('Bank'),
                    'account_no' => $this->getAccountNo('Bank'),
                    'created_by' => auth()->id()
                ]);
            }

            // Create transaction records for both entries
            $this->createTransactions([
                'entries' => [
                    [
                        'journal_type_id' => 1, // Debit
                        'amount' => $totalReturnAmount,
                        'description' => "Sales Returns for Return Item #{$returnItem->id}",
                        'account_id' => $this->getAccountId('Sales Returns'),
                        'account_no' => $this->getAccountNo('Sales Returns'),
                        'transaction_date' => $returnItem->return_date ?? now(),
                        'store_id' => $returnItem->store_id ?? null,
                        'branch_id' => $returnItem->branch_id ?? null,
                        'transcode' => 'RI',
                        'transtype' => 'Return'
                    ],
                    [
                        'journal_type_id' => 2, // Credit
                        'amount' => $totalReturnAmount,
                        'description' => $returnItem->salesReceipt && $returnItem->salesReceipt->salesOrder && $returnItem->salesReceipt->salesOrder->payment_type === 'Credit' ? 
                            "Accounts Receivable credit for Return Item #{$returnItem->id}" :
                            "Cash/Bank credit for Return Item #{$returnItem->id}",
                        'account_id' => $returnItem->salesReceipt && $returnItem->salesReceipt->salesOrder && $returnItem->salesReceipt->salesOrder->payment_type === 'Credit' ? 
                            $this->getAccountId('Accounts Receivable') :
                            $this->getAccountId('Bank'),
                        'account_no' => $returnItem->salesReceipt && $returnItem->salesReceipt->salesOrder && $returnItem->salesReceipt->salesOrder->payment_type === 'Credit' ? 
                            $this->getAccountNo('Accounts Receivable') :
                            $this->getAccountNo('Bank'),
                        'transaction_date' => $returnItem->return_date ?? now(),
                        'store_id' => $returnItem->store_id ?? null,
                        'branch_id' => $returnItem->branch_id ?? null,
                        'transcode' => 'RI',
                        'transtype' => 'Return'
                    ]
                ]
            ]);

            return $journalEntry;
        });
    }

    /**
     * Get current active financial period ID
     */
    private function getCurrentFinancialPeriodId()
    {
        // Return current financial period ID
        return FinancialPeriod::where('status', 'active')->first()?->id;
    }
}
