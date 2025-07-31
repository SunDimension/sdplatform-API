<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountType;
use App\Models\AccountSubtype;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
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

        // Get account groups, types, and subtypes
        $accountGroup = AccountGroup::first();
        if (!$accountGroup) {
            $accountGroup = AccountGroup::create([
                'name' => 'Default Account Group',
                'code' => 'DEFAULT',
                'created_by' => $user->id,
            ]);
        }

        $assetType = AccountType::where('name', 'Asset')->first();
        $liabilityType = AccountType::where('name', 'Liability')->first();
        $equityType = AccountType::where('name', 'Equity')->first();
        $revenueType = AccountType::where('name', 'Revenue')->first();
        $expenseType = AccountType::where('name', 'Expense')->first();

        // Get subtypes
        $currentAssetSubtype = AccountSubtype::where('name', 'Current Asset')->first();
        $fixedAssetSubtype = AccountSubtype::where('name', 'Fixed Asset')->first();
        $currentLiabilitySubtype = AccountSubtype::where('name', 'Current Liability')->first();
        $longTermLiabilitySubtype = AccountSubtype::where('name', 'Long-term Liability')->first();
        $ownersEquitySubtype = AccountSubtype::where('name', 'Owner\'s Equity')->first();
        $salesRevenueSubtype = AccountSubtype::where('name', 'Sales Revenue')->first();
        $operatingExpenseSubtype = AccountSubtype::where('name', 'Operating Expense')->first();

        // Create standard accounting accounts
        $accounts = [
            // Asset Accounts
            [
                'name' => 'Cash',
                'code' => '1000',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $assetType ? $assetType->id : 1,
                'account_subtype_id' => $currentAssetSubtype ? $currentAssetSubtype->id : 1,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Bank Account',
                'code' => '1010',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $assetType ? $assetType->id : 1,
                'account_subtype_id' => $currentAssetSubtype ? $currentAssetSubtype->id : 1,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Accounts Receivable',
                'code' => '1100',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $assetType ? $assetType->id : 1,
                'account_subtype_id' => $currentAssetSubtype ? $currentAssetSubtype->id : 1,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Inventory',
                'code' => '1200',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $assetType ? $assetType->id : 1,
                'account_subtype_id' => $currentAssetSubtype ? $currentAssetSubtype->id : 1,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Equipment',
                'code' => '1500',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $assetType ? $assetType->id : 1,
                'account_subtype_id' => $fixedAssetSubtype ? $fixedAssetSubtype->id : 2,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Buildings',
                'code' => '1600',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $assetType ? $assetType->id : 1,
                'account_subtype_id' => $fixedAssetSubtype ? $fixedAssetSubtype->id : 2,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],

            // Liability Accounts
            [
                'name' => 'Accounts Payable',
                'code' => '2000',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'account_subtype_id' => $currentLiabilitySubtype ? $currentLiabilitySubtype->id : 5,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Notes Payable',
                'code' => '2100',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'account_subtype_id' => $longTermLiabilitySubtype ? $longTermLiabilitySubtype->id : 6,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Accrued Expenses',
                'code' => '2200',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'account_subtype_id' => $currentLiabilitySubtype ? $currentLiabilitySubtype->id : 5,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],

            // Equity Accounts
            [
                'name' => 'Owner\'s Capital',
                'code' => '3000',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $equityType ? $equityType->id : 3,
                'account_subtype_id' => $ownersEquitySubtype ? $ownersEquitySubtype->id : 9,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Retained Earnings',
                'code' => '3100',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $equityType ? $equityType->id : 3,
                'account_subtype_id' => $ownersEquitySubtype ? $ownersEquitySubtype->id : 9,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],

            // Revenue Accounts
            [
                'name' => 'Sales Revenue',
                'code' => '4000',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'account_subtype_id' => $salesRevenueSubtype ? $salesRevenueSubtype->id : 13,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Service Revenue',
                'code' => '4100',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'account_subtype_id' => $salesRevenueSubtype ? $salesRevenueSubtype->id : 13,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],

            // Expense Accounts
            [
                'name' => 'Cost of Goods Sold',
                'code' => '5000',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'account_subtype_id' => $operatingExpenseSubtype ? $operatingExpenseSubtype->id : 15,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Salaries and Wages',
                'code' => '5100',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'account_subtype_id' => $operatingExpenseSubtype ? $operatingExpenseSubtype->id : 15,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Rent Expense',
                'code' => '5200',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'account_subtype_id' => $operatingExpenseSubtype ? $operatingExpenseSubtype->id : 15,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Utilities Expense',
                'code' => '5300',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'account_subtype_id' => $operatingExpenseSubtype ? $operatingExpenseSubtype->id : 15,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
            [
                'name' => 'Office Supplies',
                'code' => '5400',
                'account_group_id' => $accountGroup->id,
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'account_subtype_id' => $operatingExpenseSubtype ? $operatingExpenseSubtype->id : 15,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
