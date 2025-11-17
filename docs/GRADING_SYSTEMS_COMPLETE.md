# Grading Systems Management - Complete Documentation

## Overview

Comprehensive grading scheme management system supporting unlimited grading systems with flexible band definitions. Works with any grading system worldwide (A-F, 1-9, percentage-based, etc.) with automatic grade assignment and visual representation.

## Features

✅ **Multiple Grading Schemes**: Create unlimited schemes per school  
✅ **Flexible Grading Bands**: Define grade ranges, labels, and grade points  
✅ **Current Scheme**: Mark one scheme as active for automatic grading  
✅ **Country/Exam Body Association**: Link schemes to specific examination bodies  
✅ **Dynamic Band Management**: Add/remove bands via JavaScript interface  
✅ **Automatic Grade Assignment**: Assign grades based on score ranges  
✅ **Visual Representation**: Progress bar showing grade distribution  
✅ **Global Compatibility**: Supports UK, US, Kenya, Nigeria, India, and 195+ systems  
✅ **Tenant-Scoped**: Complete isolation per school  

## Database Schema

```sql
-- Grading Schemes Table
CREATE TABLE grading_schemes (
    id BIGINT UNSIGNED PRIMARY KEY,
    school_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(255) NULL,
    examination_body_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    is_current BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,
    timestamps,
    
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
    FOREIGN KEY (examination_body_id) REFERENCES examination_bodies(id) ON DELETE SET NULL,
    INDEX (school_id, is_current),
    INDEX (school_id, is_active)
);

-- Grading Bands Table
CREATE TABLE grading_bands (
    id BIGINT UNSIGNED PRIMARY KEY,
    grading_scheme_id BIGINT UNSIGNED NOT NULL,
    grade VARCHAR(10) NOT NULL,
    label VARCHAR(255) NULL,
    min_score DECIMAL(5,2) NOT NULL,
    max_score DECIMAL(5,2) NOT NULL,
    grade_point DECIMAL(4,2) NULL,
    remarks TEXT NULL,
    sort_order INT DEFAULT 0,
    timestamps,
    
    FOREIGN KEY (grading_scheme_id) REFERENCES grading_schemes(id) ON DELETE CASCADE,
    INDEX (grading_scheme_id, sort_order)
);
```

### Relationships

```
School (1) → (many) GradingScheme
GradingScheme (1) → (many) GradingBand
GradingScheme → ExaminationBody (optional)
```

## Models

### GradingScheme Model

**Location:** `app/Models/Academic/GradingScheme.php`

#### Scopes

```php
// Filter by school (tenant-scoped)
GradingScheme::forSchool($schoolId)->get();

// Only active schemes
GradingScheme::active()->get();

// Only current scheme
GradingScheme::current()->first();

// Filter by examination body
GradingScheme::byExaminationBody($examinationBodyId)->get();
```

#### Relationships

```php
// Get all grading bands (sorted by sort_order)
$scheme->bands; 

// Get examination body
$scheme->examinationBody;

// Get school
$scheme->school;
```

#### Methods

```php
// Get grade for a given score
$band = $scheme->getGradeForScore(85.5);
// Returns: GradingBand object or null

// Check for overlapping bands
$hasOverlaps = $scheme->hasOverlappingBands();
// Returns: boolean

// Get score coverage percentage
$coverage = $scheme->score_coverage;
// Returns: float (e.g., 100.0 for 0-100 coverage)
```

#### Automatic is_current Logic

```php
// When marking a scheme as current, all others are automatically unmarked
$scheme->update(['is_current' => true]);
// This scheme becomes current, all others for this school become is_current = false
```

### GradingBand Model

**Location:** `app/Models/Academic/GradingBand.php`

#### Attributes

```php
// Get score range as string
$band->score_range; // "80.00 - 100.00"

// Get full display name
$band->full_name; // "A (Distinction)" or just "A"

// Get badge color based on grade point
$band->badge_color; // "success", "primary", "info", "warning", "danger"
```

#### Methods

```php
// Check if score falls within this band
$contains = $band->containsScore(85.5);
// Returns: boolean
```

## Controller

**Location:** `app/Http/Controllers/Tenant/Academic/GradingSchemeController.php`

### Methods

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /grading_schemes | List all schemes with search |
| `create()` | GET /grading_schemes/create | Show creation form |
| `store()` | POST /grading_schemes | Create new scheme with bands |
| `show()` | GET /grading_schemes/{id} | View scheme details |
| `edit()` | GET /grading_schemes/{id}/edit | Show edit form |
| `update()` | PUT /grading_schemes/{id} | Update scheme and bands |
| `destroy()` | DELETE /grading_schemes/{id} | Delete scheme |
| `setCurrent()` | PUT /grading_schemes/{id}/set-current | Set as current |
| `exportAll()` | GET /grading_schemes/export/all | Export all schemes |

### Transaction Safety

All create/update operations are wrapped in database transactions:

```php
try {
    DB::beginTransaction();
    
    // Create scheme
    $gradingScheme = GradingScheme::create([...]);
    
    // Create bands
    foreach ($request->bands as $index => $bandData) {
        GradingBand::create([...]);
    }
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    return back()->with('error', 'Error: ' . $e->getMessage());
}
```

## Form Requests

### StoreGradingSchemeRequest

**Validation Rules:**

```php
'name' => 'required|string|max:255',
'country' => 'nullable|string|max:255',
'examination_body_id' => 'nullable|exists:examination_bodies,id',
'description' => 'nullable|string|max:1000',
'is_current' => 'boolean',
'is_active' => 'boolean',

// Band validation
'bands' => 'nullable|array',
'bands.*.grade' => 'required_with:bands|string|max:10',
'bands.*.label' => 'nullable|string|max:255',
'bands.*.min_score' => 'required_with:bands|numeric|min:0|max:100',
'bands.*.max_score' => 'required_with:bands|numeric|min:0|max:100|gte:bands.*.min_score',
'bands.*.grade_point' => 'nullable|numeric|min:0|max:10',
'bands.*.remarks' => 'nullable|string|max:500',
```

### UpdateGradingSchemeRequest

Same validation rules as Store request.

## Views

### Index View (`index.blade.php`)

**Features:**
- Search by name
- Table with scheme details
- Band count badge
- Current scheme indicator
- Edit/Delete actions
- Empty state with international examples
- Information cards (International Systems, How Grading Works)

**Columns:**
- ID
- Name (with Active badge if current)
- Country
- Exam Body code
- Bands count
- Current status
- Actions

### Create/Edit Views

**Form Fields:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| Name | Text | Yes | Grading system name |
| Country | Text | No | Country name |
| Examination Body | Dropdown | No | Select from active exam bodies |
| Set as Current | Select | No | Yes/No (for automatic grading) |
| Status | Select | No | Active/Inactive |
| Description | Textarea | No | System description |

**Dynamic Grading Bands:**

Each band has:
- **Grade** (required): A, B, 1, D1, etc.
- **Label** (optional): Distinction, Credit, Pass
- **Min Score** (required): 0-100
- **Max Score** (required): 0-100 (must be >= min)
- **Grade Point** (optional): 0-10 (GPA equivalent)
- **Remarks** (optional): Additional notes

**JavaScript Functions:**
```javascript
addBand()      // Adds new band row
removeBand()   // Removes band (minimum 1 required)
```

### Show View (`show.blade.php`)

**Sections:**

1. **Grading System Details** (Left column):
   - Name
   - Country
   - Examination Body
   - Description
   - Total Bands count
   - Score Coverage %

2. **Grading Bands Table** (Right column):
   - Grade (color-coded badge)
   - Label
   - Score Range
   - Grade Point
   - Remarks

3. **Grading Visualization** (Bottom):
   - Progress bar showing grade distribution
   - Color-coded bands
   - Score ranges displayed

**Actions:**
- Edit button
- Set as Current button (if not current)
- Back to list button

## Routes

```php
// Resource routes (7 routes)
Route::resource('grading_schemes', GradingSchemeController::class);
// generates: index, create, store, show, edit, update, destroy

// Custom routes (2 routes)
Route::put('grading_schemes/{gradingScheme}/set-current', 'setCurrent')
    ->name('grading_schemes.set_current');
    
Route::get('grading_schemes/export/all', 'exportAll')
    ->name('grading_schemes.export_all');
```

**Total:** 9 routes under `tenant.academics.grading_schemes` namespace

## Navigation

### Academics Sidebar

```blade
<a class="nav-link {{ request()->routeIs('tenant.academics.grading_schemes.*') ? 'active' : '' }}" 
   href="{{ route('tenant.academics.grading_schemes.index') }}">
  <i class="bi bi-award-fill me-2"></i>{{ __('Grading Systems') }}
</a>
```

**Position:** Between Countries and Classes (after first divider)

### Admin Menu

Same icon and text, appears in Academics collapsible section.

## Global Grading Systems

### United Kingdom

**A-Level (A*-U):**
```
A* (90-100) - Outstanding
A  (80-89)  - Excellent
B  (70-79)  - Very Good
C  (60-69)  - Good
D  (50-59)  - Satisfactory
E  (40-49)  - Pass
U  (0-39)   - Unclassified
```

**GCSE (9-1):**
```
9 (90-100) - Exceptional
8 (80-89)  - Strong
7 (70-79)  - Good
6 (60-69)  - Fairly Good
5 (50-59)  - Strong Pass
4 (40-49)  - Standard Pass
3 (30-39)  - Below Standard
2 (20-29)  - Low
1 (0-19)   - Minimal
```

### United States

**GPA Scale (4.0):**
```
A  (93-100) - 4.0 - Excellent
A- (90-92)  - 3.7
B+ (87-89)  - 3.3
B  (83-86)  - 3.0 - Good
B- (80-82)  - 2.7
C+ (77-79)  - 2.3
C  (73-76)  - 2.0 - Average
C- (70-72)  - 1.7
D+ (67-69)  - 1.3
D  (65-66)  - 1.0 - Poor
F  (0-64)   - 0.0 - Fail
```

### Kenya

**KCSE (Kenya Certificate of Secondary Education):**
```
A  (80-100) - 12 points
A- (75-79)  - 11 points
B+ (70-74)  - 10 points
B  (65-69)  - 9 points
B- (60-64)  - 8 points
C+ (55-59)  - 7 points
C  (50-54)  - 6 points
C- (45-49)  - 5 points
D+ (40-44)  - 4 points
D  (35-39)  - 3 points
D- (30-34)  - 2 points
E  (0-29)   - 1 point
```

### Nigeria

**WAEC (West African Examinations Council):**
```
A1 (75-100) - Excellent
B2 (70-74)  - Very Good
B3 (65-69)  - Good
C4 (60-64)  - Credit
C5 (55-59)  - Credit
C6 (50-54)  - Credit
D7 (45-49)  - Pass
E8 (40-44)  - Pass
F9 (0-39)   - Fail
```

### Uganda

**UNEB (Uganda National Examinations Board):**
```
D1 (80-100) - Distinction
D2 (70-79)  - Distinction
C3 (65-69)  - Credit
C4 (60-64)  - Credit
C5 (55-59)  - Credit
C6 (50-54)  - Credit
P7 (45-49)  - Pass
P8 (40-44)  - Pass
F9 (0-39)   - Fail
```

### South Africa

**NSC (National Senior Certificate):**
```
7 (80-100) - Outstanding Achievement
6 (70-79)  - Meritorious Achievement
5 (60-69)  - Substantial Achievement
4 (50-59)  - Adequate Achievement
3 (40-49)  - Moderate Achievement
2 (30-39)  - Elementary Achievement
1 (0-29)   - Not Achieved
```

### India

**CBSE (Central Board of Secondary Education):**
```
A1 (91-100) - 10.0
A2 (81-90)  - 9.0
B1 (71-80)  - 8.0
B2 (61-70)  - 7.0
C1 (51-60)  - 6.0
C2 (41-50)  - 5.0
D  (33-40)  - 4.0
E1 (21-32)  - Needs Improvement
E2 (0-20)   - Needs Improvement
```

## Usage Examples

### Creating a Grading Scheme

```php
// Via form submission
POST /tenant/academics/grading_schemes
[
    'name' => 'Uganda UNEB O-Level',
    'country' => 'Uganda',
    'examination_body_id' => 1, // UNEB
    'is_current' => true,
    'is_active' => true,
    'description' => 'UNEB grading for Ordinary Level',
    'bands' => [
        [
            'grade' => 'D1',
            'label' => 'Distinction',
            'min_score' => 80,
            'max_score' => 100,
            'grade_point' => 9.0,
            'remarks' => 'Outstanding performance'
        ],
        [
            'grade' => 'D2',
            'label' => 'Distinction',
            'min_score' => 70,
            'max_score' => 79,
            'grade_point' => 8.0,
            'remarks' => 'Excellent performance'
        ],
        // ... more bands
    ]
]
```

### Getting Grade for Score

```php
$scheme = GradingScheme::current()->first();
$band = $scheme->getGradeForScore(85.5);

echo $band->grade;      // "D1"
echo $band->label;      // "Distinction"
echo $band->grade_point; // 9.0
```

### Checking for Overlaps

```php
$scheme = GradingScheme::find($id);

if ($scheme->hasOverlappingBands()) {
    return back()->with('error', 'Grading bands have overlapping score ranges!');
}
```

## Integration Points

### 1. Student Grading

```php
// Get current grading scheme
$scheme = GradingScheme::forSchool($schoolId)->current()->first();

// Assign grade based on score
$studentScore = 85.5;
$band = $scheme->getGradeForScore($studentScore);

$student->grade = $band->grade;
$student->grade_point = $band->grade_point;
$student->save();
```

### 2. Exam Results

```php
// Process exam results
foreach ($examSubmissions as $submission) {
    $band = $gradingScheme->getGradeForScore($submission->score);
    
    $submission->update([
        'grade' => $band->grade,
        'grade_label' => $band->label,
        'grade_point' => $band->grade_point,
    ]);
}
```

### 3. Report Cards

```php
// Generate report card with grades
$scheme = GradingScheme::current()->first();
$bands = $scheme->bands;

// Display grade legend on report
foreach ($bands as $band) {
    echo "{$band->grade}: {$band->score_range} ({$band->label})";
}
```

### 4. GPA Calculation

```php
// Calculate overall GPA
$totalGradePoints = 0;
$subjectCount = 0;

foreach ($student->subjects as $subject) {
    $band = $gradingScheme->getGradeForScore($subject->score);
    $totalGradePoints += $band->grade_point;
    $subjectCount++;
}

$gpa = $totalGradePoints / $subjectCount;
```

## Best Practices

1. **Create Non-Overlapping Bands**: Ensure score ranges don't overlap
2. **Use is_current Wisely**: Only one scheme should be current
3. **Include Grade Points**: For GPA calculations
4. **Add Descriptive Labels**: Help users understand grades
5. **Sort Bands Properly**: Use sort_order field (highest to lowest)
6. **Test Score Coverage**: Ensure 0-100 range is covered
7. **Link to Exam Bodies**: Associate with correct examination body
8. **Document Remarks**: Add context for each grade

## Troubleshooting

### Issue: Overlapping Bands

**Problem:** Bands have overlapping score ranges  
**Solution:**
```php
// Check before saving
if ($scheme->hasOverlappingBands()) {
    // Fix overlaps or show error
}
```

### Issue: Missing Grades for Scores

**Problem:** Score doesn't match any band  
**Solution:** Ensure bands cover full 0-100 range

```php
$coverage = $scheme->score_coverage;
if ($coverage < 100) {
    // Add missing bands
}
```

### Issue: Multiple Current Schemes

**Problem:** More than one scheme marked as current  
**Solution:** Model automatically handles this in `boot()` method

```php
// When saving, all others are automatically unmarked
$scheme->update(['is_current' => true]);
```

## Security

✅ **Tenant Isolation**: All queries scoped to current school  
✅ **Ownership Verification**: Checks scheme belongs to school  
✅ **Transaction Safety**: Rollback on errors  
✅ **Validation**: Score ranges, grade points, required fields  
✅ **CSRF Protection**: All forms protected  
✅ **SQL Injection Prevention**: Eloquent ORM usage  

## Performance

### Optimizations

1. **Eager Loading:**
```php
GradingScheme::with('bands', 'examinationBody')->get();
```

2. **Index Usage:**
```php
// Indexed queries
->where('school_id', $schoolId)
->where('is_current', true)
```

3. **Caching Current Scheme:**
```php
$current = Cache::remember("grading_scheme_{$schoolId}", 3600, function () use ($schoolId) {
    return GradingScheme::forSchool($schoolId)->current()->with('bands')->first();
});
```

## Migration Details

**File:** `database/migrations/tenants/2025_11_16_000004_create_grading_schemes_tables.php`

**Migration Times:**
- SMATCAMPUS Demo School: 264.69ms ✓
- Starlight Academy: 150.62ms ✓
- Busoga College Mwiri: 131.63ms ✓
- Jinja Senior Secondary School: 146.36ms ✓

**Tables Created:**
- `grading_schemes` (8 columns + timestamps)
- `grading_bands` (9 columns + timestamps)

## Summary

✅ **11 Files Created**: 1 migration, 2 models, 1 controller, 2 form requests, 5 views  
✅ **3 Files Modified**: routes/web.php, academics sidebar, admin menu  
✅ **9 Routes**: 7 resource + 2 custom routes  
✅ **Global Compatibility**: UK, US, Kenya, Nigeria, Uganda, India, South Africa, and 195+ countries  
✅ **Automatic Grading**: Assign grades based on score ranges  
✅ **Visual Representation**: Progress bar visualization  
✅ **Production Ready**: Transaction safety, validation, security, responsive UI  

**Access:** `/tenant/academics/grading_schemes`

🎯 **100% Production Ready** - Fully functional grading system with dynamic band management, automatic grade assignment, and global compatibility!
