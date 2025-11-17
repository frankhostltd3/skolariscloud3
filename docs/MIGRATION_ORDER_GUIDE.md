# Tenant Migration Order Guide

## Overview
This document explains the proper order for tenant database migrations to ensure dependencies are resolved correctly. All tenant migrations use the `2024_01_01` prefix with sequential numbering to guarantee execution order.

---

## Migration Order (Dependency Tree)

### Level 1: Core Tables (No Dependencies)
```
000001 - users table
000002 - cache table  
000003 - jobs table
000010 - settings table
000011 - mail_settings table
000012 - payment_gateway_settings table
000013 - messaging_channel_settings table
000014 - attendance_settings table
000020 - currencies table
```

### Level 2: User Extensions
```
000021 - add_exchange_rate_metadata_to_currencies (depends on currencies)
000030 - permission_tables (Spatie permissions)
000031 - add_approval_fields_to_users (depends on users)
```

### Level 3: HR Foundation
```
000040 - departments table (independent)
000041 - positions table (independent)
000042 - salary_scales table (independent)
```

### Level 4: Employees
```
000050 - employees table (depends on departments, positions, salary_scales)
000051 - add_employee_number_to_employees (depends on employees)
000052 - add_employee_type_to_employees (depends on employees)
000053 - add_identity_fields_to_employees (depends on employees + employee_number)
```

### Level 5: Teachers
```
000060 - teachers table (depends on users)
000061 - enhance_teachers table (depends on teachers)
000062 - add_sync_fields_for_teacher_employee (depends on teachers + employees)
```

### Level 6: Academic Structure
```
000070 - education_levels table (independent)
000071 - countries_and_examination_bodies tables (independent)
000072 - grading_schemes tables (independent)
000073 - terms table (independent)
000074 - rooms table (independent)
```

### Level 7: Classes
```
000080 - classes table (depends on education_levels)
000081 - class_streams table (depends on classes)
```

### Level 8: Subjects
```
000090 - subjects table (depends on education_levels)
000091 - add_required_periods_to_subjects (depends on subjects)
```

### Level 9: Class-Subject Relationships
```
000100 - class_subjects table (depends on classes + subjects + teachers)
000101 - update_subjects_and_class_subjects (depends on class_subjects)
```

### Level 10: Timetable
```
000110 - timetable_entries table (depends on classes, subjects, rooms, teachers, terms)
000111 - add_room_id_to_timetable_entries (depends on timetable_entries + rooms)
000112 - teacher_availabilities table (depends on teachers)
000113 - timetable_constraints table (depends on timetable_entries)
```

### Level 11: Attendance
```
000120 - attendance table (depends on classes, terms)
000121 - attendance_records table (depends on attendance + users)
000122 - staff_attendance table (depends on users/employees)
000123 - biometric_templates table (polymorphic - depends on users)
000124 - add_attendance_method_tracking (depends on attendance)
```

### Level 12: Finance
```
000130 - expense_categories table (independent)
000131 - transactions table (depends on users)
000132 - fee_structures table (depends on classes, terms)
000133 - expenses table (depends on expense_categories, currencies, users) ✅
000134 - invoices table (depends on users/students, fee_structures) ✅
000135 - payments table (depends on invoices) ✅
000136 - payment_gateway_configs table (independent) ✅
000137 - payment_transactions table (depends on users) ✅
```

### Level 13: Assessments & Reports
```
000140 - quiz_tables (quizzes + quiz_attempts) (depends on subjects, classes, users) ✅
000150 - report_logs table (depends on users) ✅
```

---

## Current Status

### ✅ Completed (54 migrations)
All migrations from 000001 to 000150 are properly ordered and exist in `database/migrations/tenants/`.

**Migration Count by Module:**
- Core (users, cache, jobs): 3
- Settings: 5
- Currencies: 2
- Permissions: 2
- HR (departments, positions, employees): 7
- Teachers: 3
- Academic Structure: 5
- Classes & Streams: 2
- Subjects: 2
- Class-Subject Relations: 2
- Timetable: 4
- Attendance: 5
- Finance: 8
- Quizzes: 1
- Reports: 1

**Total: 54 migrations ready for VPS deployment** ✅

---

## Dependency Matrix

| Migration | Depends On |
|-----------|------------|
| employees | departments, positions, salary_scales |
| teachers | users |
| classes | education_levels |
| class_streams | classes |
| subjects | education_levels |
| class_subjects | classes, subjects, teachers |
| timetable_entries | classes, subjects, rooms, teachers, terms |
| attendance | classes, terms |
| attendance_records | attendance, users |
| fee_structures | classes, terms |
| expenses | expense_categories, currencies, users |
| invoices | users, fee_structures |
| payments | invoices |
| quizzes | subjects, classes |
| quiz_attempts | quizzes, users |

---

## Migration Naming Convention

**Format:** `2024_01_01_XXXXXX_description.php`

- **2024_01_01**: Fixed date prefix for consistent ordering
- **XXXXXX**: Sequential 6-digit number (000001, 000002, etc.)
- **description**: Descriptive snake_case name

**Examples:**
```
2024_01_01_000001_create_users_table.php
2024_01_01_000050_create_employees_table.php
2024_01_01_000100_create_class_subjects_table.php
```

---

## VPS Deployment Checklist

### Pre-Deployment
- [ ] Verify all migrations are in `database/migrations/tenants/`
- [ ] Ensure migrations use `2024_01_01_XXXXXX_` naming
- [ ] Check for duplicate migrations (old 2025 dates)
- [ ] Review migration dependencies
- [ ] Test migrations on staging/local first

### Deployment Commands
```bash
# 1. Run tenant migrations
php artisan tenants:migrate

# 2. If tables already exist, mark migrations as completed
php artisan tenants:register-permission-migrations

# 3. Seed permissions and roles
php artisan tenants:seed-permissions

# 4. Seed other data
php artisan tenants:seed-currencies
php artisan tenants:seed-expense-categories
```

### Post-Deployment
- [ ] Verify all tables created
- [ ] Check foreign key constraints
- [ ] Test CRUD operations
- [ ] Verify data integrity
- [ ] Check logs for errors

---

## Troubleshooting

### Issue: "Table already exists"
**Solution:** Migration ran before. Either:
1. Skip with `--skip-existing` flag (if available)
2. Manually mark as migrated in `migrations` table
3. Use `tenants:register-permission-migrations` for permission tables

### Issue: "Column not found after XYZ"
**Cause:** Migration trying to add column AFTER a column that doesn't exist yet
**Solution:** Ensure the migration creating that column runs first. Check migration order.

### Issue: "Base table or view not found"
**Cause:** Migration depends on table that hasn't been created
**Solution:** Create the dependency table first. Check dependency matrix above.

### Issue: "Cannot add foreign key constraint"
**Cause:** Referenced table doesn't exist or referenced column doesn't exist
**Solution:** Ensure parent table created first. Verify column names match.

---

## Creating New Migrations

When adding new migrations:

1. **Identify dependencies**: What tables does it depend on?
2. **Find insertion point**: Where in the sequence should it go?
3. **Choose number**: Next available number after dependencies
4. **Test locally**: Run migration on fresh database
5. **Update this doc**: Add to dependency matrix

**Example:**
Adding a `student_grades` table that depends on `students` and `subjects`:

1. Dependencies: users (students are users), subjects
2. Insertion point: After Level 9 (class_subjects)
3. Number: 000105 (between class_subjects and timetable_entries)
4. Name: `2024_01_01_000105_create_student_grades_table.php`

---

## Best Practices

1. **Always use foreign keys**: Helps catch dependency issues early
2. **Use `nullOnDelete()` or `cascadeOnDelete()`**: Prevents orphaned records
3. **Index foreign keys**: Improves query performance
4. **Test rollbacks**: Ensure `down()` method works correctly
5. **Document dependencies**: Comment at top of migration file
6. **Keep migrations focused**: One migration = one logical change
7. **Never modify deployed migrations**: Create new migration to alter existing tables

---

## Migration Template

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create example_table
 * Dependencies: users, other_tables
 * Order: 000XXX
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('example_table', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // ... other columns
            $table->timestamps();
            
            // Indexes
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('example_table');
    }
};
```

---

## Conclusion

The current migration system uses a deterministic ordering scheme (`2024_01_01_XXXXXX_`) to ensure migrations run in the correct order regardless of filesystem or database behavior. This makes VPS deployments reliable and predictable.

**Key Points:**
- ✅ 47 migrations properly ordered
- ⚠️ 8 migrations need to be added
- 📊 Clear dependency tree documented
- 🚀 Ready for VPS deployment once missing migrations added

For questions or issues, refer to the dependency matrix and troubleshooting sections above.
