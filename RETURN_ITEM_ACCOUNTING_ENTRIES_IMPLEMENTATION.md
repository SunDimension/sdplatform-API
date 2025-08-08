# ReturnItem Accounting Entries Implementation

## Overview

This document outlines the implementation of accounting entries for the `ReturnItemController` in the Sales and Inventory Software API. The implementation follows the same pattern as other controllers (`SalesReceiptController`, `PostInflowController`, `PostOutflowController`) to provide comprehensive accounting functionality for return items.

## Features

### 1. Automatic Accounting Entry Generation
- **Trigger**: When a new return item is created via the `store` method
- **Process**: Automatically generates accounting entries within a database transaction
- **Error Handling**: Logs errors without rolling back the main transaction to ensure data consistency

### 2. Manual Accounting Entry Generation
- **Single Entry**: Generate accounting entries for a specific return item
- **Bulk Generation**: Generate accounting entries for multiple return items
- **Duplicate Prevention**: Checks for existing entries before creating new ones

### 3. Accounting Entry Retrieval
- **Query**: Retrieve existing accounting entries for a specific return item
- **Relationships**: Includes account and journal type details

## API Endpoints

### 1. Generate Accounting Entries for Single Return Item
```
POST /api/return-items/{id}/generate-accounting-entries
```

**Response:**
```json
{
    "message": "Accounting entries generated successfully",
    "journal_entry": {
        "id": 123,
        "description": "Return Item #456 - Customer Return",
        "payment_date": "2024-01-15T10:30:00Z",
        "store_id": 1,
        "branch_id": 2,
        "created_by": 1,
        "created_at": "2024-01-15T10:30:00Z",
        "updated_at": "2024-01-15T10:30:00Z"
    }
}
```

### 2. Generate Accounting Entries for Multiple Return Items
```
POST /api/return-items/generate-bulk-accounting-entries
```

**Request Body:**
```json
{
    "return_item_ids": [1, 2, 3, 4, 5]
}
```

**Response:**
```json
{
    "message": "Bulk accounting entries generation completed. Success: 3, Failures: 2",
    "results": [
        {
            "id": 1,
            "status": "success",
            "journal_entry_id": 123
        },
        {
            "id": 2,
            "status": "skipped",
            "message": "Accounting entries already exist"
        },
        {
            "id": 3,
            "status": "failed",
            "error": "Account not found"
        }
    ],
    "summary": {
        "total": 5,
        "success": 3,
        "failure": 2
    }
}
```

### 3. Get Accounting Entries for Return Item
```
GET /api/return-items/{id}/accounting-entries
```

**Response:**
```json
{
    "return_item_id": 456,
    "journal_entries": [
        {
            "id": 123,
            "description": "Return Item #456 - Customer Return",
            "payment_date": "2024-01-15T10:30:00Z",
            "store_id": 1,
            "branch_id": 2,
            "created_by": 1,
            "details": [
                {
                    "id": 1,
                    "journal_type_id": 1,
                    "amount": 150.00,
                    "description": "Sales Returns for Return Item #456",
                    "account_id": 5,
                    "account_no": "SR001",
                    "account": {
                        "id": 5,
                        "name": "Sales Returns",
                        "code": "SR001"
                    },
                    "journalType": {
                        "id": 1,
                        "name": "Debit"
                    }
                },
                {
                    "id": 2,
                    "journal_type_id": 2,
                    "amount": 150.00,
                    "description": "Accounts Receivable credit for Return Item #456",
                    "account_id": 3,
                    "account_no": "AR001",
                    "account": {
                        "id": 3,
                        "name": "Accounts Receivable",
                        "code": "AR001"
                    },
                    "journalType": {
                        "id": 2,
                        "name": "Credit"
                    }
                }
            ]
        }
    ]
}
```

## Implementation Details

### 1. AccountingEntryService Extension

Added `generateReturnItemEntries($returnItem)` method to handle return item accounting:

```php
public function generateReturnItemEntries($returnItem)
{
    return DB::transaction(function () use ($returnItem) {
        // Calculate total return amount
        $totalReturnAmount = $returnItem->returnDetails->sum(function ($detail) {
            return $detail->return_quantity * $detail->unit_price;
        });

        // Create journal entry
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

        // Create transaction records
        $this->createTransactions([
            'description' => "Return Item #{$returnItem->id}",
            'transaction_date' => $returnItem->return_date ?? now(),
            'transcode' => 'RI',
            'transtype' => 'Return',
            'naration' => "Return Item #{$returnItem->id} - Customer Return",
            'debit' => $totalReturnAmount,
            'credit' => $totalReturnAmount,
            'amount' => $totalReturnAmount,
            'store_id' => $returnItem->store_id ?? null,
            'branch_id' => $returnItem->branch_id ?? null,
            'account_no' => $this->getAccountNo('Sales Returns'),
            'account_id' => $this->getAccountId('Sales Returns'),
            'created_by' => auth()->id()
        ]);

        return $journalEntry;
    });
}
```

### 2. ReturnItemController Modifications

#### Constructor Injection
```php
protected $accountingEntryService;

public function __construct(AccountingEntryService $accountingEntryService)
{
    $this->accountingEntryService = $accountingEntryService;
}
```

#### Modified Store Method
```php
public function store(ReturnItemStoreRequest $request): ReturnItemResource
{
    return DB::transaction(function () use ($request) {
        $returnItem = ReturnItem::create($request->validated());

        // Generate accounting entries for the return item
        try {
            $this->accountingEntryService->generateReturnItemEntries($returnItem);
        } catch (\Exception $e) {
            Log::error('Failed to generate accounting entries for return item', [
                'return_item_id' => $returnItem->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw the exception to avoid rolling back the main transaction
        }

        return new ReturnItemResource($returnItem);
    });
}
```

#### New Methods Added
- `generateAccountingEntries($id)`: Generate entries for a single return item
- `generateBulkAccountingEntries(Request $request)`: Generate entries for multiple return items
- `getAccountingEntries($id)`: Retrieve existing journal entries

## Accounting Logic

### Double-Entry Bookkeeping for Returns

1. **Sales Returns (Debit)**: Reduces revenue by the return amount
2. **Accounts Receivable/Cash (Credit)**: Credits the customer account or cash/bank account

### Payment Type Handling

- **Credit Sales**: Credits Accounts Receivable
- **Cash Sales**: Credits Cash/Bank account
- **Default**: Credits Cash/Bank if no sales receipt or order found

### Amount Calculation

The return amount is calculated as:
```php
$totalReturnAmount = $returnItem->returnDetails->sum(function ($detail) {
    return $detail->return_quantity * $detail->unit_price;
});
```

## Usage Examples

### 1. Create a Return Item (Automatic Accounting Entries)
```bash
POST /api/return-items
Content-Type: application/json

{
    "customer_id": 1,
    "branch_id": 2,
    "store_id": 1,
    "return_date": "2024-01-15",
    "notes": "Customer returned damaged items",
    "return_status": "pending"
}
```

### 2. Generate Accounting Entries Manually
```bash
POST /api/return-items/456/generate-accounting-entries
```

### 3. Generate Bulk Accounting Entries
```bash
POST /api/return-items/generate-bulk-accounting-entries
Content-Type: application/json

{
    "return_item_ids": [1, 2, 3, 4, 5]
}
```

### 4. Retrieve Accounting Entries
```bash
GET /api/return-items/456/accounting-entries
```

## Benefits

1. **Automated Accounting**: Automatic generation of accounting entries when return items are created
2. **Data Consistency**: Uses database transactions to ensure atomicity
3. **Error Resilience**: Logs errors without disrupting the main transaction
4. **Flexibility**: Supports both automatic and manual generation
5. **Audit Trail**: Complete tracking of all accounting entries
6. **Bulk Operations**: Efficient processing of multiple return items
7. **Payment Type Awareness**: Handles different payment types appropriately

## Error Handling

- **Missing Accounts**: Logs errors when required accounts are not found
- **Duplicate Entries**: Prevents creation of duplicate accounting entries
- **Transaction Failures**: Graceful handling of database transaction failures
- **Validation Errors**: Proper validation of input data

## Dependencies

- `AccountingEntryService`: Core service for accounting entry generation
- `ReturnItem` Model: Main model for return items
- `ReturnDetails` Model: Details of returned items
- `TransactionJournalEntry`: Journal entry records
- `TransactionJournalEntryDetail`: Journal entry detail records
- `Transaction`: Transaction records for reporting

## Testing Considerations

1. **Unit Tests**: Test individual accounting entry generation
2. **Integration Tests**: Test complete return item workflow
3. **Error Scenarios**: Test missing accounts, invalid data
4. **Bulk Operations**: Test multiple return item processing
5. **Payment Types**: Test different payment type scenarios

## Future Enhancements

1. **Tax Handling**: Add support for tax calculations in returns
2. **Discount Adjustments**: Handle discount adjustments in returns
3. **Partial Returns**: Support for partial item returns
4. **Return Reasons**: Track return reasons for analytics
5. **Approval Workflow**: Integrate with approval processes
6. **Reporting**: Enhanced reporting for return analytics 