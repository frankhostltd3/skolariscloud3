<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::connection('mysql')->select("SHOW COLUMNS FROM permissions");
echo "Permissions Table Columns:\n";
foreach ($columns as $col) {
    echo "{$col->Field}: {$col->Type} (Null: {$col->Null})\n";
}

$columns = DB::connection('mysql')->select("SHOW COLUMNS FROM model_has_roles");
echo "\nModel Has Roles Table Columns:\n";
foreach ($columns as $col) {
    echo "{$col->Field}: {$col->Type} (Null: {$col->Null})\n";
}
