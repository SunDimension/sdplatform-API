# Accounting Entries Implementation for SalesReceiptController

## Overview

This implementation adds comprehensive accounting entry functionality to the SalesReceiptController, allowing automatic generation of journal entries when sales receipts are created and providing additional endpoints for managing accounting entries.

## Features Implemented

### 1. Automatic Accounting Entry Generation
- When a sales receipt is created via the `store` method, accounting entries are automatically generated
- Uses the existing `AccountingEntryService` to create proper journal entries
- Handles both cash and credit payment types
- Creates entries for Cash/Bank (debit) and Accounts Receivable (credit)

### 2. New Controller Methods

#### `generateAccountingEntries($id)`
- Generates accounting entries for a specific sales receipt
- Checks for existing entries to prevent duplicates
- Returns success/error response with journal entry data

#### `generateBulkAccountingEntries(Request $request)`
- Processes multiple sales receipts at once
- Accepts an array of sales receipt IDs
- Returns summary of successful and failed operations

#### `getAccountingEntries($id)`
- Retrieves existing accounting entries for a sales receipt
- Returns journal entries with their details

### 3. New API Routes

```php
// Generate accounting entries for a specific sales receipt
POST /api/sales-receipts/{id}/generate-accounting-entries

// Generate accounting entries for multiple sales receipts
POST /api/sales-receipts/generate-bulk-accounting-entries

// Get accounting entries for a sales receipt
GET /api/sales-receipts/{id}/accounting-entries
```

## Implementation Details

### Dependencies
- `AccountingEntryService`: Handles the creation of journal entries
- `TransactionJournalEntry`: Model for journal entries
- `TransactionJournalEntryDetail`: Model for journal entry details

### Database Transactions
- All accounting entry operations are wrapped in database transactions
- Ensures data consistency and rollback on errors

### Error Handling
- Comprehensive error handling with proper logging
- Graceful degradation - accounting entry failures don't prevent sales receipt creation
- Detailed error messages for debugging

## Usage Examples

### Creating a Sales Receipt with Automatic Accounting Entries
```php
// The store method automatically generates accounting entries
$response = $this->post('/api/sales-receipts', [
    'customer_id' => 1,
    'branch_id' => 1,
    'store_id' => 1,
    'amount_paid' => 1000.00,
    'payment_type' => 'Cash',
    // ... other fields
]);
```

### Generating Accounting Entries for Existing Receipts
```php
// Generate entries for a specific receipt
$response = $this->post('/api/sales-receipts/1/generate-accounting-entries');

// Generate entries for multiple receipts
$response = $this->post('/api/sales-receipts/generate-bulk-accounting-entries', [
    'sales_receipt_ids' => [1, 2, 3]
]);
```

### Retrieving Accounting Entries
```php
// Get accounting entries for a receipt
$response = $this->get('/api/sales-receipts/1/accounting-entries');
```

## Accounting Entry Structure

### For Cash Payments
- **Debit**: Cash/Bank Account
- **Credit**: Accounts Receivable

### For Credit Payments
- **Debit**: Cash/Bank Account  
- **Credit**: Accounts Receivable

### Journal Entry Details
- Transaction date: Sales receipt payment date
- Description: "Sales Receipt #[receipt_number]"
- Store and branch information included
- Created by: Authenticated user

## Testing

The implementation includes comprehensive tests for:
- Individual accounting entry generation
- Bulk accounting entry generation
- Retrieving accounting entries
- Error handling scenarios

## Benefits

1. **Automated Accounting**: No manual journal entry creation required
2. **Data Integrity**: Proper double-entry bookkeeping
3. **Audit Trail**: Complete tracking of all financial transactions
4. **Flexibility**: Support for both individual and bulk operations
5. **Error Recovery**: Ability to regenerate entries for existing receipts

## Future Enhancements

1. Support for different payment methods (credit cards, bank transfers)
2. Integration with tax calculations
3. Support for discounts and returns
4. Advanced reporting and reconciliation features
5. Integration with external accounting systems 