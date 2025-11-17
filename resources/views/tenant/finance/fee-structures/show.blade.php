@extends('tenant.layouts.app')
@section('title', 'Fee Structure Details')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Fee Structure Details</h1>
            <div>
                <a href="{{ route('tenant.finance.fee-structures.edit', $feeStructure) }}" class="btn btn-warning"><i
                        class="bi bi-pencil me-1"></i> Edit</a>
                <a href="{{ route('tenant.finance.fee-structures.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Basic Information</h5>
                        <table class="table">
                            <tr>
                                <th width="30%">Fee Name:</th>
                                <td>{{ $feeStructure->fee_name }}</td>
                            </tr>
                            <tr>
                                <th>Fee Type:</th>
                                <td>{{ ucfirst($feeStructure->fee_type) }}</td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td>{{ formatMoney($feeStructure->amount) }}</td>
                            </tr>
                            <tr>
                                <th>Academic Year:</th>
                                <td>{{ $feeStructure->academic_year }}</td>
                            </tr>
                            <tr>
                                <th>Term:</th>
                                <td>{{ $feeStructure->term ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Due Date:</th>
                                <td>{{ $feeStructure->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><span
                                        class="badge {{ $feeStructure->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $feeStructure->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $feeStructure->description ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Statistics</h5>
                        <div class="mb-3"><strong>Total Invoices:</strong> {{ $stats['total_invoices'] }}</div>
                        <div class="mb-3"><strong>Total Amount:</strong> {{ formatMoney($stats['total_amount']) }}</div>
                        <div class="mb-3"><strong>Paid Amount:</strong> {{ formatMoney($stats['paid_amount']) }}</div>
                        <div><strong>Outstanding:</strong> {{ formatMoney($stats['outstanding']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
