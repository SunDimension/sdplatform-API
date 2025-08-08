<?php

namespace Database\Seeders;

use App\Models\AccountSubtype;
use App\Models\AccountType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSubtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'status_id' => 1,
                'branch_id' => 1,
                'warehouse_id' => 1,
            ]);
        }

        // Get account types
        $assetType = AccountType::where('name', 'Asset')->first();
        $liabilityType = AccountType::where('name', 'Liability')->first();
        $equityType = AccountType::where('name', 'Equity')->first();
        $revenueType = AccountType::where('name', 'Revenue')->first();
        $expenseType = AccountType::where('name', 'Expense')->first();

        // Create comprehensive account subtypes based on the chart of accounts
        $accountSubtypes = [
            // Asset subtypes - based on the actual account structure
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Fixed Assets',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Tangible Fixed Assets',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Land and Buildings',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Plant & Machinery',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Motor Vehicles',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Office Equipment',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Current Assets',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Inventory',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Inventory - Domestic',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Inventory - Foreign',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Accounts Receivable',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Customers - Domestic',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Customers - Foreign',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Liquid Assets',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Cash Accounts',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Bank Accounts',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Other Receivables',
                'created_by' => $user->id,
            ],

            // Liability subtypes - based on the actual account structure
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Current Liabilities',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Revolving Credits',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Tax Payables',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'VAT Payables',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Withholding Tax Payables',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Accounts Payable',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Vendors - Domestic',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Vendors - Foreign',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Other Payables',
                'created_by' => $user->id,
            ],

            // Equity subtypes - based on the actual account structure
            [
                'account_type_id' => $equityType ? $equityType->id : 3,
                'name' => 'Capital & Reserves',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $equityType ? $equityType->id : 3,
                'name' => 'Capital Employed',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $equityType ? $equityType->id : 3,
                'name' => 'Reserves',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $equityType ? $equityType->id : 3,
                'name' => 'Profit Realised',
                'created_by' => $user->id,
            ],

            // Revenue subtypes - based on the actual account structure
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Sales Revenue',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Sales - Domestic',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Sales - Foreign',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Fees & Commissions',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Other Income',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Discount Received',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Interest Received',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Gain on Sales of Fixed Assets',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Incentives',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Rebate',
                'created_by' => $user->id,
            ],

            // Expense subtypes - based on the actual account structure
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Salaries & Wages',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Office Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Other Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Selling & Distribution Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Repairs & Maintenance',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Depreciation Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Cost of Goods Sold',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Direct Cost Applied',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Purchase Variant Account',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Discount Allowed',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Carriage Outward',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Utility Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Tax Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Professional Fees',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Miscellaneous Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Cash Shortages',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Security Expenses',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Donations/Gifts',
                'created_by' => $user->id,
            ],
        ];

        foreach ($accountSubtypes as $subtype) {
            AccountSubtype::create($subtype);
        }
    }
} 