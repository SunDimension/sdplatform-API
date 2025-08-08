<?php

namespace Database\Factories;

use App\Models\PeriodicFinancialReport;
use App\Models\FinancialPeriod;
use App\Models\Store;
use App\Models\Branch;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PeriodicFinancialReport>
 */
class PeriodicFinancialReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reportTypes = [
            PeriodicFinancialReport::REPORT_TYPE_PROFIT_LOSS,
            PeriodicFinancialReport::REPORT_TYPE_BALANCE_SHEET,
            PeriodicFinancialReport::REPORT_TYPE_TRIAL_BALANCE
        ];

        $statuses = [
            PeriodicFinancialReport::STATUS_DRAFT,
            PeriodicFinancialReport::STATUS_FINAL,
            PeriodicFinancialReport::STATUS_ARCHIVED
        ];

        return [
            'id' => Str::uuid(),
            'report_type' => $this->faker->randomElement($reportTypes),
            'financial_period_id' => FinancialPeriod::factory(),
            'store_id' => Store::factory(),
            'branch_id' => Branch::factory(),
            'region_id' => Region::factory(),
            'report_data' => $this->generateSampleReportData(),
            'generated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'generated_by' => User::factory(),
            'is_balanced' => $this->faker->boolean(80), // 80% chance of being balanced
            'total_debits' => $this->faker->randomFloat(2, 1000, 100000),
            'total_credits' => $this->faker->randomFloat(2, 1000, 100000),
            'difference' => $this->faker->randomFloat(2, 0, 100),
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Generate sample report data based on report type
     */
    private function generateSampleReportData(): array
    {
        $reportType = $this->faker->randomElement([
            PeriodicFinancialReport::REPORT_TYPE_PROFIT_LOSS,
            PeriodicFinancialReport::REPORT_TYPE_BALANCE_SHEET,
            PeriodicFinancialReport::REPORT_TYPE_TRIAL_BALANCE
        ]);

        switch ($reportType) {
            case PeriodicFinancialReport::REPORT_TYPE_PROFIT_LOSS:
                return [
                    'period' => [
                        'id' => Str::uuid(),
                        'name' => 'January 2024',
                        'date_from' => '2024-01-01',
                        'date_to' => '2024-01-31',
                        'financial_year' => '2024'
                    ],
                    'revenues' => [
                        [
                            'account' => [
                                'id' => Str::uuid(),
                                'name' => 'Sales Revenue',
                                'code' => '4000',
                                'account_type' => 'Revenue',
                                'account_group' => 'Operating Revenue'
                            ],
                            'opening_balance' => 0,
                            'debit_total' => 0,
                            'credit_total' => 50000,
                            'balance' => 50000
                        ]
                    ],
                    'expenses' => [
                        [
                            'account' => [
                                'id' => Str::uuid(),
                                'name' => 'Cost of Goods Sold',
                                'code' => '5000',
                                'account_type' => 'Expense',
                                'account_group' => 'Cost of Sales'
                            ],
                            'opening_balance' => 0,
                            'debit_total' => 30000,
                            'credit_total' => 0,
                            'balance' => 30000
                        ]
                    ],
                    'total_revenue' => 50000,
                    'total_expenses' => 30000,
                    'net_income' => 20000,
                    'generated_at' => now()->toISOString(),
                    'scope' => [
                        'store_id' => 1,
                        'branch_id' => 1,
                        'region_id' => 1
                    ]
                ];

            case PeriodicFinancialReport::REPORT_TYPE_BALANCE_SHEET:
                return [
                    'period' => [
                        'id' => Str::uuid(),
                        'name' => 'January 2024',
                        'date_from' => '2024-01-01',
                        'date_to' => '2024-01-31',
                        'financial_year' => '2024'
                    ],
                    'assets' => [
                        [
                            'account' => [
                                'id' => Str::uuid(),
                                'name' => 'Cash',
                                'code' => '1000',
                                'account_type' => 'Asset',
                                'account_group' => 'Current Assets'
                            ],
                            'opening_balance' => 10000,
                            'debit_total' => 50000,
                            'credit_total' => 20000,
                            'balance' => 40000
                        ]
                    ],
                    'liabilities' => [
                        [
                            'account' => [
                                'id' => Str::uuid(),
                                'name' => 'Accounts Payable',
                                'code' => '2000',
                                'account_type' => 'Liability',
                                'account_group' => 'Current Liabilities'
                            ],
                            'opening_balance' => 5000,
                            'debit_total' => 10000,
                            'credit_total' => 15000,
                            'balance' => 10000
                        ]
                    ],
                    'equity' => [
                        [
                            'account' => [
                                'id' => Str::uuid(),
                                'name' => 'Retained Earnings',
                                'code' => '3000',
                                'account_type' => 'Equity',
                                'account_group' => 'Retained Earnings'
                            ],
                            'opening_balance' => 20000,
                            'debit_total' => 0,
                            'credit_total' => 20000,
                            'balance' => 40000
                        ]
                    ],
                    'total_assets' => 40000,
                    'total_liabilities' => 10000,
                    'total_equity' => 40000,
                    'total_liabilities_and_equity' => 50000,
                    'generated_at' => now()->toISOString(),
                    'scope' => [
                        'store_id' => 1,
                        'branch_id' => 1,
                        'region_id' => 1
                    ]
                ];

            case PeriodicFinancialReport::REPORT_TYPE_TRIAL_BALANCE:
                return [
                    'period' => [
                        'id' => Str::uuid(),
                        'name' => 'January 2024',
                        'date_from' => '2024-01-01',
                        'date_to' => '2024-01-31',
                        'financial_year' => '2024'
                    ],
                    'trial_balance' => [
                        [
                            'account' => [
                                'id' => Str::uuid(),
                                'name' => 'Cash',
                                'code' => '1000',
                                'account_type' => 'Asset',
                                'account_group' => 'Current Assets'
                            ],
                            'opening_balance' => 10000,
                            'debit_total' => 50000,
                            'credit_total' => 20000,
                            'balance' => 40000,
                            'is_debit_balance' => true,
                            'debit_balance' => 40000,
                            'credit_balance' => 0
                        ],
                        [
                            'account' => [
                                'id' => Str::uuid(),
                                'name' => 'Accounts Payable',
                                'code' => '2000',
                                'account_type' => 'Liability',
                                'account_group' => 'Current Liabilities'
                            ],
                            'opening_balance' => 5000,
                            'debit_total' => 10000,
                            'credit_total' => 15000,
                            'balance' => 10000,
                            'is_debit_balance' => false,
                            'debit_balance' => 0,
                            'credit_balance' => 10000
                        ]
                    ],
                    'total_debits' => 40000,
                    'total_credits' => 10000,
                    'difference' => 30000,
                    'is_balanced' => false,
                    'generated_at' => now()->toISOString(),
                    'scope' => [
                        'store_id' => 1,
                        'branch_id' => 1,
                        'region_id' => 1
                    ]
                ];

            default:
                return [];
        }
    }

    /**
     * Indicate that the report is a profit and loss statement
     */
    public function profitLoss(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => PeriodicFinancialReport::REPORT_TYPE_PROFIT_LOSS,
        ]);
    }

    /**
     * Indicate that the report is a balance sheet
     */
    public function balanceSheet(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => PeriodicFinancialReport::REPORT_TYPE_BALANCE_SHEET,
        ]);
    }

    /**
     * Indicate that the report is a trial balance
     */
    public function trialBalance(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => PeriodicFinancialReport::REPORT_TYPE_TRIAL_BALANCE,
        ]);
    }

    /**
     * Indicate that the report is balanced
     */
    public function balanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_balanced' => true,
            'difference' => 0,
        ]);
    }

    /**
     * Indicate that the report is unbalanced
     */
    public function unbalanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_balanced' => false,
            'difference' => $this->faker->randomFloat(2, 1, 1000),
        ]);
    }

    /**
     * Indicate that the report is in draft status
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PeriodicFinancialReport::STATUS_DRAFT,
        ]);
    }

    /**
     * Indicate that the report is in final status
     */
    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PeriodicFinancialReport::STATUS_FINAL,
        ]);
    }

    /**
     * Indicate that the report is archived
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PeriodicFinancialReport::STATUS_ARCHIVED,
        ]);
    }

    /**
     * For a specific store
     */
    public function forStore($storeId): static
    {
        return $this->state(fn (array $attributes) => [
            'store_id' => $storeId,
        ]);
    }

    /**
     * For a specific branch
     */
    public function forBranch($branchId): static
    {
        return $this->state(fn (array $attributes) => [
            'branch_id' => $branchId,
        ]);
    }

    /**
     * For a specific region
     */
    public function forRegion($regionId): static
    {
        return $this->state(fn (array $attributes) => [
            'region_id' => $regionId,
        ]);
    }
} 