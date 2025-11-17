# Migration Order Guide

This document captures the order each migration set should run so that the schema is created deterministically for both the landlord (central) database and every tenant database.

---

## 1. Central (Landlord) Database
Run these migrations on the central connection in chronological order. The filenames already contain sortable timestamps; the grouping below shows the recommended batches.

1. **Laravel baseline (core tables)**
   - `0001_01_01_000000_create_users_table.php`
   - `0001_01_01_000001_create_cache_table.php`
   - `0001_01_01_000002_create_jobs_table.php`
2. **User profile and security extensions**
   - `2025_09_29_163018_add_profile_fields_to_users_table.php`
   - `2025_10_03_190000_add_password_security_fields_to_users_table.php`
   - `2025_10_03_190309_add_two_factor_fields_to_users_table.php`
   - `2025_10_09_130458_add_last_activity_at_to_users_table.php`
3. **Landlord billing + notifications**
   - `2025_10_01_000001_create_landlord_dunning_policies_table.php`
   - `2025_10_01_000001_create_landlord_invoices_table.php`
   - `2025_10_01_000002_create_landlord_invoice_items_table.php`
   - `2025_10_01_120000_create_landlord_audit_logs_table.php`
   - `2025_10_01_121000_create_landlord_notifications_table.php`
4. **School registry and tenant linkage**
   - `2025_11_13_000000_create_schools_table.php`
   - `2025_11_13_120000_add_subdomain_to_schools_table.php`
   - `2025_11_13_000001_add_user_type_to_users_table.php`
   - `2025_11_13_000002_add_school_id_to_users_table.php`
   - `2025_11_13_000003_create_school_user_invitations_table.php`
5. **Mail + settings infrastructure**
   - `2025_11_13_130000_create_mail_settings_table.php`
   - `2025_11_13_131500_update_mail_settings_to_global.php`
   - `2025_11_15_120000_create_settings_table.php`
   - `2025_11_15_140504_create_settings_table.php` *(kept for historical compatibility; ensure duplicates don’t conflict)*
6. **HR and approval support**
   - `2025_11_13_193540_add_approval_fields_to_users_table.php`
   - `2025_11_13_193545_add_approval_fields_to_users_table.php`
   - `2025_11_13_195522_create_departments_table.php`
   - `2025_11_13_195523_create_positions_table.php`
7. **Payments + messaging settings**
   - `2025_11_15_090000_create_payment_gateway_settings_table.php`
   - `2025_11_15_110100_create_messaging_channel_settings_table.php`
8. **Currency + exchange-rate stack**
   - `2025_11_15_165413_create_currencies_table.php`
   - `2025_11_15_165419_create_currencies_table.php`
   - `2025_11_15_171007_add_exchange_rate_metadata_to_currencies_table.php`
9. **Sessions / queues / cache**
   - `2025_11_15_180000_create_sessions_table.php`
   - `2025_11_17_021154_create_cache_tables.php`
   - `2025_11_17_021154_create_queue_tables.php`
10. **System feature migrations (bulk dated 2025-11-17)**
    - Run all `2025_11_17_021154_*` migrations in order (alphabetical already reflects creation sequence).
    - Follow with all `2025_11_17_021155_*` migrations.
    - Finish with `2025_11_17_021156_add_approval_fields_to_users_table.php`.

> **Tip:** Because duplicates (e.g., two settings or currency migrations) exist for backwards compatibility, confirm whether both need to run or if one supersedes the other in your environment. If a newer file replaces an older one, comment it out in `migrations.json` or rename accordingly.

### Command
```powershell
php artisan migrate
```
(This runs on the default central connection. Use `--database=landlord` if you named the connection differently.)

---

## 2. Tenant Database
Each tenant runs the tenant migration stack. The filenames already form an ordered timeline (2024-01-01 base → feature increments). Recommended grouping:

1. **Tenant baseline**
   - `2024_01_01_000001_create_users_table.php`
   - `2024_01_01_000002_create_cache_table.php`
   - `2024_01_01_000003_create_jobs_table.php`
2. **Core configuration**
   - `2024_01_01_000010_create_settings_table.php`
   - `2024_01_01_000011_create_mail_settings_table.php`
   - `2024_01_01_000012_create_payment_gateway_settings_table.php`
   - `2024_01_01_000013_create_messaging_channel_settings_table.php`
   - `2024_01_01_000014_create_attendance_settings_table.php`
3. **Currencies + permissions**
   - `2024_01_01_000020_create_currencies_table.php`
   - `2024_01_01_000021_add_exchange_rate_metadata_to_currencies_table.php`
   - `2024_01_01_000030_create_permission_tables.php`
   - `2024_01_01_000031_add_approval_fields_to_users_table.php`
4. **HR & staff**
   - Department/position/salary/employees migrations (`000040`–`000053`).
   - Teacher tables (`000060`–`000062`).
5. **Academics (levels, exams, grading, terms, rooms, classes, streams)**
   - `000070`–`000081` range.
6. **Subjects & timetable**
   - `000090`–`000113`.
7. **Attendance / biometrics**
   - `000120`–`000124`.
8. **Finance**
   - `000130`–`000137` (expense categories through payment transactions).
9. **Quizzes & reports**
   - `000140`, `000150`, etc.

Keep marching forward until the newest tenant migration. Whenever you add a migration, respect the timestamp ordering so `php artisan tenant:migrate` (or your tenancy package command) picks it up automatically.

### Command
Use your tenancy tooling; example using Laravel Tenancy:
```powershell
php artisan tenants:migrate
```
Or, for a single school:
```powershell
php artisan tenants:migrate --tenant=tenant_000007
```

---

## 3. Suggested Workflow
1. **Central first:** `php artisan migrate` (or `php artisan migrate --database=landlord`).
2. **Seed central data if needed:** `php artisan db:seed`.
3. **Tenant migrations:** `php artisan tenants:migrate` (or targeted tenant).
4. **Tenant seeds:** `php artisan tenants:seed` (if defined).

Running in this order guarantees the landlord tables (schools, connection metadata, queue jobs) exist before any tenant tries to boot.

---

## 4. Troubleshooting
- If you hit "table already exists" errors, verify whether duplicate migrations should be pruned or whether you’re re-running on an existing schema. Consider `php artisan migrate:status` on both connections.
- To rebuild from scratch in dev environments:
  ```powershell
  php artisan migrate:fresh
  php artisan tenants:migrate:fresh
  ```
- Always back up tenant databases before altering migration order in production.
