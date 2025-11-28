@extends('tenant.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h5 fw-semibold mb-0">{{ __('Order') }} #{{ $order->id }}</h1>
        <a class="btn btn-outline-secondary btn-sm"
            href="{{ route('tenant.modules.bookstore.orders.index') }}">{{ __('Back') }}</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">{{ __('Order Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2"><span class="text-secondary small">{{ __('Order Number') }}</span>
                        <div class="fw-semibold">{{ $order->order_number }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Customer') }}</span>
                        <div class="fw-semibold">{{ $order->customer_name }} · {{ $order->customer_email }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Phone') }}</span>
                        <div class="fw-semibold">{{ $order->customer_phone ?? '-' }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Shipping Address') }}</span>
                        <div class="fw-semibold">{{ $order->shipping_address }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Total Amount') }}</span>
                        <div class="fw-semibold">{{ format_money($order->total) }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Status') }}</span>
                        <div class="fw-semibold">{{ ucfirst($order->status) }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Payment Status') }}</span>
                        <div class="fw-semibold">{{ ucfirst($order->payment_status) }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Payment Method') }}</span>
                        <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
                    </div>
                    <div class="mb-2"><span class="text-secondary small">{{ __('Date') }}</span>
                        <div class="fw-semibold">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">{{ __('Order Items') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Item') }}</th>
                                <th class="text-end">{{ __('Qty') }}</th>
                                <th class="text-end">{{ __('Price') }}</th>
                                <th class="text-end">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item->book_title }}</div>
                                        <div class="small text-secondary">{{ $item->book_author }}</div>
                                    </td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ format_money($item->unit_price) }}</td>
                                    <td class="text-end">{{ format_money($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">{{ __('Subtotal') }}</td>
                                <td class="text-end">{{ format_money($order->subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">{{ __('Tax') }}</td>
                                <td class="text-end">{{ format_money($order->tax_amount) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">{{ __('Shipping') }}</td>
                                <td class="text-end">{{ format_money($order->shipping_cost) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">{{ __('Total') }}</td>
                                <td class="text-end fw-bold">{{ format_money($order->total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-semibold mb-3">{{ __('Admin notes') }}</h2>
                    <form method="post" action="{{ route('tenant.modules.bookstore.orders.notes', $order) }}">
                        @csrf
                        <textarea name="admin_notes" class="form-control" rows="6">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                        <div class="mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">{{ __('Save notes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
