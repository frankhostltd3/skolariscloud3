@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Timetable</h1>
    <div class="d-flex gap-2">
      @can('manage timetable')
        <a href="{{ route('tenant.academics.timetable.generate') }}" class="btn btn-success">
          <i class="fas fa-magic me-2"></i>Generate Timetable
        </a>
        <a href="{{ route('tenant.academics.timetable.create') }}" class="btn btn-primary">Add entry</a>
      @endcan
    </div>
  </div>

  <form method="get" class="row g-2 mb-3">
    <div class="col-md-3">
      <select name="class_id" class="form-select" onchange="this.form.submit()">
        <option value="">All classes</option>
        @foreach($classes as $c)
          <option value="{{ $c->id }}" @selected(($filters['class_id'] ?? '') == $c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <select name="class_stream_id" class="form-select" onchange="this.form.submit()">
        <option value="">All streams</option>
        @foreach($streams as $s)
          <option value="{{ $s->id }}" @selected(($filters['class_stream_id'] ?? '') == $s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <select name="day_of_week" class="form-select" onchange="this.form.submit()">
        <option value="">All days</option>
        @for($d=1;$d<=7;$d++)
          <option value="{{ $d }}" @selected(($filters['day_of_week'] ?? '') == $d)>{{ \Carbon\Carbon::create()->startOfWeek()->addDays($d-1)->format('l') }}</option>
        @endfor
      </select>
    </div>
    <div class="col-md-3 text-end">
      <a class="btn btn-outline-secondary" href="{{ route('tenant.academics.timetable.index') }}">Reset</a>
    </div>
  </form>

  @can('manage timetable')
  <!-- Bulk Actions Bar -->
  <div id="bulk-actions" class="card mb-3" style="display: none;">
    <div class="card-body">
      <div class="row align-items-center">
        <div class="col-md-6">
          <span id="selected-count" class="text-muted">0 entries selected</span>
        </div>
        <div class="col-md-6 text-end">
          <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="showBulkUpdateModal()">
            <i class="fas fa-edit me-1"></i>Bulk Update
          </button>
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmBulkDelete()">
            <i class="fas fa-trash me-1"></i>Bulk Delete
          </button>
        </div>
      </div>
    </div>
  </div>
  @endcan

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          @can('manage timetable')
          <th width="40">
            <input type="checkbox" id="select-all" class="form-check-input">
          </th>
          @endcan
          <th>Day</th>
          <th>Time</th>
          <th>Class</th>
          <th>Stream</th>
          <th>Subject</th>
          <th>Teacher</th>
          <th>Room</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($entries as $e)
          <tr>
            @can('manage timetable')
            <td>
              <input type="checkbox" class="entry-checkbox form-check-input" value="{{ $e->id }}">
            </td>
            @endcan
            <td>{{ \Carbon\Carbon::create()->startOfWeek()->addDays($e->day_of_week-1)->format('l') }}</td>
            <td>{{ $e->starts_at }} - {{ $e->ends_at }}</td>
            <td>{{ $e->class->name ?? '—' }}</td>
            <td>{{ $e->stream->name ?? '—' }}</td>
            <td>{{ $e->subject->name ?? '—' }}</td>
            <td>{{ optional($e->teacher)->full_name ?? optional($e->teacher)->name ?? '—' }}</td>
            <td>{{ $e->room ?? '—' }}</td>
            <td class="text-end">
              @can('manage timetable')
              <a href="{{ route('tenant.academics.timetable.edit', $e) }}" class="btn btn-sm btn-outline-primary">Edit</a>
              <form action="{{ route('tenant.academics.timetable.destroy', $e) }}" method="post" class="d-inline" onsubmit="return confirm('Delete this entry?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
              @endcan
            </td>
          </tr>
        @empty
          <tr>
            @can('manage timetable')
            <td></td>
            @endcan
            @can('manage timetable')
            <td colspan="8" class="text-center text-muted py-4">No timetable entries found.</td>
            @else
            <td colspan="7" class="text-center text-muted py-4">No timetable entries found.</td>
            @endcan
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{ $entries->links() }}
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const entryCheckboxes = document.querySelectorAll('.entry-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');

    // Handle select all checkbox
    selectAllCheckbox?.addEventListener('change', function() {
        entryCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsVisibility();
    });

    // Handle individual checkboxes
    entryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === entryCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < entryCheckboxes.length;
            updateBulkActionsVisibility();
        });
    });

    function updateBulkActionsVisibility() {
        const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = `${checkedBoxes.length} entries selected`;
        } else {
            bulkActions.style.display = 'none';
        }
    }
});

// Bulk delete confirmation
function confirmBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select entries to delete.');
        return;
    }

    if (confirm(`Are you sure you want to delete ${checkedBoxes.length} timetable entries?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("tenant.academics.timetable.bulkDelete") }}';

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add selected entries
        checkedBoxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'entries[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });

        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}

// Show bulk update modal
function showBulkUpdateModal() {
    const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select entries to update.');
        return;
    }

    // Create modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'bulkUpdateModal';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Update Timetable Entries</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulkUpdateForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="POST">

                        <div class="mb-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select" id="bulkAction" required>
                                <option value="">Select action...</option>
                                <option value="update_room">Update Room</option>
                                <option value="update_teacher">Update Teacher</option>
                                <option value="clear_room">Clear Room</option>
                                <option value="clear_teacher">Clear Teacher</option>
                            </select>
                        </div>

                        <div class="mb-3" id="roomField" style="display: none;">
                            <label class="form-label">Room</label>
                            <input type="text" name="room" class="form-control" placeholder="Enter room name">
                        </div>

                        <div class="mb-3" id="teacherField" style="display: none;">
                            <label class="form-label">Teacher</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">Select teacher...</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name ?? $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Entries</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Add selected entries to form
    const form = modal.querySelector('#bulkUpdateForm');
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'entries[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    // Set form action
    form.action = '{{ route("tenant.academics.timetable.bulkUpdate") }}';

    // Handle action change
    const actionSelect = modal.querySelector('#bulkAction');
    const roomField = modal.querySelector('#roomField');
    const teacherField = modal.querySelector('#teacherField');

    actionSelect.addEventListener('change', function() {
        roomField.style.display = this.value === 'update_room' ? 'block' : 'none';
        teacherField.style.display = this.value === 'update_teacher' ? 'block' : 'none';
    });

    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Clean up when modal is hidden
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}
</script>
@endsection
