@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Create New Grade')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create New Grade</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="class_id">Select Class</label>
                                <select class="form-control" id="class_id" name="class_id">
                                    <option value="">Choose a class...</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="subject_id">Select Subject</label>
                                <select class="form-control" id="subject_id" name="subject_id" {{ !$classId ? 'disabled' : '' }}>
                                    <option value="">Choose a subject...</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="student_id">Select Student</label>
                                <select class="form-control" id="student_id" name="student_id" {{ !$classId ? 'disabled' : '' }}>
                                    <option value="">Choose a student...</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->name }} ({{ $student->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="button" id="proceed-btn" class="btn btn-primary" disabled>
                                <i class="fas fa-arrow-right"></i> Proceed to Grade Entry
                            </button>
                            <a href="{{ route('tenant.teacher.grades.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const studentSelect = document.getElementById('student_id');
    const proceedBtn = document.getElementById('proceed-btn');

    function updateProceedButton() {
        const classSelected = classSelect.value !== '';
        const subjectSelected = subjectSelect.value !== '';
        const studentSelected = studentSelect.value !== '';

        proceedBtn.disabled = !(classSelected && subjectSelected && studentSelected);
    }

    function updateSubjectsAndStudents() {
        const classId = classSelect.value;

        if (classId) {
            // Reload page with class_id parameter to get subjects and students
            window.location.href = '{{ route("tenant.teacher.grades.create") }}?class_id=' + classId;
        } else {
            subjectSelect.disabled = true;
            studentSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Choose a subject...</option>';
            studentSelect.innerHTML = '<option value="">Choose a student...</option>';
            updateProceedButton();
        }
    }

    classSelect.addEventListener('change', updateSubjectsAndStudents);
    subjectSelect.addEventListener('change', updateProceedButton);
    studentSelect.addEventListener('change', updateProceedButton);

    // Initial state
    updateProceedButton();

    proceedBtn.addEventListener('click', function() {
        const classId = classSelect.value;
        const subjectId = subjectSelect.value;
        const studentId = studentSelect.value;

        if (classId && subjectId && studentId) {
            // Redirect to the teacher grade entry form
            window.location.href = '{{ route("tenant.teacher.grades.create") }}?class_id=' + classId + '&subject_id=' + subjectId + '&student_id=' + studentId + '&action=enter_grade';
        }
    });
});
</script>
@endsection
