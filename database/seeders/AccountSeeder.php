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

        // Get or create account groups
        $balanceSheetGroup = AccountGroup::where('name', 'Balance Sheet')->first();
        if (!$balanceSheetGroup) {
            $balanceSheetGroup = AccountGroup::create([
                'name' => 'Balance Sheet',
                'created_by' => $user->id,
            ]);
        }

        $profitLossGroup = AccountGroup::where('name', 'Profit and Loss')->first();
        if (!$profitLossGroup) {
            $profitLossGroup = AccountGroup::create([
                'name' => 'Profit and Loss',
                'created_by' => $user->id,
            ]);
        }

        // Get or create account types
        $assetType = AccountType::where('name', 'Asset')->first();
        if (!$assetType) {
            $assetType = AccountType::create([
                'account_group_id' => $balanceSheetGroup->id,
                'name' => 'Asset',
                'code' => 'ASSET',
                'created_by' => $user->id,
            ]);
        }

        $liabilityType = AccountType::where('name', 'Liability')->first();
        if (!$liabilityType) {
            $liabilityType = AccountType::create([
                'account_group_id' => $balanceSheetGroup->id,
                'name' => 'Liability',
                'code' => 'LIABILITY',
                'created_by' => $user->id,
            ]);
        }

        $equityType = AccountType::where('name', 'Equity')->first();
        if (!$equityType) {
            $equityType = AccountType::create([
                'account_group_id' => $balanceSheetGroup->id,
                'name' => 'Equity',
                'code' => 'EQUITY',
                'created_by' => $user->id,
            ]);
        }

        $revenueType = AccountType::where('name', 'Revenue')->first();
        if (!$revenueType) {
            $revenueType = AccountType::create([
                'account_group_id' => $profitLossGroup->id,
                'name' => 'Revenue',
                'code' => 'REVENUE',
                'created_by' => $user->id,
            ]);
        }

        $expenseType = AccountType::where('name', 'Expense')->first();
        if (!$expenseType) {
            $expenseType = AccountType::create([
                'account_group_id' => $profitLossGroup->id,
                'name' => 'Expense',
                'code' => 'EXPENSE',
                'created_by' => $user->id,
            ]);
        }

        // Get specific subtypes for better categorization
        $subtypes = [
            // Asset subtypes
            'Fixed Assets' => AccountSubtype::where('name', 'Fixed Assets')->first(),
            'Tangible Fixed Assets' => AccountSubtype::where('name', 'Tangible Fixed Assets')->first(),
            'Land and Buildings' => AccountSubtype::where('name', 'Land and Buildings')->first(),
            'Plant & Machinery' => AccountSubtype::where('name', 'Plant & Machinery')->first(),
            'Motor Vehicles' => AccountSubtype::where('name', 'Motor Vehicles')->first(),
            'Office Equipment' => AccountSubtype::where('name', 'Office Equipment')->first(),
            'Current Assets' => AccountSubtype::where('name', 'Current Assets')->first(),
            'Inventory' => AccountSubtype::where('name', 'Inventory')->first(),
            'Inventory - Domestic' => AccountSubtype::where('name', 'Inventory - Domestic')->first(),
            'Inventory - Foreign' => AccountSubtype::where('name', 'Inventory - Foreign')->first(),
            'Accounts Receivable' => AccountSubtype::where('name', 'Accounts Receivable')->first(),
            'Customers - Domestic' => AccountSubtype::where('name', 'Customers - Domestic')->first(),
            'Customers - Foreign' => AccountSubtype::where('name', 'Customers - Foreign')->first(),
            'Liquid Assets' => AccountSubtype::where('name', 'Liquid Assets')->first(),
            'Cash Accounts' => AccountSubtype::where('name', 'Cash Accounts')->first(),
            'Bank Accounts' => AccountSubtype::where('name', 'Bank Accounts')->first(),
            'Other Receivables' => AccountSubtype::where('name', 'Other Receivables')->first(),
            
            // Liability subtypes
            'Current Liabilities' => AccountSubtype::where('name', 'Current Liabilities')->first(),
            'Revolving Credits' => AccountSubtype::where('name', 'Revolving Credits')->first(),
            'Tax Payables' => AccountSubtype::where('name', 'Tax Payables')->first(),
            'VAT Payables' => AccountSubtype::where('name', 'VAT Payables')->first(),
            'Withholding Tax Payables' => AccountSubtype::where('name', 'Withholding Tax Payables')->first(),
            'Accounts Payable' => AccountSubtype::where('name', 'Accounts Payable')->first(),
            'Vendors - Domestic' => AccountSubtype::where('name', 'Vendors - Domestic')->first(),
            'Vendors - Foreign' => AccountSubtype::where('name', 'Vendors - Foreign')->first(),
            'Other Payables' => AccountSubtype::where('name', 'Other Payables')->first(),
            
            // Equity subtypes
            'Capital & Reserves' => AccountSubtype::where('name', 'Capital & Reserves')->first(),
            'Capital Employed' => AccountSubtype::where('name', 'Capital Employed')->first(),
            'Reserves' => AccountSubtype::where('name', 'Reserves')->first(),
            'Profit Realised' => AccountSubtype::where('name', 'Profit Realised')->first(),
            
            // Revenue subtypes
            'Sales Revenue' => AccountSubtype::where('name', 'Sales Revenue')->first(),
            'Sales - Domestic' => AccountSubtype::where('name', 'Sales - Domestic')->first(),
            'Sales - Foreign' => AccountSubtype::where('name', 'Sales - Foreign')->first(),
            'Fees & Commissions' => AccountSubtype::where('name', 'Fees & Commissions')->first(),
            'Other Income' => AccountSubtype::where('name', 'Other Income')->first(),
            'Discount Received' => AccountSubtype::where('name', 'Discount Received')->first(),
            'Interest Received' => AccountSubtype::where('name', 'Interest Received')->first(),
            'Gain on Sales of Fixed Assets' => AccountSubtype::where('name', 'Gain on Sales of Fixed Assets')->first(),
            'Incentives' => AccountSubtype::where('name', 'Incentives')->first(),
            'Rebate' => AccountSubtype::where('name', 'Rebate')->first(),
            
            // Expense subtypes
            'Salaries & Wages' => AccountSubtype::where('name', 'Salaries & Wages')->first(),
            'Office Expenses' => AccountSubtype::where('name', 'Office Expenses')->first(),
            'Other Expenses' => AccountSubtype::where('name', 'Other Expenses')->first(),
            'Selling & Distribution Expenses' => AccountSubtype::where('name', 'Selling & Distribution Expenses')->first(),
            'Repairs & Maintenance' => AccountSubtype::where('name', 'Repairs & Maintenance')->first(),
            'Depreciation Expenses' => AccountSubtype::where('name', 'Depreciation Expenses')->first(),
            'Cost of Goods Sold' => AccountSubtype::where('name', 'Cost of Goods Sold')->first(),
            'Direct Cost Applied' => AccountSubtype::where('name', 'Direct Cost Applied')->first(),
            'Purchase Variant Account' => AccountSubtype::where('name', 'Purchase Variant Account')->first(),
            'Discount Allowed' => AccountSubtype::where('name', 'Discount Allowed')->first(),
            'Carriage Outward' => AccountSubtype::where('name', 'Carriage Outward')->first(),
            'Utility Expenses' => AccountSubtype::where('name', 'Utility Expenses')->first(),
            'Tax Expenses' => AccountSubtype::where('name', 'Tax Expenses')->first(),
            'Professional Fees' => AccountSubtype::where('name', 'Professional Fees')->first(),
            'Miscellaneous Expenses' => AccountSubtype::where('name', 'Miscellaneous Expenses')->first(),
            'Cash Shortages' => AccountSubtype::where('name', 'Cash Shortages')->first(),
            'Security Expenses' => AccountSubtype::where('name', 'Security Expenses')->first(),
            'Donations/Gifts' => AccountSubtype::where('name', 'Donations/Gifts')->first(),
        ];

        // Fallback to first available subtype if specific one not found
        $defaultSubtype = AccountSubtype::first();

        // Create comprehensive chart of accounts
        $accounts = [
            // Balance Sheet - Assets
            ['code' => '10000', 'name' => 'Balance Sheet', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10001', 'name' => 'Assets', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10002', 'name' => 'Fixed Assets', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10003', 'name' => 'Tangible Fixed Assets', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10004', 'name' => 'Land And Buildings', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10005', 'name' => 'Acquisition Cost', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10006', 'name' => 'Accum. Depreciation', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10019', 'name' => 'Land And Buildings, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10020', 'name' => 'Plant & Machinery', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10021', 'name' => 'Acquisition Cost', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10022', 'name' => 'Accum. Depreciation', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10029', 'name' => 'Plant & Machinery, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10030', 'name' => 'Motor Vehicle', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10031', 'name' => 'Acquisition Cost', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10032', 'name' => 'Accum. Depreciation', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10039', 'name' => 'Motor Vehicle, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10040', 'name' => 'Office Equipment', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10041', 'name' => 'Acquisition Cost', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10042', 'name' => 'Accum. Depreciation', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10049', 'name' => 'Office Equipment, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10050', 'name' => 'Tangible Fixed Assets, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10051', 'name' => 'Fixed Assets, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10052', 'name' => 'Current Assets', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10053', 'name' => 'Inventory', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10054', 'name' => 'Inventory-Domestic', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10055', 'name' => 'Products- Domestic-Individual', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10056', 'name' => 'Products-Domestic-Corporate', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10057', 'name' => 'Products Domesticc- Others', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10079', 'name' => 'Inventory- Domestic, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10080', 'name' => 'Inventory-Foreign', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10081', 'name' => 'Products- Foreign-Individual', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10082', 'name' => 'Products-Foreign-Corporate', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10083', 'name' => 'Products Foreign- Others', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10099', 'name' => 'Inventory-Foreign, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10100', 'name' => 'Inventory, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10101', 'name' => 'Accounts Receivable', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10102', 'name' => 'Customers-Domestic', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10103', 'name' => 'Customers-Domestic-Individual', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10104', 'name' => 'Customers-Domestic-Corporate', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10105', 'name' => 'Customers-Domestic-Others', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10106', 'name' => 'Debtors', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10119', 'name' => 'Customers-Domestic, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10120', 'name' => 'Customers-Foreign', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10121', 'name' => 'Customers- Foreign- Individual', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10122', 'name' => 'Customers-Foreign-Corporate', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10123', 'name' => 'Customers-Foreign-Others', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10139', 'name' => 'Customers- Foreign- Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10140', 'name' => 'Accounts Receivable, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10141', 'name' => 'Liquid Assets', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10142', 'name' => 'Cash Accounts', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10143', 'name' => 'Vault (Till)', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10144', 'name' => 'Petty Cash', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10145', 'name' => 'Cashier One', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10146', 'name' => 'Cashier Two', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10147', 'name' => 'Cashier Three', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10169', 'name' => 'Cash Accounts, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10170', 'name' => 'Bank Accounts', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10171', 'name' => 'Access Bank', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10172', 'name' => 'FCMB Bank', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10173', 'name' => 'First Bank', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10174', 'name' => 'Sterling Bank', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10175', 'name' => 'UBA Bank', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10176', 'name' => 'Guaranty trust bank Plc', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10179', 'name' => 'Bank Accounts, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10198', 'name' => 'Liquid Assets, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10199', 'name' => 'Current Assets, Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10200', 'name' => 'Other Receivables', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10201', 'name' => 'Project Advance (HO)', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10219', 'name' => 'Other Receivables Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '10299', 'name' => 'Assets Total', 'account_type_id' => $assetType->id, 'account_group_id' => $balanceSheetGroup->id],

            // Balance Sheet - Liabilities
            ['code' => '20000', 'name' => 'Liabilities & Equity', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20001', 'name' => 'Current Liabilities', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20002', 'name' => 'Revolving Credits', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20003', 'name' => 'Loans From Commercial Banks', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20004', 'name' => 'Loans From Others', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20005', 'name' => 'Vat Purchases', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20006', 'name' => 'Vat Sales', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20007', 'name' => 'Other Payables', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20008', 'name' => 'Witholding Tax Payable-State Govt', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20009', 'name' => 'Witholding Tax Payable-Federal Govt', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20010', 'name' => 'Other Liabilities', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20039', 'name' => 'Current Liabilities, Total', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20040', 'name' => 'Account Payables', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20041', 'name' => 'Vendors-Domestic', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20042', 'name' => 'Vendors-Domestic-Individual', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20043', 'name' => 'Vendors-Domestic-Corporate', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20044', 'name' => 'Vendors-Domestic-Others', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20059', 'name' => 'Vendors-Domestic, Total', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20060', 'name' => 'Vendors-Foreign', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20061', 'name' => 'Vendors- Foreign- Individual', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20062', 'name' => 'Vendors-Foreign-Corporate', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20063', 'name' => 'Vendors-Foreign-Others', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20079', 'name' => 'Vendors- Foreign- Total', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20080', 'name' => 'Accounts Payables, Total', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],

            // Balance Sheet - Equity
            ['code' => '20090', 'name' => 'Capital & Reserves', 'account_type_id' => $equityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20091', 'name' => 'Capital Employed', 'account_type_id' => $equityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20092', 'name' => 'Reserves', 'account_type_id' => $equityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20093', 'name' => 'Profit Realised', 'account_type_id' => $equityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20198', 'name' => 'Caital & Reserves, Total', 'account_type_id' => $equityType->id, 'account_group_id' => $balanceSheetGroup->id],
            ['code' => '20199', 'name' => 'Liabilities Total', 'account_type_id' => $liabilityType->id, 'account_group_id' => $balanceSheetGroup->id],

            // Profit and Loss - Revenue
            ['code' => '30000', 'name' => 'Income Statement', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30001', 'name' => 'Revenue', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30002', 'name' => 'Sales-Domestic', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30003', 'name' => 'Sales- Domestic-Individual', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30004', 'name' => 'Sales-Domestic-Corporate', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30005', 'name' => 'Sales-  Domesticc- Others', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30019', 'name' => 'Sales- Domestic, Total', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30020', 'name' => 'Sales-Foreign', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30021', 'name' => 'Sales - Foreign-Individual', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30022', 'name' => 'Sales -Foreign-Corporate', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30023', 'name' => 'Sales- Foreign- Others', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30039', 'name' => 'Sales-Foreign, Total', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30041', 'name' => 'Fees & Commissions', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30042', 'name' => 'Discount Received', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30043', 'name' => 'Interest Received', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30044', 'name' => 'Gain On Sales Of Fixed Assets', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30045', 'name' => 'Incentives', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30046', 'name' => 'Rebate', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30047', 'name' => 'Other Income', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30059', 'name' => 'Fees & Commissions, Total', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '30090', 'name' => 'Revenue Total', 'account_type_id' => $revenueType->id, 'account_group_id' => $profitLossGroup->id],

            // Profit and Loss - Expenses
            ['code' => '40000', 'name' => 'Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40001', 'name' => 'Salaries & Wages', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40002', 'name' => 'Basic Salary', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40003', 'name' => 'Transport Allowance', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40004', 'name' => 'Other Allowance', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40005', 'name' => 'Staff Bonus', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40006', 'name' => 'Meal Allowance', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40019', 'name' => 'Salaries & Wages, Total', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40020', 'name' => 'Office Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40021', 'name' => 'Electricity Bills', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40022', 'name' => 'Printing And Stationery', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40023', 'name' => 'Recharge Cards', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40024', 'name' => 'Postage', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40025', 'name' => 'Petrol And Oil', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40026', 'name' => 'Bank Charges', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40027', 'name' => 'Rent Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40028', 'name' => 'School Fees', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40029', 'name' => 'Other Expenses- (Wages)', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40030', 'name' => 'Cash Shortages', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40031', 'name' => 'Security Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40049', 'name' => 'Office Expenses, Total', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40050', 'name' => 'Other Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40051', 'name' => 'Consultancy Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40052', 'name' => 'Software Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40053', 'name' => 'Legal Fees', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40054', 'name' => 'Other Professional Fees', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40055', 'name' => 'Miscelleneous Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40056', 'name' => 'Tax Paid', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40057', 'name' => 'Utility Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40058', 'name' => 'Donations/Gift', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40069', 'name' => 'Other Expenses, Total', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40070', 'name' => 'Selling & Distribution Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40071', 'name' => 'Advertising', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40072', 'name' => 'Entertainment', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40073', 'name' => 'Public Relations', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40074', 'name' => 'Transport And Travel', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40075', 'name' => 'Delivery Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40076', 'name' => 'Loading/Offloading', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40077', 'name' => 'Clearing Charges', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40078', 'name' => 'Direct Cost Applied', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40079', 'name' => 'Purchase Variant Account', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40080', 'name' => 'COGS Account', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40081', 'name' => 'Discount Allowed', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40082', 'name' => 'Carriage Outward', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40089', 'name' => 'Selling  & Distribution Expenses Total', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40090', 'name' => 'Repairs & Maintenance', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40091', 'name' => 'Buildings- Repairs', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40092', 'name' => 'Motor Vehicle- Repairs', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40093', 'name' => 'Office Equipment-Repairs', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40094', 'name' => 'Plant & Machinery-Repairs', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40095', 'name' => 'Computer Repairs', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40119', 'name' => 'Repairs & Maintenance Total', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40120', 'name' => 'Depreciation Expenses', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40121', 'name' => 'Depr- Buildings', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40122', 'name' => 'Depr- Motor Vehicle', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40123', 'name' => 'Depr- Office Equipment', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40124', 'name' => 'Depr-Plant & Machinery', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40139', 'name' => 'Depreciation Expenses, Total', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40198', 'name' => 'Expenses Total', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
            ['code' => '40199', 'name' => 'Ytd Profit Or Loss', 'account_type_id' => $expenseType->id, 'account_group_id' => $profitLossGroup->id],
        ];

        // Helper function to get appropriate subtype for an account
        $getSubtypeForAccount = function($accountName) use ($subtypes, $defaultSubtype) {
            // Direct matches
            if (isset($subtypes[$accountName])) {
                return $subtypes[$accountName] ?: $defaultSubtype;
            }
            
            // Pattern matches for better categorization
            if (str_contains($accountName, 'Land And Buildings') || str_contains($accountName, 'Buildings')) {
                return $subtypes['Land and Buildings'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Plant & Machinery')) {
                return $subtypes['Plant & Machinery'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Motor Vehicle')) {
                return $subtypes['Motor Vehicles'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Office Equipment')) {
                return $subtypes['Office Equipment'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Inventory') && str_contains($accountName, 'Domestic')) {
                return $subtypes['Inventory - Domestic'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Inventory') && str_contains($accountName, 'Foreign')) {
                return $subtypes['Inventory - Foreign'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Inventory')) {
                return $subtypes['Inventory'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Customers') && str_contains($accountName, 'Domestic')) {
                return $subtypes['Customers - Domestic'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Customers') && str_contains($accountName, 'Foreign')) {
                return $subtypes['Customers - Foreign'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Accounts Receivable')) {
                return $subtypes['Accounts Receivable'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Cash') || str_contains($accountName, 'Vault') || str_contains($accountName, 'Petty Cash') || str_contains($accountName, 'Cashier')) {
                return $subtypes['Cash Accounts'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Bank')) {
                return $subtypes['Bank Accounts'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Liquid Assets')) {
                return $subtypes['Liquid Assets'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Current Assets')) {
                return $subtypes['Current Assets'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Fixed Assets')) {
                return $subtypes['Fixed Assets'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Tangible Fixed Assets')) {
                return $subtypes['Tangible Fixed Assets'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Other Receivables')) {
                return $subtypes['Other Receivables'] ?: $defaultSubtype;
            }
            
            // Liability patterns
            if (str_contains($accountName, 'VAT')) {
                return $subtypes['VAT Payables'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Withholding Tax')) {
                return $subtypes['Withholding Tax Payables'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Vendors') && str_contains($accountName, 'Domestic')) {
                return $subtypes['Vendors - Domestic'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Vendors') && str_contains($accountName, 'Foreign')) {
                return $subtypes['Vendors - Foreign'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Accounts Payable')) {
                return $subtypes['Accounts Payable'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Current Liabilities')) {
                return $subtypes['Current Liabilities'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Revolving Credits')) {
                return $subtypes['Revolving Credits'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Other Payables')) {
                return $subtypes['Other Payables'] ?: $defaultSubtype;
            }
            
            // Equity patterns
            if (str_contains($accountName, 'Capital & Reserves')) {
                return $subtypes['Capital & Reserves'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Capital Employed')) {
                return $subtypes['Capital Employed'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Reserves')) {
                return $subtypes['Reserves'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Profit Realised')) {
                return $subtypes['Profit Realised'] ?: $defaultSubtype;
            }
            
            // Revenue patterns
            if (str_contains($accountName, 'Sales') && str_contains($accountName, 'Domestic')) {
                return $subtypes['Sales - Domestic'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Sales') && str_contains($accountName, 'Foreign')) {
                return $subtypes['Sales - Foreign'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Sales')) {
                return $subtypes['Sales Revenue'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Fees & Commissions')) {
                return $subtypes['Fees & Commissions'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Discount Received')) {
                return $subtypes['Discount Received'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Interest Received')) {
                return $subtypes['Interest Received'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Gain On Sales Of Fixed Assets')) {
                return $subtypes['Gain on Sales of Fixed Assets'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Incentives')) {
                return $subtypes['Incentives'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Rebate')) {
                return $subtypes['Rebate'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Other Income')) {
                return $subtypes['Other Income'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Revenue')) {
                return $subtypes['Sales Revenue'] ?: $defaultSubtype;
            }
            
            // Expense patterns
            if (str_contains($accountName, 'Salaries & Wages')) {
                return $subtypes['Salaries & Wages'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Office Expenses')) {
                return $subtypes['Office Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Selling & Distribution Expenses')) {
                return $subtypes['Selling & Distribution Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Repairs & Maintenance')) {
                return $subtypes['Repairs & Maintenance'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Depreciation') || str_contains($accountName, 'Depr-')) {
                return $subtypes['Depreciation Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'COGS Account')) {
                return $subtypes['Cost of Goods Sold'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Direct Cost Applied')) {
                return $subtypes['Direct Cost Applied'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Purchase Variant Account')) {
                return $subtypes['Purchase Variant Account'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Discount Allowed')) {
                return $subtypes['Discount Allowed'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Carriage Outward')) {
                return $subtypes['Carriage Outward'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Utility Expenses')) {
                return $subtypes['Utility Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Tax Paid')) {
                return $subtypes['Tax Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Legal Fees') || str_contains($accountName, 'Other Professional Fees')) {
                return $subtypes['Professional Fees'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Miscelleneous Expenses')) {
                return $subtypes['Miscellaneous Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Cash Shortages')) {
                return $subtypes['Cash Shortages'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Security Expenses')) {
                return $subtypes['Security Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Donations/Gift')) {
                return $subtypes['Donations/Gifts'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Other Expenses')) {
                return $subtypes['Other Expenses'] ?: $defaultSubtype;
            }
            if (str_contains($accountName, 'Expenses')) {
                return $subtypes['Other Expenses'] ?: $defaultSubtype;
            }
            
            return $defaultSubtype;
        };

        foreach ($accounts as $accountData) {
            $subtype = $getSubtypeForAccount($accountData['name']);
            Account::create([
                'name' => $accountData['name'],
                'code' => $accountData['code'],
                'account_group_id' => $accountData['account_group_id'],
                'account_type_id' => $accountData['account_type_id'],
                'account_subtype_id' => $subtype->id,
                'account_owner_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }
} 