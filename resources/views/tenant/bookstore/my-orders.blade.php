@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4">My Orders</h2>

        @if ($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="py-3 ps-4">Order #</th>
                            <th scope="col" class="py-3">Date</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3">Total</th>
                            <th scope="col" class="py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge_color }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td>{{ format_money($order->total) }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('tenant.bookstore.order.show', $order) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5 bg-light rounded">
                <i class="bi bi-bag-x text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No orders found</h4>
                <p class="text-muted">You haven't placed any orders yet.</p>
                <a href="{{ route('tenant.bookstore.index') }}" class="btn btn-primary mt-2">Start Shopping</a>
            </div>
        @endif
    </div>
@endsection
