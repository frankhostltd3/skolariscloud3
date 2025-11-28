# Fee Structure Enhancements

## Overview
This document details the enhancements made to the Fee Structure module to support more complex fee types, specifically recurring fees and optional fees.

## Changes Implemented

### 1. Database Schema
- Added `is_recurring` (boolean) column to `fee_structures` table.
- Added `frequency` (string) column to `fee_structures` table.
- Validated existing `is_mandatory` column usage.

### 2. Backend Logic
- **Model (`App\Models\FeeStructure`)**:
    - Added `is_recurring`, `frequency`, and `is_mandatory` to `$fillable`.
    - Added boolean casting for `is_recurring` and `is_mandatory`.
- **Controller (`FeeStructureController`)**:
    - Updated `store` and `update` methods to validate and save the new fields.
    - Validation rules:
        - `is_recurring`: boolean
        - `frequency`: nullable|string|in:once,per_term,per_year
        - `is_mandatory`: boolean

### 3. Frontend UI
- **Form (`create.blade.php`)**:
    - Added "Mandatory Fee" checkbox with help text.
    - Added "Recurring Fee" checkbox with toggle logic.
    - Added "Frequency" dropdown (One-time, Per Term, Per Year) that appears only when "Recurring Fee" is checked.
    - Added JavaScript to handle the dynamic display of the frequency field.

## Usage
- **Mandatory Fees**: Checked by default. Uncheck for optional fees like clubs, trips, or extra-curricular activities.
- **Recurring Fees**: Check to indicate a fee repeats.
- **Frequency**: Select how often the fee repeats (e.g., every term or every year).

## Migration
To apply these changes to existing tenants, run:
```bash
php artisan tenants:migrate
```
