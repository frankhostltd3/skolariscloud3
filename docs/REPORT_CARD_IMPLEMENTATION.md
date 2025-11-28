# Report Card Implementation

## Overview
The report card system generates PDF reports for students, including their grades, attendance, and teacher comments. It supports both single student export and bulk class export.

## Features
- **Dynamic Data**: Fetches real grades and attendance records from the database.
- **One-Page Layout**: Optimized CSS to ensure the report fits on a single A4 page.
- **Student Photo**: Displays the student's profile photo if available.
- **School Branding**: Includes school logo, name, and address.
- **Customization**: Supports font family, font size, and color theme settings.

## Technical Details

### Controller
`App\Http\Controllers\Admin\ReportsController` handles the data generation and PDF export.
- `generateReportCardData($student, $school, $academicYear, $term)`: Fetches data for a specific student.
- `exportStudentReportCard(Request $request)`: Generates a PDF for a single student.
- `exportClassReportCards(Request $request)`: Generates a merged PDF for an entire class.

### Views
- `resources/views/admin/reports/pdf/report-card.blade.php`: Template for single student report.
- `resources/views/admin/reports/pdf/bulk-report-cards.blade.php`: Template for bulk export.

### Data Sources
- **Grades**: `App\Models\Grade` (filtered by `is_published` = true).
- **Attendance**: `App\Models\AttendanceRecord`.
- **Student Info**: `App\Models\User` (including `profile_photo`).

## Student Profile
Students can upload their profile photo via the profile settings page.
- **Route**: `/profile` (GET/POST)
- **Controller**: `App\Http\Controllers\Tenant\ProfileController`
- **Storage**: Photos are stored in `storage/app/public/profile-photos`.

## Configuration
The following settings control the report card appearance:
- `report_card_font_family`
- `report_card_font_size`
- `report_card_color_theme`
- `report_card_show_logo`
- `report_card_school_name`
- `report_card_address`
- `report_card_signature_1`, `_2`, `_3`
