@extends('tenant.layouts.app')

@section('title', __('Send Fee Reminders'))

@section('sidebar')
<div class="card shadow-sm mb-4">
  <div class="card-header fw-semibold">{{ __('Fee Management') }}</div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.overview') }}">
      <span class="bi bi-speedometer2 me-2"></span>{{ __('Financial Overview') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.fees') }}">
      <span class="bi bi-cash-stack me-2"></span>{{ __('Fee Management') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.fees.assign') }}">
      <span class="bi bi-person-plus me-2"></span>{{ __('Assign Fees') }}
    </a>
    <a class="list-group-item list-group-item-action active" href="{{ route('tenant.modules.financials.fees.reminders') }}">
      <span class="bi bi-envelope me-2"></span>{{ __('Send Reminders') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.expenses') }}">
      <span class="bi bi-receipt me-2"></span>{{ __('Expenses') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.tuition_plans') }}">
      <span class="bi bi-file-earmark-text me-2"></span>{{ __('Tuition Plans') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.invoices') }}">
      <span class="bi bi-file-earmark-pdf me-2"></span>{{ __('Invoices') }}
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header fw-semibold">{{ __('Reminder Tips') }}</div>
  <div class="card-body">
    <div class="small text-muted">
      <div class="mb-3">
        <strong>{{ __('Reminder Types') }}</strong>
        <ul class="mt-2 mb-0">
          <li>{{ __('Overdue: Remind about past due fees') }}</li>
          <li>{{ __('Upcoming: Remind about fees due soon') }}</li>
          <li>{{ __('All: Send reminders for all outstanding fees') }}</li>
        </ul>
      </div>

      <div class="alert alert-info small p-2">
        <strong>{{ __('Tip:') }}</strong> {{ __('Use specific targeting to avoid overwhelming recipients with too many notifications.') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="h5 fw-semibold mb-0">{{ __('Send Fee Reminders') }}</h1>
        <div class="small text-secondary">{{ __('Send payment reminders to students and parents') }}</div>
    </div>
    <div class="card-body">
        <form action="{{ route('tenant.modules.financials.fees.reminders.process') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fee_ids" class="form-label">{{ __('Select Fee Items') }} <span class="text-danger">*</span></label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($fees as $fee)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fee_{{ $fee->id }}"
                                           name="fee_ids[]" value="{{ $fee->id }}">
                                    <label class="form-check-label" for="fee_{{ $fee->id }}">
                                        {{ $fee->name }} - ${{ number_format($fee->amount, 2) }}
                                        <small class="text-muted">({{ ucfirst($fee->recurring_type) }})</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('fee_ids')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="reminder_type" class="form-label">{{ __('Reminder Type') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('reminder_type') is-invalid @enderror"
                                id="reminder_type" name="reminder_type" required>
                            <option value="">{{ __('Select Reminder Type') }}</option>
                            <option value="overdue" {{ old('reminder_type') == 'overdue' ? 'selected' : '' }}>{{ __('Overdue Fees') }}</option>
                            <option value="upcoming" {{ old('reminder_type') == 'upcoming' ? 'selected' : '' }}>{{ __('Upcoming Due Dates') }}</option>
                            <option value="all" {{ old('reminder_type') == 'all' ? 'selected' : '' }}>{{ __('All Outstanding Fees') }}</option>
                        </select>
                        @error('reminder_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="target_audience" class="form-label">{{ __('Target Audience') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('target_audience') is-invalid @enderror"
                                id="target_audience" name="target_audience" required>
                            <option value="">{{ __('Select Target Audience') }}</option>
                            <option value="all_students" {{ old('target_audience') == 'all_students' ? 'selected' : '' }}>{{ __('All Students') }}</option>
                            <option value="specific_class" {{ old('target_audience') == 'specific_class' ? 'selected' : '' }}>{{ __('Specific Class') }}</option>
                            <option value="specific_students" {{ old('target_audience') == 'specific_students' ? 'selected' : '' }}>{{ __('Specific Students') }}</option>
                        </select>
                        @error('target_audience')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Class Selection -->
            <div class="mb-3" id="class_selection" style="display: none;">
                <label for="class_id" class="form-label">{{ __('Select Class') }} <span class="text-danger">*</span></label>
                <select class="form-select @error('class_id') is-invalid @enderror"
                        id="class_id" name="class_id">
                    <option value="">{{ __('Select Class') }}</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Student Selection -->
            <div class="mb-3" id="student_selection" style="display: none;">
                <label for="student_ids" class="form-label">{{ __('Select Students') }} <span class="text-danger">*</span></label>
                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                    @foreach($students as $student)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="student_{{ $student->id }}"
                                   name="student_ids[]" value="{{ $student->id }}">
                            <label class="form-check-label" for="student_{{ $student->id }}">
                                {{ $student->name }} ({{ $student->student_id }})
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('student_ids')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">{{ __('Custom Message') }}</label>
                <textarea class="form-control @error('message') is-invalid @enderror"
                          id="message" name="message" rows="4"
                          placeholder="{{ __('Add a custom message to include with the reminder...') }}">{{ old('message') }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">{{ __('Leave blank to use the default reminder message.') }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Send Via') }}</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_email" name="send_email"
                                   value="1" {{ old('send_email', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_email">
                                <i class="bi bi-envelope me-1"></i>{{ __('Email') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_sms" name="send_sms"
                                   value="1" {{ old('send_sms') ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_sms">
                                <i class="bi bi-phone me-1"></i>{{ __('SMS/WhatsApp') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>{{ __('Send Reminders') }}
                </button>
                <a href="{{ route('tenant.modules.financials.fees') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const targetAudienceSelect = document.getElementById('target_audience');
    const classSelection = document.getElementById('class_selection');
    const studentSelection = document.getElementById('student_selection');

    function toggleAudienceFields() {
        const value = targetAudienceSelect.value;

        if (value === 'specific_class') {
            classSelection.style.display = 'block';
            studentSelection.style.display = 'none';
            // Clear student selections
            const studentCheckboxes = document.querySelectorAll('input[name="student_ids[]"]');
            studentCheckboxes.forEach(cb => cb.checked = false);
        } else if (value === 'specific_students') {
            classSelection.style.display = 'none';
            studentSelection.style.display = 'block';
            // Clear class selection
            document.getElementById('class_id').value = '';
        } else {
            classSelection.style.display = 'none';
            studentSelection.style.display = 'none';
        }
    }

    targetAudienceSelect.addEventListener('change', toggleAudienceFields);
    toggleAudienceFields(); // Initial call
});
</script>
@endsection