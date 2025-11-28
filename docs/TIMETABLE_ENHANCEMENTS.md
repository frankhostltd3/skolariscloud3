# Timetable Generation Enhancements (2025-11-26)

## New Features

### 1. Stream-Based Generation
- **Feature**: Generate timetables specifically for a class stream (e.g., "Grade 1 - Stream A").
- **Logic**:
  - If a class has streams, you can select a specific stream.
  - If you select a class but no stream, the system will generate timetables for **ALL streams** of that class automatically.
  - If a class has no streams, it generates for the class as usual.

### 2. Bulk Generation ("All Classes")
- **Feature**: Generate timetables for the entire school in one click.
- **Usage**: Select "All Classes & Streams" in the Generation Scope.
- **Logic**:
  - Iterates through every active class in the school.
  - If a class has streams, it generates for each stream.
  - If a class has no streams, it generates for the class.
  - **Note**: This process may take longer for large schools.

### 3. Improved Distribution Algorithm
- **Feature**: Subjects are now distributed more evenly across the week.
- **Old Logic**: Filled Monday completely, then Tuesday, etc. (Sequential).
- **New Logic**: Fills Period 1 for all days, then Period 2 for all days (Interleaved).
- **Benefit**: Prevents a subject from being scheduled 3 times on Monday and 0 times on Friday.

### 4. Manual Entry Option
- **Feature**: Added a direct link to "Manual Entry" from the generation page.
- **Usage**: Click the "Manual Entry" button to add specific timetable entries one by one.

### 5. Selectable Working Days
- **Feature**: Choose exactly which days (Mon-Sun) classes are held.
- **Usage**: Check the boxes for the days you want to schedule.

## How to Use

1.  Navigate to **Academics → Timetable → Generate**.
2.  **Select Scope**:
    *   **Single Class/Stream**: For generating one specific schedule.
    *   **All Classes & Streams**: For bulk generation.
3.  **Select Class** (if Single Scope).
4.  **Select Stream** (Optional - leave as "All Streams" to generate for all streams of the selected class).
5.  **Configure Settings**:
    *   Max Periods per Day
    *   Start Time
    *   Durations
    *   Working Days
6.  Click **Generate Timetable**.

## Technical Details

- **Controller**: `TimetableController::storeGenerated`
- **View**: `resources/views/admin/timetable/generate.blade.php`
- **Algorithm**:
  - **Teacher Conflict Check**: Ensures a teacher isn't scheduled for two classes at the same time.
  - **Slot Generation**: `[P1-Mon, P1-Tue, ... P1-Sun, P2-Mon...]`
  - **Lesson Shuffling**: Randomizes lesson order to prevent deterministic patterns.
