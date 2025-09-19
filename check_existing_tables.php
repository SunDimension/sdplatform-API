<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 CHECKING EXISTING TABLES\n";
echo "===========================\n\n";

$tables = [
    'bank_remittances',
    'branches', 
    'cashier_expenses',
    'cashier_remittance',
    'create_items',
    'credit_limits',
    'credit_sales',
    'credit_transactions',
    'customers',
    'expense_lines',
    'item_solds',
    'payment_vouchers',
    'payment_voucher_details',
    'post_inflows',
    'price_changes',
    'receive_items',
    'receive_orders',
    'releases',
    'release_details',
    'sales_orders',
    'sales_receipts',
    'settle_credit',
    'stores',
    'store_items',
    'vendors',
    'vendor_credits',
    'vendor_target',
    'years'
];

$existingTables = [];

foreach ($tables as $table) {
    if (DB::getSchemaBuilder()->hasTable($table)) {
        $existingTables[] = $table;
        echo "✅ {$table}\n";
    } else {
        echo "❌ {$table}\n";
    }
}

echo "\n📋 EXISTING TABLES ARRAY:\n";
echo "=========================\n";
echo "array (\n";
foreach ($existingTables as $index => $table) {
    echo "  {$index} => '{$table}',\n";
}
echo ");\n";
