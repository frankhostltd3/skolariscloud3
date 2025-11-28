# Assessment Configuration & Weighted Grading Implementation

## Overview
This feature allows school administrators to configure custom assessment types (e.g., Beginning of Term, Mid Term, End of Term) and assign weights to them. The system then uses these weights to calculate student grades and class rankings, ensuring that students are graded according to the school's specific policy.

## Features

### 1. Assessment Configuration Settings
- **Location**: Settings > Academic > Assessment Configuration
- **Functionality**:
  - Add unlimited assessment types.
  - Define Name (e.g., "Mid Term Exam"), Code (e.g., "MOT"), and Weight (e.g., 30%).
  - Real-time validation to ensure total weight equals 100%.
  - Stored in `settings` table as a JSON array under key `assessment_configuration`.

### 2. Weighted Grade Calculation
- **Logic**:
  - Grades are grouped by subject.
  - For each subject, grades are filtered by the configured assessment types.
  - The average percentage for each assessment type is calculated.
  - The final subject mark is the sum of `(Type Average % * Type Weight)`.
  - If no configuration exists, the system falls back to a simple average of all grades.
- **Implementation**: `App\Http\Controllers\Admin\ReportsController`

### 3. Class Ranking
- **Logic**:
  - Class rank is calculated based on the weighted overall percentage.
  - The system calculates the weighted GPA/Percentage for all students in the class.
  - Students are sorted by this weighted score to determine their position.
- **Implementation**: `App\Http\Controllers\Admin\ReportsController`

### 4. Grade Entry Integration
- **Functionality**:
  - When teachers enter grades, the "Assessment Type" dropdown is dynamically populated with the configured types.
  - Validation ensures that only valid assessment types are saved.
- **Implementation**: `App\Http\Controllers\Tenant\Teacher\GradesController`

## Technical Details

### Database
- **Settings Table**: Stores the configuration.
- **Grades Table**: Uses `assessment_type` column to link grades to the configuration.

### Code Structure
- **Controller**: `AcademicSettingsController` handles saving the configuration.
- **View**: `resources/views/settings/academic.blade.php` provides the UI.
- **Helper**: `setting('assessment_configuration')` retrieves the config.
- **Reports**: `ReportsController` contains the core calculation logic (`calculateSubjectGrade`, `calculateOverallPercentage`).

## Usage Guide

1.  **Configure Assessments**:
    - Go to Settings > Academic.
    - Scroll to "Assessment Configuration".
    - Add rows for each exam/assessment type (e.g., BOT 10%, MOT 30%, EOT 60%).
    - Save Settings.

2.  **Enter Grades**:
    - Teachers go to "Enter Grades".
    - Select the appropriate "Assessment Type" (e.g., "MOT") from the dropdown.
    - Enter marks.

3.  **Generate Reports**:
    - Go to Reports > Report Cards.
    - Generate a report card.
    - The system will automatically calculate the final mark based on the weights defined in step 1.

## Example Scenarios

**Scenario A: Standard Term**
- BOT (Beginning of Term): 10%
- MOT (Mid Term): 30%
- EOT (End of Term): 60%
- **Result**: A student getting 80% in BOT, 70% in MOT, and 90% in EOT will have a final mark of:
  `(80 * 0.10) + (70 * 0.30) + (90 * 0.60) = 8 + 21 + 54 = 83%`

**Scenario B: Single Exam**
- Final Exam: 100%
- **Result**: The final mark is exactly what the student scored in the final exam.
