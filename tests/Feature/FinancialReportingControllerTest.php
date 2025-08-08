<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\FinancialPeriod;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class FinancialReportingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_generate_profit_and_loss_statement()
    {
        $this->actingAs($this->user);

        // Create account types
        $revenueType = AccountType::create([
            'name' => 'Revenue',
            'code' => 'REVENUE',
            'account_group_id' => AccountGroup::create(['name' => 'Profit and Loss'])->id,
        ]);

        $expenseType = AccountType::create([
            'name' => 'Expense',
            'code' => 'EXPENSE',
            'account_group_id' => AccountGroup::create(['name' => 'Profit and Loss'])->id,
        ]);

        // Create accounts
        $revenueAccount = Account::create([
            'name' => 'Sales Revenue',
            'code' => '4000',
            'account_type_id' => $revenueType->id,
            'account_group_id' => $revenueType->account_group_id,
        ]);

        $expenseAccount = Account::create([
            'name' => 'Cost of Goods Sold',
            'code' => '5000',
            'account_type_id' => $expenseType->id,
            'account_group_id' => $expenseType->account_group_id,
        ]);

        // Create transactions
        Transaction::create([
            'account_id' => $revenueAccount->id,
            'transaction_date' => '2024-01-15',
            'credit' => 50000,
            'debit' => 0,
            'amount' => 50000,
        ]);

        Transaction::create([
            'account_id' => $expenseAccount->id,
            'transaction_date' => '2024-01-15',
            'debit' => 30000,
            'credit' => 0,
            'amount' => 30000,
        ]);

        $response = $this->postJson('/api/financial-reporting/profit-and-loss', [
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Profit and Loss Statement generated successfully',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'period',
                        'revenues',
                        'expenses',
                        'total_revenue',
                        'total_expenses',
                        'net_income',
                        'generated_at',
                    ],
                ]);
    }

    /** @test */
    public function it_can_generate_balance_sheet()
    {
        $this->actingAs($this->user);

        // Create account types
        $assetType = AccountType::create([
            'name' => 'Asset',
            'code' => 'ASSET',
            'account_group_id' => AccountGroup::create(['name' => 'Balance Sheet'])->id,
        ]);

        $liabilityType = AccountType::create([
            'name' => 'Liability',
            'code' => 'LIABILITY',
            'account_group_id' => AccountGroup::create(['name' => 'Balance Sheet'])->id,
        ]);

        $equityType = AccountType::create([
            'name' => 'Equity',
            'code' => 'EQUITY',
            'account_group_id' => AccountGroup::create(['name' => 'Balance Sheet'])->id,
        ]);

        // Create accounts
        $cashAccount = Account::create([
            'name' => 'Cash',
            'code' => '1000',
            'account_type_id' => $assetType->id,
            'account_group_id' => $assetType->account_group_id,
        ]);

        $payableAccount = Account::create([
            'name' => 'Accounts Payable',
            'code' => '2000',
            'account_type_id' => $liabilityType->id,
            'account_group_id' => $liabilityType->account_group_id,
        ]);

        $capitalAccount = Account::create([
            'name' => 'Owner\'s Capital',
            'code' => '3000',
            'account_type_id' => $equityType->id,
            'account_group_id' => $equityType->account_group_id,
        ]);

        // Create transactions
        Transaction::create([
            'account_id' => $cashAccount->id,
            'transaction_date' => '2024-01-15',
            'debit' => 50000,
            'credit' => 0,
            'amount' => 50000,
        ]);

        Transaction::create([
            'account_id' => $payableAccount->id,
            'transaction_date' => '2024-01-15',
            'credit' => 20000,
            'debit' => 0,
            'amount' => 20000,
        ]);

        Transaction::create([
            'account_id' => $capitalAccount->id,
            'transaction_date' => '2024-01-15',
            'credit' => 30000,
            'debit' => 0,
            'amount' => 30000,
        ]);

        $response = $this->postJson('/api/financial-reporting/balance-sheet', [
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Balance Sheet generated successfully',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'period',
                        'assets',
                        'liabilities',
                        'equity',
                        'total_assets',
                        'total_liabilities',
                        'total_equity',
                        'total_liabilities_and_equity',
                        'generated_at',
                    ],
                ]);
    }

    /** @test */
    public function it_can_get_financial_periods()
    {
        $this->actingAs($this->user);

        // Create a financial period
        $period = FinancialPeriod::create([
            'name' => 'Q1 2024',
            'date_from' => '2024-01-01',
            'date_to' => '2024-03-31',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/financial-reporting/financial-periods');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Financial periods retrieved successfully',
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'date_from',
                            'date_to',
                            'financial_year',
                            'is_active',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function it_can_get_account_summary()
    {
        $this->actingAs($this->user);

        // Create account type and account
        $assetType = AccountType::create([
            'name' => 'Asset',
            'code' => 'ASSET',
            'account_group_id' => AccountGroup::create(['name' => 'Balance Sheet'])->id,
        ]);

        Account::create([
            'name' => 'Cash',
            'code' => '1000',
            'account_type_id' => $assetType->id,
            'account_group_id' => $assetType->account_group_id,
        ]);

        $response = $this->getJson('/api/financial-reporting/account-summary?account_type=Asset');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Account summary retrieved successfully',
                ])
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'code',
                            'account_type',
                            'account_group',
                        ],
                    ],
                ]);
    }

    /** @test */
    public function it_can_get_financial_metrics()
    {
        $this->actingAs($this->user);

        // Create account types and accounts
        $revenueType = AccountType::create([
            'name' => 'Revenue',
            'code' => 'REVENUE',
            'account_group_id' => AccountGroup::create(['name' => 'Profit and Loss'])->id,
        ]);

        $expenseType = AccountType::create([
            'name' => 'Expense',
            'code' => 'EXPENSE',
            'account_group_id' => AccountGroup::create(['name' => 'Profit and Loss'])->id,
        ]);

        $assetType = AccountType::create([
            'name' => 'Asset',
            'code' => 'ASSET',
            'account_group_id' => AccountGroup::create(['name' => 'Balance Sheet'])->id,
        ]);

        $revenueAccount = Account::create([
            'name' => 'Sales Revenue',
            'code' => '4000',
            'account_type_id' => $revenueType->id,
            'account_group_id' => $revenueType->account_group_id,
        ]);

        $expenseAccount = Account::create([
            'name' => 'Cost of Goods Sold',
            'code' => '5000',
            'account_type_id' => $expenseType->id,
            'account_group_id' => $expenseType->account_group_id,
        ]);

        $cashAccount = Account::create([
            'name' => 'Cash',
            'code' => '1000',
            'account_type_id' => $assetType->id,
            'account_group_id' => $assetType->account_group_id,
        ]);

        // Create transactions
        Transaction::create([
            'account_id' => $revenueAccount->id,
            'transaction_date' => '2024-01-15',
            'credit' => 50000,
            'debit' => 0,
            'amount' => 50000,
        ]);

        Transaction::create([
            'account_id' => $expenseAccount->id,
            'transaction_date' => '2024-01-15',
            'debit' => 30000,
            'credit' => 0,
            'amount' => 30000,
        ]);

        Transaction::create([
            'account_id' => $cashAccount->id,
            'transaction_date' => '2024-01-15',
            'debit' => 50000,
            'credit' => 0,
            'amount' => 50000,
        ]);

        $response = $this->getJson('/api/financial-reporting/financial-metrics?date_from=2024-01-01&date_to=2024-01-31');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Financial metrics retrieved successfully',
                ])
                ->assertJsonStructure([
                    'data' => [
                        'total_revenue',
                        'total_expenses',
                        'net_income',
                        'total_assets',
                        'total_liabilities',
                        'total_equity',
                        'profit_margin',
                        'debt_to_equity_ratio',
                        'current_ratio',
                    ],
                ]);
    }

    /** @test */
    public function it_validates_date_range()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/financial-reporting/profit-and-loss', [
            'date_from' => '2024-12-31',
            'date_to' => '2024-01-01', // Invalid: date_to before date_from
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['date_to']);
    }

    /** @test */
    public function it_validates_financial_period_id()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/financial-reporting/profit-and-loss', [
            'financial_period_id' => 'invalid-uuid',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['financial_period_id']);
    }

    /** @test */
    public function it_can_generate_trial_balance()
    {
        // Create test data
        $accountType = AccountType::factory()->create(['name' => 'Asset']);
        $accountGroup = AccountGroup::factory()->create();
        
        $account1 = Account::factory()->create([
            'account_type_id' => $accountType->id,
            'account_group_id' => $accountGroup->id,
            'code' => '1000',
            'name' => 'Cash',
        ]);
        
        $account2 = Account::factory()->create([
            'account_type_id' => $accountType->id,
            'account_group_id' => $accountGroup->id,
            'code' => '1100',
            'name' => 'Bank',
        ]);

        $response = $this->postJson('/api/financial-reporting/trial-balance', [
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Trial Balance generated successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'period',
                    'trial_balance',
                    'total_debits',
                    'total_credits',
                    'difference',
                    'is_balanced',
                    'generated_at',
                ],
                'is_balanced',
            ]);
    }

    /** @test */
    public function it_can_generate_detailed_trial_balance()
    {
        // Create test data
        $assetType = AccountType::factory()->create(['name' => 'Asset']);
        $liabilityType = AccountType::factory()->create(['name' => 'Liability']);
        $accountGroup = AccountGroup::factory()->create();
        
        $cashAccount = Account::factory()->create([
            'account_type_id' => $assetType->id,
            'account_group_id' => $accountGroup->id,
            'code' => '1000',
            'name' => 'Cash',
        ]);
        
        $bankAccount = Account::factory()->create([
            'account_type_id' => $assetType->id,
            'account_group_id' => $accountGroup->id,
            'code' => '1100',
            'name' => 'Bank',
        ]);

        $response = $this->postJson('/api/financial-reporting/trial-balance', [
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31',
            'detailed' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Trial Balance generated successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'period',
                    'trial_balance',
                    'total_debits',
                    'total_credits',
                    'difference',
                    'is_balanced',
                    'detailed_breakdown',
                    'generated_at',
                ],
                'is_balanced',
            ]);
    }

    /** @test */
    public function it_can_get_trial_balance_summary()
    {
        // Create test data
        $accountType = AccountType::factory()->create(['name' => 'Asset']);
        $accountGroup = AccountGroup::factory()->create();
        
        Account::factory()->create([
            'account_type_id' => $accountType->id,
            'account_group_id' => $accountGroup->id,
            'code' => '1000',
            'name' => 'Cash',
        ]);

        $response = $this->getJson('/api/financial-reporting/trial-balance-summary?date_from=2024-01-01&date_to=2024-01-31');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Trial Balance summary retrieved successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'total_accounts',
                    'total_debits',
                    'total_credits',
                    'difference',
                    'is_balanced',
                    'period',
                    'account_types_breakdown',
                ],
            ]);
    }

    /** @test */
    public function it_validates_trial_balance_date_range()
    {
        $response = $this->postJson('/api/financial-reporting/trial-balance', [
            'date_from' => '2024-01-31',
            'date_to' => '2024-01-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date_to']);
    }

    /** @test */
    public function it_validates_trial_balance_format()
    {
        $response = $this->postJson('/api/financial-reporting/trial-balance', [
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31',
            'format' => 'invalid-format',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format']);
    }
} 