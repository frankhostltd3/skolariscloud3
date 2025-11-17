@extends('tenant.layouts.app')

@section('title', __('Academic Settings'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('Academic Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.settings.admin.academic.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Academic Year -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Academic Year') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="current_academic_year" class="form-label">{{ __('Current Academic Year') }}</label>
                                <input type="text" class="form-control @error('current_academic_year') is-invalid @enderror"
                                       id="current_academic_year" name="current_academic_year"
                                       value="{{ old('current_academic_year', setting('current_academic_year', date('Y') . '-' . (date('Y') + 1))) }}"
                                       placeholder="e.g., 2024-2025" required>
                                @error('current_academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="academic_year_start" class="form-label">{{ __('Academic Year Start Date') }}</label>
                                <input type="date" class="form-control @error('academic_year_start') is-invalid @enderror"
                                       id="academic_year_start" name="academic_year_start"
                                       value="{{ old('academic_year_start', setting('academic_year_start', date('Y') . '-01-01')) }}" required>
                                @error('academic_year_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="academic_year_end" class="form-label">{{ __('Academic Year End Date') }}</label>
                                <input type="date" class="form-control @error('academic_year_end') is-invalid @enderror"
                                       id="academic_year_end" name="academic_year_end"
                                       value="{{ old('academic_year_end', setting('academic_year_end', (date('Y') + 1) . '-12-31')) }}" required>
                                @error('academic_year_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Grading System -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Grading System') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="grading_scale" class="form-label">{{ __('Grading Scale') }}</label>
                                <select class="form-select @error('grading_scale') is-invalid @enderror" id="grading_scale" name="grading_scale">
                                    <option value="letter" {{ old('grading_scale', setting('grading_scale', 'letter')) == 'letter' ? 'selected' : '' }}>Letter Grades (A, B, C, D, F)</option>
                                    <option value="percentage" {{ old('grading_scale', setting('grading_scale', 'letter')) == 'percentage' ? 'selected' : '' }}>Percentage (0-100)</option>
                                    <option value="points" {{ old('grading_scale', setting('grading_scale', 'letter')) == 'points' ? 'selected' : '' }}>Points System</option>
                                </select>
                                @error('grading_scale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="passing_grade" class="form-label">{{ __('Passing Grade') }}</label>
                                <input type="number" class="form-control @error('passing_grade') is-invalid @enderror"
                                       id="passing_grade" name="passing_grade" min="0" max="100"
                                       value="{{ old('passing_grade', setting('passing_grade', 50)) }}" required>
                                @error('passing_grade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Class Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Class Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="max_students_per_class" class="form-label">{{ __('Maximum Students Per Class') }}</label>
                                <input type="number" class="form-control @error('max_students_per_class') is-invalid @enderror"
                                       id="max_students_per_class" name="max_students_per_class" min="1"
                                       value="{{ old('max_students_per_class', setting('max_students_per_class', 40)) }}" required>
                                @error('max_students_per_class')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="min_students_per_class" class="form-label">{{ __('Minimum Students Per Class') }}</label>
                                <input type="number" class="form-control @error('min_students_per_class') is-invalid @enderror"
                                       id="min_students_per_class" name="min_students_per_class" min="1"
                                       value="{{ old('min_students_per_class', setting('min_students_per_class', 15)) }}" required>
                                @error('min_students_per_class')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="class_naming_convention" class="form-label">{{ __('Class Naming Convention') }}</label>
                                <select class="form-select @error('class_naming_convention') is-invalid @enderror" id="class_naming_convention" name="class_naming_convention">
                                    <option value="grade" {{ old('class_naming_convention', setting('class_naming_convention', 'grade')) == 'grade' ? 'selected' : '' }}>Grade (Grade 1, Grade 2)</option>
                                    <option value="form" {{ old('class_naming_convention', setting('class_naming_convention', 'grade')) == 'form' ? 'selected' : '' }}>Form (Form 1, Form 2)</option>
                                    <option value="standard" {{ old('class_naming_convention', setting('class_naming_convention', 'grade')) == 'standard' ? 'selected' : '' }}>Standard (Standard 1, Standard 2)</option>
                                    <option value="year" {{ old('class_naming_convention', setting('class_naming_convention', 'grade')) == 'year' ? 'selected' : '' }}>Year (Year 1, Year 2)</option>
                                </select>
                                @error('class_naming_convention')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Attendance Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Attendance Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="attendance_required" class="form-label">{{ __('Attendance Required') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="attendance_required" name="attendance_required" value="1"
                                           {{ old('attendance_required', setting('attendance_required', true)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="attendance_required">
                                        {{ __('Require daily attendance tracking') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="attendance_grace_period" class="form-label">{{ __('Attendance Grace Period (minutes)') }}</label>
                                <input type="number" class="form-control @error('attendance_grace_period') is-invalid @enderror"
                                       id="attendance_grace_period" name="attendance_grace_period" min="0"
                                       value="{{ old('attendance_grace_period', setting('attendance_grace_period', 15)) }}">
                                @error('attendance_grace_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="attendance_marking_deadline" class="form-label">{{ __('Attendance Marking Deadline (hours after class)') }}</label>
                                <input type="number" class="form-control @error('attendance_marking_deadline') is-invalid @enderror"
                                       id="attendance_marking_deadline" name="attendance_marking_deadline" min="0"
                                       value="{{ old('attendance_marking_deadline', setting('attendance_marking_deadline', 24)) }}">
                                @error('attendance_marking_deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Examination Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Examination Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="exam_types" class="form-label">{{ __('Default Exam Types') }}</label>
                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="exam_midterm" name="exam_types[]" value="midterm"
                                               {{ in_array('midterm', old('exam_types', setting('exam_types', ['midterm', 'final']))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exam_midterm">{{ __('Midterm') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="exam_final" name="exam_types[]" value="final"
                                               {{ in_array('final', old('exam_types', setting('exam_types', ['midterm', 'final']))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exam_final">{{ __('Final') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="exam_quiz" name="exam_types[]" value="quiz"
                                               {{ in_array('quiz', old('exam_types', setting('exam_types', ['midterm', 'final']))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exam_quiz">{{ __('Quiz') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="exam_assignment" name="exam_types[]" value="assignment"
                                               {{ in_array('assignment', old('exam_types', setting('exam_types', ['midterm', 'final']))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="exam_assignment">{{ __('Assignment') }}</label>
                                    </div>
                                </div>
                                @error('exam_types')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="exam_result_publication" class="form-label">{{ __('Exam Result Publication') }}</label>
                                <select class="form-select @error('exam_result_publication') is-invalid @enderror" id="exam_result_publication" name="exam_result_publication">
                                    <option value="immediate" {{ old('exam_result_publication', setting('exam_result_publication', 'manual')) == 'immediate' ? 'selected' : '' }}>Immediate</option>
                                    <option value="manual" {{ old('exam_result_publication', setting('exam_result_publication', 'manual')) == 'manual' ? 'selected' : '' }}>Manual Publication</option>
                                    <option value="scheduled" {{ old('exam_result_publication', setting('exam_result_publication', 'manual')) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                                @error('exam_result_publication')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject Settings -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">{{ __('Subject Settings') }}</h5>
                            </div>
                            <div class="col-md-6">
                                <label for="mandatory_subjects" class="form-label">{{ __('Mandatory Subjects') }}</label>
                                <input type="text" class="form-control @error('mandatory_subjects') is-invalid @enderror"
                                       id="mandatory_subjects" name="mandatory_subjects"
                                       value="{{ old('mandatory_subjects', setting('mandatory_subjects', 'Mathematics,English,Science')) }}"
                                       placeholder="Comma-separated list">
                                @error('mandatory_subjects')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="elective_subjects_limit" class="form-label">{{ __('Maximum Elective Subjects Per Student') }}</label>
                                <input type="number" class="form-control @error('elective_subjects_limit') is-invalid @enderror"
                                       id="elective_subjects_limit" name="elective_subjects_limit" min="0"
                                       value="{{ old('elective_subjects_limit', setting('elective_subjects_limit', 3)) }}">
                                @error('elective_subjects_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> {{ __('Save Academic Settings') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection