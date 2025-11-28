# Report Card Font Customization

## Overview
The Report Card system now supports font customization, allowing administrators to tailor the appearance of generated PDFs to match their school's branding.

## Features
- **Font Family Selection**: Choose from standard web-safe fonts and UTF-8 compatible fonts.
  - Arial (Default)
  - Helvetica
  - Times New Roman
  - Courier
  - DejaVu Sans (Recommended for special characters/UTF-8 support)
- **Base Font Size**: Adjust the base font size (10px - 18px). All other text elements scale relatively.
- **Heading Weight**: Toggle between "Bold" and "Normal" for headers and labels.
- **Compact Layout**: Optimized to fit all report card details on a single A4 page.
- **Student Photo**: Automatically displays the student's profile photo if available.
- **Logo Handling**: Improved logo rendering using absolute filesystem paths for reliability.

## Implementation Details

### Database Storage
Settings are stored in the tenant database using the `settings` table via the `setting()` helper:
- `report_card_font_family` (string)
- `report_card_font_size` (integer)
- `report_card_heading_font_weight` (string)

### Controller Logic
`App\Http\Controllers\Admin\ReportSettingsController` handles the validation and storage of these settings.

### PDF Views
The following Blade views have been updated to dynamically apply these settings via inline CSS:
- `resources/views/admin/reports/pdf/report-card.blade.php` (Single student report)
- `resources/views/admin/reports/pdf/bulk-report-cards.blade.php` (Bulk class export)

### Layout Optimization
- Reduced margins and padding to maximize space.
- Student information and photo are displayed side-by-side using a table layout.
- Summary section uses a compact 2-column layout.
- Footer and signature lines are positioned to avoid page overflow.

### Image Handling
- **School Logo**: Uses `storage_path('app/public/' . setting('school_logo'))` to ensure DomPDF can access the file directly from the filesystem.
- **Student Photo**: Uses `storage_path('app/public/' . $student->profile_photo)` to display the student's profile image.

### CSS Logic
The styling uses the base font size to calculate relative sizes for headers:
```css
body {
    font-size: {{ setting('report_card_font_size', 12) }}px;
}
.school-name {
    /* Base size + 10px */
    font-size: {{ setting('report_card_font_size', 12) + 10 }}px;
}
.report-title {
    /* Base size + 6px */
    font-size: {{ setting('report_card_font_size', 12) + 6 }}px;
}
```

## Usage
1. Navigate to **Reports > Report Settings** in the admin dashboard.
2. Scroll to the **Typography** section.
3. Select your desired Font Family, Base Font Size, and Heading Weight.
4. Click **Save Settings**.
5. Generate a report card to see the changes.
