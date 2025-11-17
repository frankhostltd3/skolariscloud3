@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Countries') }}</h1>
            <p class="text-muted mb-0">{{ __('Manage country information and settings.') }}</p>
        </div>
        <a class="btn btn-primary" href="{{ url('/tenant/academics/countries/create') }}"><i
                class="bi bi-plus-circle me-1"></i>{{ __('Add Country') }}</a>
    </div>
    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')
    <div class="card shadow-sm">
        <div class="card-body">
            @if ($countries->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-globe text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">{{ __('No Countries Yet') }}</h3>
                    <p class="text-muted mb-4">{{ __('Add countries to associate with examination bodies and schools.') }}
                    </p>
                    <a href="{{ url('/tenant/academics/countries/create') }}" class="btn btn-primary"><i
                            class="bi bi-plus-circle me-1"></i>{{ __('Create First Country') }}</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('ISO Code') }}</th>
                                <th>{{ __('Phone Code') }}</th>
                                <th>{{ __('Currency') }}</th>
                                <th>{{ __('Exam Bodies') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($countries as $country)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $country->full_name }}</div>
                                    </td>
                                    <td><code>{{ $country->iso_code_2 }}</code> / <code>{{ $country->iso_code_3 }}</code>
                                    </td>
                                    <td>{{ $country->phone_code ?? '-' }}</td>
                                    <td>{{ $country->currency_symbol ?? '-' }} ({{ $country->currency_code ?? '-' }})</td>
                                    <td><span class="badge bg-secondary">{{ $country->examination_bodies_count }}</span>
                                    </td>
                                    <td><span
                                            class="badge {{ $country->is_active ? 'bg-success' : 'bg-warning' }}">{{ $country->is_active ? __('Active') : __('Inactive') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('tenant.academics.countries.show', $country) }}"
                                                class="btn btn-outline-primary" title="{{ __('View') }}"><i
                                                    class="bi bi-eye"></i></a>
                                            <a href="{{ route('tenant.academics.countries.edit', $country) }}"
                                                class="btn btn-outline-secondary" title="{{ __('Edit') }}"><i
                                                    class="bi bi-pencil"></i></a>
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $country->id }})"
                                                title="{{ __('Delete') }}"><i class="bi bi-trash"></i></button>
                                        </div>
                                        <form id="delete-form-{{ $country->id }}"
                                            action="{{ route('tenant.academics.countries.destroy', $country) }}"
                                            method="POST" class="d-none">@csrf @method('DELETE')</form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($countries->hasPages())
                    <div class="mt-3">{{ $countries->links() }}</div>
                @endif
            @endif
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function confirmDelete(countryId) {
            if (confirm('{{ __('Are you sure you want to delete this country?') }}')) {
                document.getElementById('delete-form-' + countryId).submit();
            }
        }
    </script>
@endpush

