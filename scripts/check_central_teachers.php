<?php

use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

echo "Checking Central Database...\n";
echo "Current Connection: " . DB::getDefaultConnection() . "\n";
echo "Database Name: " . DB::connection()->getDatabaseName() . "\n";

try {
    $count = DB::table('teachers')->count();
    echo "Total Teachers in Central DB: " . $count . "\n";

    $teachers = DB::table('teachers')->get();
    foreach ($teachers as $t) {
        echo " - " . $t->name . " (" . $t->email . ")\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
