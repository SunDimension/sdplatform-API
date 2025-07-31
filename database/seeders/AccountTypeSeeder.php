<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a basic user if none exists
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role_id' => 1, // Assuming role_id 1 exists
                'status_id' => 1, // Assuming status_id 1 exists
                'branch_id' => 1, // Assuming branch_id 1 exists
                'warehouse_id' => 1, // Assuming warehouse_id 1 exists
            ]);
        }

        // Create a basic account group if none exists
        $accountGroup = AccountGroup::first();
        if (!$accountGroup) {
            $accountGroup = AccountGroup::create([
                'name' => 'Default Account Group',
                'code' => 'DEFAULT',
                'created_by' => $user->id,
            ]);
        }

        // Create account types directly
        $accountTypes = [
            [
                'account_group_id' => $accountGroup->id,
                'name' => 'Asset',
                'code' => 'ASSET',
                'created_by' => $user->id,
            ],
            [
                'account_group_id' => $accountGroup->id,
                'name' => 'Liability',
                'code' => 'LIABILITY',
                'created_by' => $user->id,
            ],
            [
                'account_group_id' => $accountGroup->id,
                'name' => 'Equity',
                'code' => 'EQUITY',
                'created_by' => $user->id,
            ],
            [
                'account_group_id' => $accountGroup->id,
                'name' => 'Revenue',
                'code' => 'REVENUE',
                'created_by' => $user->id,
            ],
            [
                'account_group_id' => $accountGroup->id,
                'name' => 'Expense',
                'code' => 'EXPENSE',
                'created_by' => $user->id,
            ],
        ];

        foreach ($accountTypes as $accountTypeData) {
            AccountType::create($accountTypeData);
        }
    }
}
