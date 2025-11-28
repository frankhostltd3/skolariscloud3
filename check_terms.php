
use App\Models\School;
use Illuminate\Support\Facades\DB;

$school = School::find(16);
if ($school) {
    echo "School found: " . $school->name . "\n";
    echo "Database: " . $school->database . "\n";

    // Switch to tenant database
    // We can't easily switch connection config in tinker script execution this way without full bootstrap
    // But we can use raw SQL

    if ($school->database) {
        DB::statement("USE " . $school->database);

        $terms = DB::table('terms')->get();
        echo "Terms count: " . $terms->count() . "\n";
        echo $terms;
    } else {
        echo "No database assigned to school.\n";
    }} else {
    echo "School not found.\n";
}
