<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\AccountingEntryService;
use App\Models\TransactionJournalEntry;
use App\Models\TransactionJournalEntryDetail;
use Illuminate\Foundation\Testing\WithFaker;

class AccountingEntryServiceTest extends TestCase
{
    use WithFaker;

    protected $accountingService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->accountingService = new AccountingEntryService();
    }

    /** @test */
    public function it_can_instantiate_accounting_entry_service()
    {
        $this->assertInstanceOf(AccountingEntryService::class, $this->accountingService);
    }

    /** @test */
    public function it_can_create_transaction_journal_entry_model()
    {
        $entry = new TransactionJournalEntry();
        $this->assertInstanceOf(TransactionJournalEntry::class, $entry);
    }

    /** @test */
    public function it_can_create_transaction_journal_entry_detail_model()
    {
        $detail = new TransactionJournalEntryDetail();
        $this->assertInstanceOf(TransactionJournalEntryDetail::class, $detail);
    }

    /** @test */
    public function transaction_journal_entry_has_correct_fillable_fields()
    {
        $expectedFillable = [
            'description',
            'payment_date',
            'store_id',
            'branch_id',
            'vendor_id',
            'created_by',
            'modified_by',
            'deleted_by',
            'approval_stage_id',
            'approval_officer_id'
        ];

        $entry = new TransactionJournalEntry();
        $this->assertEquals($expectedFillable, $entry->getFillable());
    }

    /** @test */
    public function transaction_journal_entry_detail_has_correct_fillable_fields()
    {
        $expectedFillable = [
            'transaction_journal_entry_id',
            'journal_type_id',
            'amount',
            'description',
            'account_id',
            'account_no',
            'created_by',
            'modified_by',
            'deleted_by',
        ];

        $detail = new TransactionJournalEntryDetail();
        $this->assertEquals($expectedFillable, $detail->getFillable());
    }

    /** @test */
    public function it_has_required_methods()
    {
        $methods = get_class_methods($this->accountingService);
        
        $requiredMethods = [
            'createInventoryEntries',
            'createSalesEntries',
            'createPurchaseEntries',
            'createStockTransferEntries',
            'createReturnEntries',
            'generateSalesOrderEntries',
            'generateSalesReceiptEntries'
        ];

        foreach ($requiredMethods as $method) {
            $this->assertContains($method, $methods, "Method {$method} should exist in AccountingEntryService");
        }
    }

    /** @test */
    public function it_uses_correct_model_classes()
    {
        $reflection = new \ReflectionClass($this->accountingService);
        $sourceCode = file_get_contents($reflection->getFileName());
        
        // Check that the service uses the new models
        $this->assertStringContainsString('TransactionJournalEntry', $sourceCode);
        $this->assertStringContainsString('TransactionJournalEntryDetail', $sourceCode);
        
        // Check that it doesn't use the old models
        $this->assertStringNotContainsString('JournalEntry::', $sourceCode);
        $this->assertStringNotContainsString('JournalEntryDetail::', $sourceCode);
    }

    /** @test */
    public function it_uses_correct_foreign_key_names()
    {
        $reflection = new \ReflectionClass($this->accountingService);
        $sourceCode = file_get_contents($reflection->getFileName());
        
        // Check that it uses the correct foreign key name
        $this->assertStringContainsString('transaction_journal_entry_id', $sourceCode);
        $this->assertStringNotContainsString('journal_entry_id', $sourceCode);
    }

    /** @test */
    public function it_has_correct_imports()
    {
        $reflection = new \ReflectionClass($this->accountingService);
        $sourceCode = file_get_contents($reflection->getFileName());
        
        // Check that it imports the correct models
        $this->assertStringContainsString('use App\Models\TransactionJournalEntry;', $sourceCode);
        $this->assertStringContainsString('use App\Models\TransactionJournalEntryDetail;', $sourceCode);
        
        // Check that it doesn't import the old models
        $this->assertStringNotContainsString('use App\Models\JournalEntry;', $sourceCode);
        $this->assertStringNotContainsString('use App\Models\JournalEntryDetail;', $sourceCode);
    }
}
