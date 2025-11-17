@extends('tenant.layouts.app')
@section('title', 'Fee Structures')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Fee Structures</h1>
            <a href="{{ route('tenant.finance.fee-structures.create') }}" class="btn btn-primary"><i
                    class="bi bi-plus-circle me-1"></i> Add Fee Structure</a>
        </div>
        <div class="card">
            <div class="card-body">
                @if ($feeStructures->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fee Name</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Academic Year</th>
                                    <th>Term</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feeStructures as $fee)
                                    <tr>
                                        <td>{{ $fee->fee_name }}</td>
                                        <td>{{ ucfirst($fee->fee_type) }}</td>
                                        <td>{{ formatMoney($fee->amount) }}</td>
                                        <td>{{ $fee->academic_year }}</td>
                                        <td>{{ $fee->term ?? '-' }}</td>
                                        <td><span
                                                class="badge {{ $fee->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $fee->is_active ? 'Active' : 'Inactive' }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tenant.finance.fee-structures.show', $fee) }}"
                                                class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('tenant.finance.fee-structures.edit', $fee) }}"
                                                class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $feeStructures->links() }}
                @else
                    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i> No fee structures found. <a
                            href="{{ route('tenant.finance.fee-structures.create') }}">Add one now</a>.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
