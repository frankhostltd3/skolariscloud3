@extends('tenant.layouts.app')

@section('title', __('Tuition Plans'))

@section('sidebar')
<div class="card shadow-sm mb-4">
  <div class="card-header fw-semibold">{{ __('Financial Management') }}</div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.overview') }}">
      <span class="bi bi-speedometer2 me-2"></span>{{ __('Financial Overview') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.fees') }}">
      <span class="bi bi-cash-stack me-2"></span>{{ __('Fee Management') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.expenses') }}">
      <span class="bi bi-receipt me-2"></span>{{ __('Expenses') }}
    </a>
    <a class="list-group-item list-group-item-action active" href="{{ route('tenant.modules.financials.tuition_plans') }}">
      <span class="bi bi-file-earmark-text me-2"></span>{{ __('Tuition Plans') }}
    </a>
    <a class="list-group-item list-group-item-action" href="{{ route('tenant.modules.financials.invoices') }}">
      <span class="bi bi-file-earmark-pdf me-2"></span>{{ __('Invoices') }}
    </a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header fw-semibold">{{ __('Plan Management Tips') }}</div>
  <div class="card-body">
    <div class="small text-muted">
      <div class="mb-3">
        <strong>{{ __('Tuition Plans') }}</strong>
        <ul class="mt-2 mb-0">
          <li>{{ __('Bundle multiple fees into structured plans') }}</li>
          <li>{{ __('Set up installment schedules for payments') }}</li>
          <li>{{ __('Apply discounts and taxes per item') }}</li>
          <li>{{ __('Track payment status for each installment') }}</li>
        </ul>
      </div>

      <div class="alert alert-info small p-2">
        <strong>{{ __('Tip:') }}</strong> {{ __('Create different plans for different grade levels and academic years.') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Tuition Plans') }}</h1>
    <div class="small text-secondary">{{ __('Define structured fee plans and installment schedules.') }}</div>
  </div>
  <a href="{{ route('tenant.modules.financials.tuition_plans.create') }}" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-circle me-1"></i>{{ __('Create Plan') }}
  </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h6 fw-semibold mb-0">{{ __('Available Tuition Plans') }}</h2>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ __('Filter') }}</button>
        <button class="btn btn-outline-secondary btn-sm" type="button" disabled>{{ __('Export') }}</button>
      </div>
    </div>

    @if($tuitionPlans->count() > 0)
      <div class="row">
        @foreach($tuitionPlans as $plan)
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h6 class="mb-0">{{ $plan->name }}</h6>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="{{ route('tenant.modules.financials.tuition_plans.show', $plan) }}">
                    <i class="bi bi-eye me-2"></i>{{ __('View') }}
                  </a></li>
                  <li><a class="dropdown-item" href="{{ route('tenant.modules.financials.tuition_plans.edit', $plan) }}">
                    <i class="bi bi-pencil me-2"></i>{{ __('Edit') }}
                  </a></li>
                  <li><a class="dropdown-item" href="#" onclick="duplicatePlan({{ $plan->id }})">
                    <i class="bi bi-copy me-2"></i>{{ __('Duplicate') }}
                  </a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger" href="#" onclick="deletePlan({{ $plan->id }}, '{{ $plan->name }}')">
                    <i class="bi bi-trash me-2"></i>{{ __('Delete') }}
                  </a></li>
                </ul>
              </div>
            </div>
            <div class="card-body">
              <p class="text-muted small mb-2">{{ $plan->description }}</p>
              <div class="row text-center">
                <div class="col-6">
                  <div class="fw-bold">{{ $plan->formatted_amount }}</div>
                  <small class="text-muted">{{ __('Total Amount') }}</small>
                </div>
                <div class="col-6">
                  <div class="fw-bold">{{ $plan->installment_count }}</div>
                  <small class="text-muted">{{ __('Installments') }}</small>
                </div>
              </div>
              <hr>
              <div class="small">
                <div class="mb-1"><strong>{{ __('Grade:') }}</strong> {{ $plan->grade_level }}</div>
                <div class="mb-1"><strong>{{ __('Year:') }}</strong> {{ $plan->academic_year }}</div>
                <div class="mb-1"><strong>{{ __('Status:') }}</strong>
                  @if($plan->is_active)
                    <span class="badge bg-success">{{ __('Active') }}</span>
                  @else
                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                  @endif
                </div>
              </div>
            </div>
            <div class="card-footer">
              <a href="{{ route('tenant.modules.financials.tuition_plans.show', $plan) }}" class="btn btn-sm btn-outline-primary w-100">
                {{ __('View Details') }}
              </a>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="d-flex justify-content-center">
        {{ $tuitionPlans->links() }}
      </div>
    @else
      <div class="text-center py-5">
        <div class="mb-3">
          <i class="bi bi-file-earmark-text display-1 text-muted"></i>
        </div>
        <h5 class="text-muted">{{ __('No Tuition Plans Yet') }}</h5>
        <p class="text-muted">{{ __('Create your first tuition plan to get started with structured fee management.') }}</p>
        <a href="{{ route('tenant.modules.financials.tuition_plans.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i>{{ __('Create First Plan') }}
        </a>
      </div>
    @endif
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Delete Tuition Plan') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>{{ __('Are you sure you want to delete the tuition plan') }} <strong id="planName"></strong>?</p>
        <p class="text-danger small">{{ __('This action cannot be undone.') }}</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <form id="deleteForm" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">{{ __('Delete Plan') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Duplicate Form -->
<form id="duplicateForm" method="POST" style="display: none;">
  @csrf
</form>

<script>
function deletePlan(planId, planName) {
  document.getElementById('planName').textContent = planName;
  document.getElementById('deleteForm').action = '{{ url("modules/financials/tuition-plans") }}/' + planId;
  new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function duplicatePlan(planId) {
  document.getElementById('duplicateForm').action = '{{ url("modules/financials/tuition-plans") }}/' + planId + '/duplicate';
  document.getElementById('duplicateForm').submit();
}
</script>
@endsection