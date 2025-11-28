@extends('layouts.dashboard-teacher')

@section('title', 'Enter Grade')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Enter Grade</h4>
                        <p class="text-muted mb-0">
                            Student: {{ $student->name }} | Class: {{ $class->name }} | Subject: {{ $subject->name }}
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.teacher.grades.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="hidden" name="class_id" value="{{ $class->id }}">
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="assessment_name">Assessment Name *</label>
                                        <input type="text" class="form-control" id="assessment_name"
                                            name="assessment_name" value="{{ old('assessment_name') }}" required>
                                        @error('assessment_name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="assessment_type">Assessment Type *</label>
                                        <select class="form-control" id="assessment_type" name="assessment_type" required>
                                            <option value="">Select type...</option>
                                            @php
                                                $config = setting('assessment_configuration');
                                                $customTypes = [];
                                                if ($config && is_array($config)) {
                                                    foreach ($config as $c) {
                                                        $code = $c['code'] ?? $c['name'];
                                                        $name = $c['name'] ?? $code;
                                                        $customTypes[$code] = $name;
                                                    }
                                                }
                                                // Default types
                                                $defaultTypes = [
                                                    'quiz' => 'Quiz',
                                                    'test' => 'Test',
                                                    'exam' => 'Exam',
                                                    'assignment' => 'Assignment',
                                                    'project' => 'Project',
                                                    'homework' => 'Homework',
                                                    'participation' => 'Participation',
                                                    'other' => 'Other',
                                                ];

                                                // Merge custom types first if they exist
                                                $allTypes = $customTypes + $defaultTypes;
                                            @endphp

                                            @foreach ($allTypes as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('assessment_type') == $value ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('assessment_type')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="marks_obtained">Marks Obtained *</label>
                                        <input type="number" step="0.01" class="form-control" id="marks_obtained"
                                            name="marks_obtained" value="{{ old('marks_obtained') }}" required>
                                        @error('marks_obtained')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="total_marks">Total Marks *</label>
                                        <input type="number" step="0.01" class="form-control" id="total_marks"
                                            name="total_marks" value="{{ old('total_marks') }}" required>
                                        @error('total_marks')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="assessment_date">Assessment Date *</label>
                                        <input type="date" class="form-control" id="assessment_date"
                                            name="assessment_date" value="{{ old('assessment_date', date('Y-m-d')) }}"
                                            required>
                                        @error('assessment_date')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="term">Term/Semester</label>
                                        <input type="text" class="form-control" id="term" name="term"
                                            value="{{ old('term') }}" placeholder="e.g., Term 1, Semester 2">
                                        @error('term')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="comments">Comments/Notes</label>
                                        <textarea class="form-control" id="comments" name="comments" rows="3"
                                            placeholder="Optional comments about this grade">{{ old('comments') }}</textarea>
                                        @error('comments')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Grade
                                    </button>
                                    <a href="{{ route('tenant.teacher.grades.create') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Selection
                                    </a>
                                    <a href="{{ route('tenant.teacher.grades.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-list"></i> View All Grades
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-calculate percentage if both marks are entered
            const marksObtained = document.getElementById('marks_obtained');
            const totalMarks = document.getElementById('total_marks');

            function updatePercentage() {
                const obtained = parseFloat(marksObtained.value);
                const total = parseFloat(totalMarks.value);

                if (obtained >= 0 && total > 0 && obtained <= total) {
                    const percentage = ((obtained / total) * 100).toFixed(2);
                    // Could add a percentage display here if needed
                    console.log('Percentage: ' + percentage + '%');
                }
            }

            marksObtained.addEventListener('input', updatePercentage);
            totalMarks.addEventListener('input', updatePercentage);
        });
    </script>
@endsection
