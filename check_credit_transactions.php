<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 CHECKING CREDIT_TRANSACTIONS TABLE\n";
echo "=====================================\n\n";

$columns = DB::select('SHOW COLUMNS FROM credit_transactions');
foreach ($columns as $col) {
    echo "{$col->Field}: {$col->Type}\n";
}
