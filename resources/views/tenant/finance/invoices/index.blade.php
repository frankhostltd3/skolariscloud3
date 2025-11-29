@extends('tenant.layouts.app')
@section('title', 'Invoices')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Invoices</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('tenant.finance.invoices.bulk-generate') }}" class="btn btn-outline-primary">
                    <i class="bi bi-lightning me-1"></i> Bulk Generate
                </a>
                <a href="{{ route('tenant.finance.invoices.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Create Invoice
                </a>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6>Total Invoices</h6>
                        <h3>{{ $stats['total_invoices'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6>Total Amount</h6>
                        <h3>{{ formatMoney($stats['total_amount']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6>Paid</h6>
                        <h3>{{ formatMoney($stats['paid_amount']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6>Outstanding</h6>
                        <h3>{{ formatMoney($stats['outstanding']) }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($invoices->count() > 0)
                    <form id="bulkSendForm" action="{{ route('tenant.finance.invoices.bulk-send') }}" method="POST">
                        @csrf
                        <!-- Bulk Actions Bar -->
                        <div class="d-flex justify-content-between align-items-center mb-3" id="bulkActionsBar" style="display: none !important;">
                            <div>
                                <input type="checkbox" id="selectAll" class="form-check-input me-2">
                                <label for="selectAll" class="form-check-label">Select All</label>
                                <span id="selectedCount" class="ms-2 text-muted">(0 selected)</span>
                            </div>
                            <div class="btn-group">
                                <button type="submit" name="send_to" value="student" class="btn btn-sm btn-outline-success" disabled id="bulkSendStudent">
                                    <i class="bi bi-person me-1"></i> Send to Students
                                </button>
                                <button type="submit" name="send_to" value="parent" class="btn btn-sm btn-outline-success" disabled id="bulkSendParent">
                                    <i class="bi bi-people me-1"></i> Send to Parents
                                </button>
                                <button type="submit" name="send_to" value="both" class="btn btn-sm btn-success" disabled id="bulkSendBoth">
                                    <i class="bi bi-send me-1"></i> Send to Both
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                        </th>
                                        <th>Invoice #</th>
                                        <th>Student</th>
                                        <th>Fee</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}" class="form-check-input invoice-checkbox">
                                            </td>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->student->name }}</td>
                                            <td>{{ $invoice->feeStructure->fee_name }}</td>
                                            <td>{{ formatMoney($invoice->total_amount) }}</td>
                                            <td>{{ formatMoney($invoice->paid_amount) }}</td>
                                            <td>{{ formatMoney($invoice->balance) }}</td>
                                            <td><span
                                                    class="badge {{ $invoice->status_badge_class }}">{{ ucfirst($invoice->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('tenant.finance.invoices.show', $invoice) }}"
                                                        class="btn btn-info" title="View"><i class="bi bi-eye"></i></a>
                                                    
                                                    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                                                    <a href="{{ route('tenant.finance.invoices.edit', $invoice) }}"
                                                        class="btn btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                                    @endif
                                                    
                                                    <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" 
                                                            data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><h6 class="dropdown-header">Actions</h6></li>
                                                        <li>
                                                            <a href="{{ route('tenant.finance.invoices.print', $invoice) }}" target="_blank" class="dropdown-item">
                                                                <i class="bi bi-printer me-2"></i> Print Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('tenant.finance.invoices.download', $invoice) }}" class="dropdown-item">
                                                                <i class="bi bi-download me-2"></i> Download PDF
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        
                                                        @if($invoice->status !== 'cancelled')
                                                        <li><h6 class="dropdown-header">Send Invoice</h6></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item send-single" 
                                                                    data-invoice-id="{{ $invoice->id }}" data-send-to="student">
                                                                <i class="bi bi-person me-2"></i> Send to Student
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item send-single" 
                                                                    data-invoice-id="{{ $invoice->id }}" data-send-to="parent">
                                                                <i class="bi bi-people me-2"></i> Send to Parent(s)
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item send-single" 
                                                                    data-invoice-id="{{ $invoice->id }}" data-send-to="both">
                                                                <i class="bi bi-send me-2"></i> Send to Both
                                                            </button>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        
                                                        <li><h6 class="dropdown-header">Share</h6></li>
                                                        <li>
                                                            <a href="{{ route('tenant.finance.invoices.share-whatsapp', $invoice) }}" target="_blank" class="dropdown-item">
                                                                <i class="bi bi-whatsapp me-2"></i> Share on WhatsApp
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item" onclick="shareSms({{ $invoice->id }})">
                                                                <i class="bi bi-chat-dots me-2"></i> Share via SMS
                                                            </button>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        @endif
                                                        
                                                        @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                                                        <li>
                                                            <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $invoice->id }}">
                                                                <i class="bi bi-x-circle me-2"></i> Cancel Invoice
                                                            </button>
                                                        </li>
                                                        @endif
                                                        
                                                        @if($invoice->payments()->count() === 0)
                                                        <li>
                                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $invoice->id }}">
                                                                <i class="bi bi-trash me-2"></i> Delete Invoice
                                                            </button>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Cancel and Delete Modals -->
                    @foreach($invoices as $invoice)
                        <!-- Cancel Modal -->
                        @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                        <div class="modal fade" id="cancelModal{{ $invoice->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.finance.invoices.cancel', $invoice) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cancel Invoice #{{ $invoice->invoice_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Reason for Cancellation <span class="text-danger">*</span></label>
                                                <textarea name="cancellation_reason" class="form-control" rows="4" required minlength="10" maxlength="1000" placeholder="Please provide a detailed reason for cancelling this invoice (minimum 10 characters)..."></textarea>
                                                <div class="form-text">Minimum 10 characters required</div>
                                            </div>
                                            <div class="alert alert-warning mb-0">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                This action cannot be undone. The invoice will be marked as cancelled.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-warning">Cancel Invoice</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Delete Modal -->
                        @if($invoice->payments()->count() === 0)
                        <div class="modal fade" id="deleteModal{{ $invoice->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('tenant.finance.invoices.destroy', $invoice) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete Invoice #{{ $invoice->invoice_number }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Reason for Deletion <span class="text-danger">*</span></label>
                                                <textarea name="deletion_reason" class="form-control" rows="4" required minlength="10" maxlength="1000" placeholder="Please provide a detailed reason for deleting this invoice (minimum 10 characters)..."></textarea>
                                                <div class="form-text">Minimum 10 characters required</div>
                                            </div>
                                            <div class="alert alert-danger mb-0">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                <strong>Warning:</strong> This action will permanently delete the invoice. This cannot be undone.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-danger">Delete Invoice</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach

                    {{ $invoices->links() }}
                @else
                    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> No invoices found.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Hidden forms for single sends -->
    <form id="singleSendStudentForm" action="" method="POST" style="display: none;">
        @csrf
    </form>
    <form id="singleSendParentForm" action="" method="POST" style="display: none;">
        @csrf
    </form>
    <form id="singleSendBothForm" action="" method="POST" style="display: none;">
        @csrf
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllHeader = document.getElementById('selectAllHeader');
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.invoice-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    const bulkButtons = document.querySelectorAll('#bulkSendStudent, #bulkSendParent, #bulkSendBoth');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.invoice-checkbox:checked');
        const count = checked.length;
        
        if (count > 0) {
            bulkActionsBar.style.display = 'flex';
            selectedCount.textContent = `(${count} selected)`;
            bulkButtons.forEach(btn => btn.disabled = false);
        } else {
            bulkActionsBar.style.display = 'none';
            bulkButtons.forEach(btn => btn.disabled = true);
        }
        
        selectAllHeader.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
        if (selectAll) selectAll.checked = selectAllHeader.checked;
    }

    selectAllHeader.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        if (selectAll) selectAll.checked = this.checked;
        updateBulkActions();
    });

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            selectAllHeader.checked = this.checked;
            updateBulkActions();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    // Single send buttons
    document.querySelectorAll('.send-single').forEach(btn => {
        btn.addEventListener('click', function() {
            const invoiceId = this.dataset.invoiceId;
            const sendTo = this.dataset.sendTo;
            let form;
            
            switch(sendTo) {
                case 'student':
                    form = document.getElementById('singleSendStudentForm');
                    form.action = `{{ url('tenant/finance/invoices') }}/${invoiceId}/send-student`;
                    break;
                case 'parent':
                    form = document.getElementById('singleSendParentForm');
                    form.action = `{{ url('tenant/finance/invoices') }}/${invoiceId}/send-parent`;
                    break;
                case 'both':
                    form = document.getElementById('singleSendBothForm');
                    form.action = `{{ url('tenant/finance/invoices') }}/${invoiceId}/send-both`;
                    break;
            }
            
            if (form) {
                form.submit();
            }
        });
    });
});

// SMS Share Function
async function shareSms(invoiceId) {
    try {
        const response = await fetch(`{{ url('tenant/finance/invoices') }}/${invoiceId}/share-sms`);
        const data = await response.json();
        
        if (data.success) {
            // Try to open SMS app
            if (navigator.share) {
                // Use Web Share API if available
                await navigator.share({
                    text: data.message
                });
            } else {
                // Fallback: copy to clipboard
                await navigator.clipboard.writeText(data.message);
                alert('SMS message copied to clipboard! You can paste it in your messaging app.');
            }
        }
    } catch (error) {
        console.error('Error sharing SMS:', error);
        alert('Unable to share via SMS. Please try again.');
    }
}
</script>
@endpush
