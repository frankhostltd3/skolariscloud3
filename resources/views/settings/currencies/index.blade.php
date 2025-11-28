@extends('tenant.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
                <div>
                    <h1 class="h4 fw-semibold mb-1">Currency Management</h1>
                    <p class="text-muted mb-0">Manage currencies and exchange rates for payment processing.</p>
                </div>
                <div class="mt-3 mt-lg-0 d-flex gap-2">
                    <form method="POST" action="{{ route('tenant.settings.admin.currencies.update-rates') }}"
                        class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-arrow-repeat me-2"></i>Update Exchange Rates
                        </button>
                    </form>
                    <a href="{{ route('tenant.settings.admin.currencies.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Currency
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    @if ($currencies->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-currency-exchange text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 fw-semibold">No Currencies Found</h5>
                            <p class="text-muted">Add your first currency to get started.</p>
                            <a href="{{ route('tenant.settings.admin.currencies.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Add Currency
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="border-0">Currency</th>
                                        <th scope="col" class="border-0">Code</th>
                                        <th scope="col" class="border-0">Symbol</th>
                                        <th scope="col" class="border-0">Exchange Rate</th>
                                        <th scope="col" class="border-0">Status</th>
                                        <th scope="col" class="border-0">Auto-Update</th>
                                        <th scope="col" class="border-0">Last Updated</th>
                                        <th scope="col" class="border-0 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($currencies as $currency)
                                        <tr>
                                            <td class="fw-medium">
                                                {{ $currency->name }}
                                                @if ($currency->is_default)
                                                    <span class="badge bg-primary ms-2">Default</span>
                                                @endif
                                            </td>
                                            <td>
                                                <code class="text-secondary">{{ $currency->code }}</code>
                                            </td>
                                            <td>{{ $currency->symbol }}</td>
                                            <td>
                                                <span
                                                    class="text-nowrap">{{ number_format($currency->exchange_rate, 6) }}</span>
                                            </td>
                                            <td>
                                                <form method="POST"
                                                    action="{{ route('tenant.settings.admin.currencies.toggle-active', $currency) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-link p-0 text-decoration-none"
                                                        @if ($currency->is_default) disabled title="Cannot deactivate default currency" @endif>
                                                        @if ($currency->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                @if ($currency->code !== 'USD')
                                                    <form method="POST"
                                                        action="{{ route('tenant.settings.admin.currencies.toggle-auto-update', $currency) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-link p-0 text-decoration-none"
                                                            title="Toggle auto-update">
                                                            @if ($currency->auto_update_enabled)
                                                                <span class="badge bg-info">Enabled</span>
                                                            @else
                                                                <span class="badge bg-secondary">Manual</span>
                                                            @endif
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="badge bg-light text-dark">Base</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($currency->last_updated_at)
                                                    <small
                                                        class="text-muted">{{ $currency->last_updated_at->diffForHumans() }}</small>
                                                @else
                                                    <small class="text-muted">Never</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @if (!$currency->is_default)
                                                        <form method="POST"
                                                            action="{{ route('tenant.settings.admin.currencies.set-default', $currency) }}"
                                                            class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-primary"
                                                                title="Set as Default">
                                                                <i class="bi bi-star"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('tenant.settings.admin.currencies.edit', $currency) }}"
                                                        class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    @if (!$currency->is_default)
                                                        <form method="POST"
                                                            action="{{ route('tenant.settings.admin.currencies.destroy', $currency) }}"
                                                            class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this currency?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger"
                                                                title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            @if ($currencies->isNotEmpty())
                <div class="alert alert-info mt-4 d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle-fill mt-1"></i>
                    <div>
                        <strong>Exchange Rate Reference:</strong> All exchange rates are relative to USD (1.0). For
                        example, if 1 USD = 3700 UGX, set the UGX exchange rate to 3700.
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
