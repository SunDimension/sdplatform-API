<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 CHECKING ITEM_SOLDS TABLE\n";
echo "============================\n\n";

$columns = DB::select('SHOW COLUMNS FROM item_solds');
foreach ($columns as $col) {
    echo "{$col->Field}: {$col->Type}\n";
}
