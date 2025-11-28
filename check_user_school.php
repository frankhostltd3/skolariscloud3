
use App\Models\School;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$school = School::find(16);
if ($school) {
    echo "School found: " . $school->name . "\n";

    // Switch to tenant database
    DB::purge('tenant');
    config(['database.connections.tenant.database' => $school->database]);
    DB::connection('tenant')->reconnect();
    DB::setDefaultConnection('tenant');

    $user = User::find(1);
    if ($user) {
        echo "User found: " . $user->name . "\n";
        echo "School ID: " . $user->school_id . "\n";
    } else {
        echo "User not found.\n";
    }

} else {
    echo "School not found.\n";
}
