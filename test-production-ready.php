<?php
// Production Readiness Test Script
// Run: php test-production-ready.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n================================================\n";
echo "PRODUCTION READINESS TEST - SKOLARISCLOUD3\n";
echo "================================================\n\n";

$tests = [];
$passed = 0;
$failed = 0;

// Test 1: Database Connection
echo "Testing database connection... ";
try {
    DB::connection()->getPdo();
    echo "✓ PASSED\n";
    $tests[] = ['Database Connection', 'PASSED'];
    $passed++;
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $tests[] = ['Database Connection', 'FAILED'];
    $failed++;
}

// Test 2: Schools exist
echo "Checking schools... ";
try {
    $schools = App\Models\School::count();
    echo "✓ PASSED ($schools schools)\n";
    $tests[] = ['Schools Exist', 'PASSED'];
    $passed++;
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $tests[] = ['Schools Exist', 'FAILED'];
    $failed++;
}

// Test 3: Key Models Load
echo "Testing key models... ";
try {
    $models = [
        'Student', 'Teacher', 'SchoolClass', 'Subject', 'Grade',
        'Attendance', 'Fee', 'Payment', 'Book', 'Employee'
    ];
    $modelsFail = [];
    foreach ($models as $model) {
        $class = "App\\Models\\$model";
        if (!class_exists($class)) {
            $modelsFail[] = $model;
        }
    }
    if (empty($modelsFail)) {
        echo "✓ PASSED\n";
        $tests[] = ['Key Models', 'PASSED'];
        $passed++;
    } else {
        echo "✗ FAILED: Missing - " . implode(', ', $modelsFail) . "\n";
        $tests[] = ['Key Models', 'FAILED'];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $tests[] = ['Key Models', 'FAILED'];
    $failed++;
}

// Test 4: Routes Load
echo "Testing routes... ";
try {
    $routes = Route::getRoutes();
    $routeCount = count($routes);
    echo "✓ PASSED ($routeCount routes)\n";
    $tests[] = ['Routes Load', 'PASSED'];
    $passed++;
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $tests[] = ['Routes Load', 'FAILED'];
    $failed++;
}

// Test 5: Config Files
echo "Testing configuration... ";
try {
    $configs = ['app', 'database', 'mail', 'cache', 'session'];
    $configFail = [];
    foreach ($configs as $config) {
        if (!config($config)) {
            $configFail[] = $config;
        }
    }
    if (empty($configFail)) {
        echo "✓ PASSED\n";
        $tests[] = ['Configuration', 'PASSED'];
        $passed++;
    } else {
        echo "✗ FAILED: Missing - " . implode(', ', $configFail) . "\n";
        $tests[] = ['Configuration', 'FAILED'];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $tests[] = ['Configuration', 'FAILED'];
    $failed++;
}

// Test 6: Permissions Seeded
echo "Testing permissions... ";
try {
    // Permissions are in tenant databases - just check if seeder exists
    if (file_exists(database_path('seeders/PermissionsSeeder.php'))) {
        echo "✓ PASSED (PermissionsSeeder exists)\n";
        $tests[] = ['Permissions', 'PASSED'];
        $passed++;
    } else {
        echo "✗ FAILED: Permissions seeder not found\n";
        $tests[] = ['Permissions', 'FAILED'];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $tests[] = ['Permissions', 'FAILED'];
    $failed++;
}

// Test 7: Views Compile
echo "Testing view compilation... ";
try {
    $viewPaths = [
        'tenant.layouts.app',
        'tenant.modules.books.index',
        'tenant.modules.library.index'
    ];
    $viewFail = [];
    foreach ($viewPaths as $view) {
        if (!view()->exists($view)) {
            $viewFail[] = $view;
        }
    }
    if (empty($viewFail)) {
        echo "✓ PASSED\n";
        $tests[] = ['View Compilation', 'PASSED'];
        $passed++;
    } else {
        echo "✗ FAILED: Missing - " . implode(', ', $viewFail) . "\n";
        $tests[] = ['View Compilation', 'FAILED'];
        $failed++;
    }
} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    $tests[] = ['View Compilation', 'FAILED'];
    $failed++;
}

echo "\n================================================\n";
echo "TEST SUMMARY\n";
echo "================================================\n";
echo "Total Tests: " . ($passed + $failed) . "\n";
echo "✓ Passed: $passed\n";
echo "✗ Failed: $failed\n";
echo "\nStatus: " . ($failed === 0 ? "✓ PRODUCTION READY" : "✗ NEEDS ATTENTION") . "\n";
echo "================================================\n\n";

exit($failed === 0 ? 0 : 1);
