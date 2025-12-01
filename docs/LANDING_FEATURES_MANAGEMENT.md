 # Landing Features Management

This module allows the Landlord to manage the "Features" section on the landing page dynamically.

## Database Schema

The `landing_features` table stores the following information:
- `title`: The title of the feature.
- `description`: A short description of the feature.
- `icon`: The Bootstrap Icon class (e.g., `bi-people-fill`).
- `icon_color`: The color of the icon (can be a CSS variable or a hex code).
- `icon_bg_color`: The background color of the icon circle.
- `sort_order`: The order in which the features appear.
- `is_active`: Whether the feature is visible on the landing page.

## Landlord Panel

The Landlord can manage features via the "Landing Features" menu item in the sidebar.
- **List Features**: View all features with their status and sort order.
- **Create Feature**: Add a new feature with custom icon and colors.
- **Edit Feature**: Update existing features.
- **Delete Feature**: Remove a feature.

## Frontend Integration

The `HomeController` fetches active features ordered by `sort_order` and passes them to the `home` view.
The `home.blade.php` view iterates over the features and displays them using the configured icon and colors.
If no features are found in the database, the view falls back to the hardcoded default features.

## Files Created/Modified

- `database/migrations/2025_12_01_102849_create_landing_features_table.php` (New)
- `app/Models/LandingFeature.php` (New)
- `app/Http/Controllers/Landlord/LandingFeatureController.php` (New)
- `resources/views/landlord/landing-features/index.blade.php` (New)
- `resources/views/landlord/landing-features/create.blade.php` (New)
- `resources/views/landlord/landing-features/edit.blade.php` (New)
- `app/Http/Controllers/HomeController.php` (Modified)
- `resources/views/home.blade.php` (Modified)
- `routes/web.php` (Modified)
- `resources/views/landlord/layouts/app.blade.php` (Modified)
