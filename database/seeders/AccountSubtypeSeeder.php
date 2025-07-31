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

        // Create account subtypes data records
        $accountSubtypes = [
            // Asset subtypes
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Current Asset',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Fixed Asset',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Intangible Asset',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $assetType ? $assetType->id : 1,
                'name' => 'Other Asset',
                'created_by' => $user->id,
            ],
            // Liability subtypes
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Current Liability',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Long-term Liability',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Deferred Liability',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $liabilityType ? $liabilityType->id : 2,
                'name' => 'Other Liability',
                'created_by' => $user->id,
            ],
            // Equity subtypes
            [
                'account_type_id' => $equityType ? $equityType->id : 3,
                'name' => 'Owner\'s Equity',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $equityType ? $equityType->id : 3,
                'name' => 'Retained Earnings',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $equityType ? $equityType->id : 3,
                'name' => 'Share Capital',
                'created_by' => $user->id,
            ],
            // Revenue subtypes
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Sales Revenue',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $revenueType ? $revenueType->id : 4,
                'name' => 'Service Revenue',
                'created_by' => $user->id,
            ],
            // Expense subtypes
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Operating Expense',
                'created_by' => $user->id,
            ],
            [
                'account_type_id' => $expenseType ? $expenseType->id : 5,
                'name' => 'Non-operating Expense',
                'created_by' => $user->id,
            ],
        ];

        foreach ($accountSubtypes as $subtype) {
            AccountSubtype::create($subtype);
        }
    }
} 