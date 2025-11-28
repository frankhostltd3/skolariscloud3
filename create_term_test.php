
use App\Models\School;
use App\Models\Academic\Term;
use Illuminate\Support\Facades\DB;

$school = School::find(16);
if ($school) {
    echo "School found: " . $school->name . "\n";

    // Switch to tenant database
    DB::purge('tenant');
    config(['database.connections.tenant.database' => $school->database]);
    DB::connection('tenant')->reconnect();
    DB::setDefaultConnection('tenant');

    // Mock request data
    $data = [
        'name' => 'Term 1 2025',
        'academic_year' => '2025',
        'start_date' => '2025-01-01',
        'end_date' => '2025-04-01',
        'is_current' => true,
        'is_active' => true,
        'school_id' => $school->id,
    ];

    try {
        echo "Creating term...\n";
        $term = Term::create($data);
        echo "Term created: " . $term->id . "\n";
    } catch (\Exception $e) {
        echo "Error creating term: " . $e->getMessage() . "\n";
    }

} else {
    echo "School not found.\n";
}
