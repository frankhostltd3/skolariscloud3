@extends('tenant.layouts.app')

@section('title', __('Tuition Plan Details'))

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
  <div class="card-header fw-semibold">{{ __('Plan Actions') }}</div>
  <div class="card-body">
    <div class="d-grid gap-2">
      <a href="{{ route('tenant.modules.financials.tuition_plans.edit', $tuitionPlan) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-pencil me-1"></i>{{ __('Edit Plan') }}
      </a>
      <button class="btn btn-outline-secondary btn-sm" onclick="duplicatePlan({{ $tuitionPlan->id }})">
        <i class="bi bi-copy me-1"></i>{{ __('Duplicate Plan') }}
      </button>
      <button class="btn btn-outline-danger btn-sm" onclick="deletePlan({{ $tuitionPlan->id }}, '{{ $tuitionPlan->name }}')">
        <i class="bi bi-trash me-1"></i>{{ __('Delete Plan') }}
      </button>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-8">
    <!-- Plan Overview -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $tuitionPlan->name }}</h5>
        <div>
          @if($tuitionPlan->is_active)
            <span class="badge bg-success">{{ __('Active') }}</span>
          @else
            <span class="badge bg-secondary">{{ __('Inactive') }}</span>
          @endif
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p class="mb-2"><strong>{{ __('Academic Year:') }}</strong> {{ $tuitionPlan->academic_year }}</p>
            <p class="mb-2"><strong>{{ __('Grade Level:') }}</strong> {{ $tuitionPlan->grade_level }}</p>
            <p class="mb-2"><strong>{{ __('Currency:') }}</strong> {{ $tuitionPlan->currency->code }} ({{ $tuitionPlan->currency->name }})</p>
          </div>
          <div class="col-md-6">
            <p class="mb-2"><strong>{{ __('Total Amount:') }}</strong> {{ $tuitionPlan->formatted_amount }}</p>
            <p class="mb-2"><strong>{{ __('Installments:') }}</strong> {{ $tuitionPlan->installment_count }}</p>
            <p class="mb-2"><strong>{{ __('Created:') }}</strong> {{ $tuitionPlan->created_at->format('M d, Y') }}</p>
          </div>
        </div>

        @if($tuitionPlan->description)
        <div class="mt-3">
          <strong>{{ __('Description:') }}</strong>
          <p class="mt-1">{{ $tuitionPlan->description }}</p>
        </div>
        @endif
      </div>
    </div>

    <!-- Fee Items -->
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="mb-0">{{ __('Fee Items') }}</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ __('Fee Item') }}</th>
                <th>{{ __('Description') }}</th>
                <th class="text-end">{{ __('Quantity') }}</th>
                <th class="text-end">{{ __('Unit Price') }}</th>
                <th class="text-end">{{ __('Tax') }}</th>
                <th class="text-end">{{ __('Discount') }}</th>
                <th class="text-end">{{ __('Net Amount') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tuitionPlan->items as $item)
              <tr>
                <td>{{ $item->fee->name ?? __('Custom Fee') }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-end">{{ $item->quantity }}</td>
                <td class="text-end">{{ $tuitionPlan->currency->formatAmount($item->unit_price) }}</td>
                <td class="text-end">{{ $tuitionPlan->currency->formatAmount($item->tax_amount) }} ({{ $item->tax_rate }}%)</td>
                <td class="text-end">{{ $tuitionPlan->currency->formatAmount($item->discount_amount) }}</td>
                <td class="text-end fw-bold">{{ $tuitionPlan->currency->formatAmount($item->net_amount) }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="table-light">
                <td colspan="6" class="text-end fw-bold">{{ __('Total:') }}</td>
                <td class="text-end fw-bold">{{ $tuitionPlan->formatted_amount }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- Payment Installments -->
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="mb-0">{{ __('Payment Installments') }}</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ __('Installment') }}</th>
                <th>{{ __('Description') }}</th>
                <th class="text-end">{{ __('Amount') }}</th>
                <th class="text-center">{{ __('Due Date') }}</th>
                <th class="text-center">{{ __('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tuitionPlan->installments->sortBy('installment_number') as $installment)
              <tr>
                <td>
                  <strong>{{ $installment->name }}</strong>
                  <br><small class="text-muted">{{ __('Installment') }} {{ $installment->installment_number }}</small>
                </td>
                <td>{{ $installment->description ?: '-' }}</td>
                <td class="text-end fw-bold">{{ $tuitionPlan->currency->formatAmount($installment->amount) }}</td>
                <td class="text-center">{{ $installment->due_date->format('M d, Y') }}</td>
                <td class="text-center">
                  @if($installment->is_paid)
                    <span class="badge bg-success">{{ __('Paid') }}</span>
                  @else
                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="table-light">
                <td colspan="2" class="text-end fw-bold">{{ __('Total:') }}</td>
                <td class="text-end fw-bold">{{ $tuitionPlan->currency->formatAmount($tuitionPlan->installments->sum('amount')) }}</td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Plan Statistics -->
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="mb-0">{{ __('Plan Statistics') }}</h6>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-6">
            <div class="border rounded p-3 mb-3">
              <div class="h4 mb-1">{{ $tuitionPlan->items->count() }}</div>
              <small class="text-muted">{{ __('Fee Items') }}</small>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded p-3 mb-3">
              <div class="h4 mb-1">{{ $tuitionPlan->installments->count() }}</div>
              <small class="text-muted">{{ __('Installments') }}</small>
            </div>
          </div>
        </div>

        <hr>

        <div class="mb-3">
          <div class="d-flex justify-content-between mb-2">
            <span>{{ __('Subtotal:') }}</span>
            <span>{{ $tuitionPlan->currency->formatAmount($tuitionPlan->items->sum('total_amount')) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>{{ __('Total Tax:') }}</span>
            <span>{{ $tuitionPlan->currency->formatAmount($tuitionPlan->items->sum('tax_amount')) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>{{ __('Total Discount:') }}</span>
            <span>{{ $tuitionPlan->currency->formatAmount($tuitionPlan->items->sum('discount_amount')) }}</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between fw-bold">
            <span>{{ __('Net Total:') }}</span>
            <span>{{ $tuitionPlan->formatted_amount }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0">{{ __('Quick Actions') }}</h6>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a href="{{ route('tenant.modules.financials.tuition_plans.edit', $tuitionPlan) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>{{ __('Edit Plan') }}
          </a>
          <button class="btn btn-outline-secondary" onclick="duplicatePlan({{ $tuitionPlan->id }})">
            <i class="bi bi-copy me-1"></i>{{ __('Duplicate Plan') }}
          </button>
          <a href="{{ route('tenant.modules.financials.tuition_plans') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Plans') }}
          </a>
        </div>
      </div>
    </div>
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
        <p class="text-danger small">{{ __('This action cannot be undone and will remove all associated fee items and installments.') }}</p>
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