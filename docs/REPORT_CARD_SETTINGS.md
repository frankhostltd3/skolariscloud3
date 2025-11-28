# Report Card Settings Implementation

## Overview
A new settings module has been added to the Admin Panel to allow tenants to customize their report cards. This includes options for branding, content, and layout.

## Features
- **Branding:**
  - Toggle School Logo display.
  - Customize School Name heading.
  - Custom Address and Contact Information.
  - Color Theme selection (affects headers and borders).
- **Content:**
  - Customizable Signature Titles (e.g., Class Teacher, Principal, Parent).
- **Preview:**
  - Settings are applied immediately to all generated PDFs.

## Implementation Details

### 1. Database
- Uses the existing `settings` table via the `setting()` helper.
- Keys used:
  - `report_card_show_logo` (boolean)
  - `report_card_school_name` (string)
  - `report_card_address` (string)
  - `report_card_color_theme` (string, hex color)
  - `report_card_signature_1` (string)
  - `report_card_signature_2` (string)
  - `report_card_signature_3` (string)

### 2. Controller
- **File:** `app/Http/Controllers/Admin/ReportSettingsController.php`
- **Methods:**
  - `edit()`: Displays the settings form.
  - `update()`: Validates and saves settings.

### 3. View
- **File:** `resources/views/admin/reports/settings.blade.php`
- **Route:** `/admin/reports/settings`
- **Menu:** Reports > Report Settings

### 4. PDF Template
- **File:** `resources/views/admin/reports/pdf/report-card.blade.php`
- Updated to dynamically pull configuration from settings.

## Usage
1.  Navigate to **Reports > Report Settings**.
2.  Configure the desired options.
3.  Click **Save Settings**.
4.  Generate a report card to see the changes.
