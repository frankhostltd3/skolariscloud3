@extends('tenant.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h5 fw-semibold mb-0">{{ __('Orders') }}</h1>
        <a class="btn btn-outline-secondary btn-sm"
            href="{{ route('tenant.modules.bookstore.index') }}">{{ __('Back to Bookstore') }}</a>
    </div>

    <form method="get" class="row g-2 mb-3">
        <div class="col-auto"><input name="q" class="form-control" value="{{ $q }}"
                placeholder="{{ __('Search item, buyer, email') }}"></div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">{{ __('All statuses') }}</option>
                @foreach (['pending', 'paid', 'cancelled'] as $s)
                    <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-outline-secondary" type="submit">{{ __('Filter') }}</button></div>
        <div class="col-auto"><a class="btn btn-outline-secondary"
                href="{{ route('tenant.modules.bookstore.orders.index') }}">{{ __('Reset') }}</a></div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Item') }}</th>
                        <th>{{ __('Buyer') }}</th>
                        <th class="text-end">{{ __('Price') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="text-nowrap">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->order_number }}</div>
                                <div class="small text-secondary">{{ $order->items_count ?? $order->items()->count() }}
                                    items · #{{ $order->id }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->customer_name }}</div>
                                <div class="small text-secondary">{{ $order->customer_email }}</div>
                            </td>
                            <td class="text-end">{{ format_money($order->total) }}</td>
                            <td>
                                @php(
    $badge = match ($order->status) {
        'completed' => 'success',
        'cancelled' => 'secondary',
        'processing' => 'primary',
        default => 'warning',
    },
)
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span>
                                @if ($order->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('tenant.modules.bookstore.orders.show', $order) }}"
                                        class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                                    <form method="post"
                                        action="{{ route('tenant.modules.bookstore.orders.paid', $order) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success"
                                            @disabled($order->payment_status === 'paid')>{{ __('Mark paid') }}</button>
                                    </form>
                                    <form method="post"
                                        action="{{ route('tenant.modules.bookstore.orders.cancel', $order) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary"
                                            @disabled($order->status === 'cancelled')>{{ __('Cancel') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">{{ __('No orders found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
