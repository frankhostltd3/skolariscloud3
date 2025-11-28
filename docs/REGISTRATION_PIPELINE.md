# Registration Pipeline Overview

The registration pipeline unifies onboarding status across the landing page, admin, teacher, student, and parent dashboards. It is powered by the `App\Services\RegistrationPipelineService`, which collects tenant-safe statistics about student applications, approval status, email verification, and class placement.

## Service Responsibilities

- Detects table/column availability at runtime to stay compatible with tenants that have not yet migrated every module.
- Normalizes counts for four primary stages: submission, approval review, account verification, and class placement.
- Exposes tailored presenters for each audience (`adminOverview`, `teacherOverview`, `parentOverview`, `studentTimeline`, `landingSummary`).
- Calculates supportive metrics such as pending approvals, average approval turnaround, awaiting placement, and recent submissions (rolling 30 days).

## UI Integration

A reusable Blade component (`resources/views/components/registration/pipeline.blade.php`) renders the pipeline as a horizontal stage tracker plus quick metrics and “next action” guidance.

| Context  | Blade Hook                                                                                             |
|---------|--------------------------------------------------------------------------------------------------------|
| Landing | `resources/views/welcome.blade.php` (section before the hero)                                          |
| Admin   | `resources/views/tenant/admin/dashboard.blade.php` (pipeline + latest applicants table)                |
| Teacher | `resources/views/tenant/teacher/dashboard.blade.php` (pipeline + “Newest learners” table)              |
| Student | `resources/views/tenant/student/dashboard.blade.php` (timeline card near the top of the dashboard)     |
| Parent  | `resources/views/tenant/parent/dashboard.blade.php` (family-wide overview card)                        |

## Configuration Notes

- Approval mode text is derived from the `user_approval_mode` tenant setting (defaults to `manual`).
- Student segments fall back to role checks when the `user_type` column is missing to maintain backwards compatibility.
- Whenever class placement tables are absent, affected stages safely display as “upcoming” instead of generating SQL errors.

## Testing Checklist

1. Register a new student account and ensure the landing page “Applications (30 days)” card increments.
2. Approve or reject the student via the admin panel and confirm the admin + teacher pipelines update their status badges.
3. Log in as the student to see the four-step timeline, including prompts for email verification or class assignment.
4. Link the student to a parent account and confirm the parent dashboard reflects the matching pending/placement counts.
5. Repeat across multiple tenants to validate schema detection resilience.
