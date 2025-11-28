# Online Exam Automation & AI Hooks

This document describes the scheduling automation and generation hooks that ship with the workflow-driven online exams module.

## Scheduling & Lifecycle Automation

- **Command**: `php artisan tenants:sync-exams`
  - Iterates over each active tenant database.
  - Activates approved exams whose `starts_at` has passed and `activation_mode` is `schedule` or `auto`.
  - Completes active exams whose `ends_at` timestamp has elapsed.
  - Notifies the owning teacher via `ExamReviewDecisionNotification` whenever an exam is auto-activated or auto-completed.
- **Scheduler**: The command is registered in `routes/console.php` and runs every five minutes by default. Adjust the CRON cadence if you need a tighter SLA.
- **Service Layer**: `App\Services\ExamWindowAutomationService` encapsulates the activation/completion logic and can be reused from tests or other console utilities.

## Teacher-Facing Generation Hooks

- **Creation Methods**: The teacher UI now exposes `manual`, `automatic`, and `ai` creation modes. Manual exams behave exactly as before; automatic or AI exams can queue blueprint generation.
- **Generation Widget**: `resources/views/tenant/teacher/classroom/exams/show.blade.php` contains an "Automation & AI" card that:
  - Shows current `generation_status`, provider, and the last request context.
  - Captures syllabus topics, learning objectives, difficulty, and desired question types.
  - Queues a generation request via `POST /tenant/teacher/classroom/exams/{exam}/generate` unless a request is already in progress.
- **Controller Endpoint**: `ExamController::generate()` validates input, records metadata, and dispatches `ProcessExamGeneration`.
- **Job & Service**:
  - `ProcessExamGeneration` is queueable and hands off to `ExamGenerationService`.
  - `ExamGenerationService` implements a direct integration with OpenAI (via `gpt-4o-mini` or configured model) to generate structured exam content.
  - If `EXAM_GENERATION_DRIVER` is not set or fails, the service gracefully falls back to a blueprint generator that scaffolds sections/questions based on the submitted context.

## Configuration

Add the following environment variables (optional but recommended):

```
EXAM_GENERATION_DRIVER=openai
OPENAI_API_KEY=sk-...
# Recommended models: gpt-4o, gpt-4o-mini, gpt-4-turbo
OPENAI_EXAM_MODEL=gpt-4o-mini
AZURE_OPENAI_ENDPOINT=https://...
AZURE_OPENAI_DEPLOYMENT=exam-agent
AZURE_OPENAI_API_KEY=...
```

These values map to the new `services.exam_generation` config entry and will be read by future provider integrations.

## Notifications & Safety

- Teachers are notified whenever automation activates or closes their exam window, ensuring they remain aware of status changes even if they did not trigger them manually.
- Blueprint generation adds placeholder content and clear instructions so teachers can review/edit before publishing. Nothing goes live without the existing admin approval workflow.

## Testing Notes

- Run `php artisan tenants:sync-exams --tenant={id}` to test automation against a single tenant.
- Trigger generation from the teacher exam detail page to verify status transitions (`requested → processing → completed`).
- Check the database `online_exams.generation_metadata` column to confirm request context is being persisted for audit purposes.
