@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Examination Bodies') }}</h1>
            <p class="text-muted mb-0">{{ __('Manage examination boards like UNEB, Cambridge, KNEC, etc.') }}</p>
        </div>
        <a class="btn btn-primary" href="{{ route('tenant.academics.examination-bodies.create') }}">
            <i class="bi bi-plus-circle me-1"></i>{{ __('Add Examination Body') }}
        </a>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($examinationBodies->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-check text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">{{ __('No Examination Bodies Yet') }}</h3>
                    <p class="text-muted mb-4">{{ __('Add examination bodies that your school is affiliated with.') }}</p>
                    <a href="{{ route('tenant.academics.examination-bodies.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>{{ __('Add Examination Body') }}
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($examinationBodies as $body)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $body->name }}</div>
                                        @if ($body->website)
                                            <small><a href="{{ $body->website }}" target="_blank"
                                                    class="text-muted">{{ $body->website }}</a></small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($body->code)
                                            <code class="text-muted">{{ $body->code }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($body->country)
                                            {{ $body->country->full_name }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($body->is_international)
                                            <span class="badge bg-info">{{ __('International') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('National') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $body->is_active ? 'bg-success' : 'bg-warning' }}">
                                            {{ $body->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('tenant.academics.examination-bodies.show', $body) }}"
                                                class="btn btn-outline-primary" title="{{ __('View Details') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.academics.examination-bodies.edit', $body) }}"
                                                class="btn btn-outline-secondary" title="{{ __('Edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $body->id }})" title="{{ __('Delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $body->id }}"
                                            action="{{ route('tenant.academics.examination-bodies.destroy', $body) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($examinationBodies->hasPages())
                    <div class="mt-3">
                        {{ $examinationBodies->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(bodyId) {
            if (confirm('{{ __('Are you sure you want to delete this examination body?') }}')) {
                document.getElementById('delete-form-' + bodyId).submit();
            }
        }
    </script>
@endpush
