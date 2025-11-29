@extends('layouts.tenant.student')

@section('title', 'Clearance Required')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-left-danger shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Access Restricted
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    Fee Clearance Required
                                </div>
                                <p class="mt-3">
                                    You have overdue fees that must be cleared before you can access the student portal.
                                </p>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ban fa-4x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4 mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">Overdue Invoices</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Description</th>
                                        <th>Due Date</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($overdueInvoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->feeStructure->name ?? 'Fees' }}</td>
                                            <td class="text-danger">{{ $invoice->due_date->format('d M Y') }}</td>
                                            <td class="font-weight-bold">{{ format_money($invoice->balance) }}</td>
                                            <td>
                                                <a href="{{ route('tenant.student.fees.show', $invoice) }}"
                                                    class="btn btn-sm btn-primary">
                                                    Pay Now
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-muted small">
                        If you believe this is an error, please contact the school administration.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
