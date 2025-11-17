@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Leave types') }}</h1>
    <div class="small text-secondary">{{ __('Define allowance, approval flows, and carry-over rules.') }}</div>
  </div>
  <a href="{{ route('tenant.modules.human_resources.leave_types.create') }}" class="btn btn-primary btn-sm">{{ __('Add leave type') }}</a>
</div>

<!-- Search and Filters -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-6">
        <label for="search" class="form-label">{{ __('Search') }}</label>
        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name, code, or description') }}">
      </div>
      <div class="col-md-6 d-flex align-items-end">
        <button type="submit" class="btn btn-outline-primary me-2">{{ __('Search') }}</button>
        <a href="{{ route('tenant.modules.human_resources.leave_types.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
      </div>
    </form>
  </div>
</div>

<!-- Import/Export Buttons -->
<div class="mb-3">
  <a href="{{ route('tenant.modules.human_resources.leave_types.exportTemplate') }}" class="btn btn-info btn-sm">{{ __('Download Excel Template') }}</a>
  <a href="{{ route('tenant.modules.human_resources.leave_types.exportSqlTemplate') }}" class="btn btn-outline-info btn-sm">{{ __('Download SQL Template') }}</a>
  <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">{{ __('Import Excel/CSV/SQL') }}</button>
  <a href="{{ route('tenant.modules.human_resources.leave_types.export', ['format' => 'excel']) }}" class="btn btn-warning btn-sm">{{ __('Export Excel') }}</a>
  <a href="{{ route('tenant.modules.human_resources.leave_types.export', ['format' => 'pdf']) }}" class="btn btn-danger btn-sm">{{ __('Export PDF') }}</a>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">{{ __('Import Leave Types') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">{{ __('Import Format') }}</label>
          <div class="mb-3">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="importFormat" id="formatExcel" value="excel" checked>
              <label class="form-check-label" for="formatExcel">
                Excel/CSV
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="importFormat" id="formatSql" value="sql">
              <label class="form-check-label" for="formatSql">
                SQL
              </label>
            </div>
          </div>
        </div>

        <div id="excelImportForm">
          <form method="POST" action="{{ route('tenant.modules.human_resources.leave_types.import', 'excel') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="excelFile" class="form-label">{{ __('Select Excel or CSV file') }}</label>
              <input type="file" class="form-control" id="excelFile" name="file" accept=".xlsx,.xls,.csv" required>
              <div class="form-text">{{ __('Upload an Excel or CSV file with leave type data') }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
              <button type="submit" class="btn btn-primary">{{ __('Import Excel/CSV') }}</button>
            </div>
          </form>
        </div>

        <div id="sqlImportForm" style="display: none;">
          <form method="POST" action="{{ route('tenant.modules.human_resources.leave_types.import', 'sql') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="sqlFile" class="form-label">{{ __('Select SQL file') }}</label>
              <input type="file" class="form-control" id="sqlFile" name="file" accept=".sql" required>
              <div class="form-text">
                {{ __('Upload a SQL file containing INSERT statements for the leave_types table') }}<br>
                <small class="text-muted">{{ __('Example: INSERT INTO leave_types (name, code, default_days, requires_approval, description) VALUES (\'Annual Leave\', \'AL\', 25, 1, \'Annual leave allowance\');') }}</small>
              </div>
            </div>
            <div class="alert alert-warning">
              <strong>{{ __('Security Note:') }}</strong> {{ __('Only INSERT statements for the leave_types table will be processed. All other SQL statements will be ignored.') }}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
              <button type="submit" class="btn btn-primary">{{ __('Import SQL') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatRadios = document.querySelectorAll('input[name="importFormat"]');
    const excelForm = document.getElementById('excelImportForm');
    const sqlForm = document.getElementById('sqlImportForm');

    formatRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'excel') {
                excelForm.style.display = 'block';
                sqlForm.style.display = 'none';
            } else if (this.value === 'sql') {
                excelForm.style.display = 'none';
                sqlForm.style.display = 'block';
            }
        });
    });
});
</script>

<!-- Search and Filters -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-6">
        <label for="search" class="form-label">{{ __('Search') }}</label>
        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by name, code, or description') }}">
      </div>
      <div class="col-md-6 d-flex align-items-end">
        <button type="submit" class="btn btn-outline-primary me-2">{{ __('Search') }}</button>
        <a href="{{ route('tenant.modules.human_resources.leave_types.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-sm align-middle">
      <thead>
        <tr>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Code') }}</th>
          <th class="text-center">{{ __('Default Days') }}</th>
          <th class="text-center">{{ __('Requires Approval') }}</th>
          <th>{{ __('Description') }}</th>
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($leaveTypes as $leaveType)
          <tr>
            <td>{{ $leaveType->name }}</td>
            <td><code>{{ $leaveType->code }}</code></td>
            <td class="text-center">{{ $leaveType->default_days }}</td>
            <td class="text-center">
              @if($leaveType->requires_approval)
                <span class="badge bg-success">{{ __('Yes') }}</span>
              @else
                <span class="badge bg-secondary">{{ __('No') }}</span>
              @endif
            </td>
            <td>{{ Str::limit($leaveType->description, 50) }}</td>
            <td>
              <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('tenant.modules.human_resources.leave_types.show', $leaveType) }}" class="btn btn-outline-info" title="{{ __('View') }}">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('tenant.modules.human_resources.leave_types.edit', $leaveType) }}" class="btn btn-outline-warning" title="{{ __('Edit') }}">
                  <i class="bi bi-pencil"></i>
                </a>
                <form method="POST" action="{{ route('tenant.modules.human_resources.leave_types.destroy', $leaveType) }}" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure?') }}')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-secondary">{{ __('No leave types configured yet') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
