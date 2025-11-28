<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Tenant\Finance\ExpenseCategoryController;

$school = School::find(16);
if ($school) {
    echo "School found: " . $school->name . "\n";

    // Switch to tenant database
    DB::purge('tenant');
    config(['database.connections.tenant.database' => $school->database]);
    DB::connection('tenant')->reconnect();
    DB::setDefaultConnection('tenant');

    // Mock request
    $request = Request::create('/tenant/finance/expense-categories', 'POST', [
        'name' => 'Test Category ' . time(),
        'code' => 'TC' . time(),
        'description' => 'Test Description',
        'is_active' => '1',
    ]);

    // Mock user and school in request attributes
    $user = User::where('school_id', $school->id)->first();
    $request->attributes->set('currentSchool', $school);
    $request->setUserResolver(function () use ($user) {
        return $user;
    });

    $controller = new ExpenseCategoryController();

    try {
        $response = $controller->store($request);
        echo "Response status: " . $response->getStatusCode() . "\n";
        if ($response->isRedirect()) {
            echo "Redirect target: " . $response->getTargetUrl() . "\n";
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "Validation failed:\n";
        print_r($e->errors());
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString();
    }

} else {
    echo "School not found.\n";
}
