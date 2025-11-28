# Report Card Logo Implementation

## Overview
The report card PDF template has been updated to display the school logo dynamically. This ensures that each tenant school (e.g., Victoria School) displays its own logo on the generated report cards.

## Changes Made

### 1. View Modification
- **File:** `resources/views/admin/reports/pdf/report-card.blade.php`
- **Change:** Added logic to check for `$school->logo_url` and display the image in the header.
- **Implementation:**
  ```html
  <div class="header">
      @if($school->logo_url)
          <div style="margin-bottom: 10px;">
              <img src="{{ $school->logo_url }}" alt="School Logo" style="max-height: 80px; max-width: 150px;">
          </div>
      @endif
      <div class="school-name">{{ $school->name }}</div>
      <!-- ... -->
  </div>
  ```

### 2. Data Source
- The `$school` object is passed from `ReportsController::generateReportCardData`.
- The `logo_url` attribute is automatically handled by the `School` model accessor, ensuring the correct URL is generated for the tenant context.

## Verification
- Navigate to **Reports > Report Cards**.
- Select a student and click **Download PDF**.
- The generated PDF will now include the school logo at the top center of the page.
