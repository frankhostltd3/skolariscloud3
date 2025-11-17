@extends('tenant.layouts.app')
@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-0">{{ __('Grading Systems') }}</h1>
            <p class="text-muted small mt-1">
                {{ __('Manage grading schemes for different countries and examination bodies') }}</p>
        </div>
        <div><a class="btn btn-primary"
                href="{{ route('tenant.academics.grading_schemes.create') }}">{{ __('Add Grading System') }}</a><a
                class="btn btn-outline-secondary ms-2" href="{{ route('tenant.academics.grading_schemes.export_all') }}"
                title="{{ __('Export All') }}"><i class="bi bi-download"></i> {{ __('Export All') }}</a></div>
    </div>
    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')
    @includeWhen(session('info'), 'partials.toast')
    @if ($items->isEmpty() && !request('q'))
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-3"><i class="bi bi-award-fill text-muted" style="font-size: 3rem;"></i></div>
                <h5 class="text-muted">{{ __('No grading systems yet') }}</h5>
                <p class="text-muted mb-4">
                    {{ __('Get started by creating your first grading scheme or using one of our international templates.') }}
                </p><a class="btn btn-primary"
                    href="{{ route('tenant.academics.grading_schemes.create') }}">{{ __('Create Grading System') }}</a>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="get" class="row g-2 mb-3">
                    <div class="col-md-6"><input type="text" name="q" value="{{ $q ?? '' }}"
                            class="form-control" placeholder="{{ __('Search by name') }}" /></div>
                    <div class="col-md-6 text-end"><button class="btn btn-outline-secondary">{{ __('Search') }}</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Exam Body') }}</th>
                                <th>{{ __('Bands') }}</th>
                                <th>{{ __('Current') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $it)
                                <tr>
                                    <td>{{ $it->id }}</td>
                                    <td><a href="{{ route('tenant.academics.grading_schemes.show', $it) }}"
                                            class="text-decoration-none">{{ $it->name }}</a>
                                        @if ($it->is_current)
                                            <span class="badge bg-success ms-1">{{ __('Active') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $it->country ?? '—' }}</td>
                                    <td>{{ optional($it->examinationBody)->code ?? '—' }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $it->bands_count }}</span></td>
                                    <td>
                                        @if ($it->is_current)
                                        <span class="badge bg-success">{{ __('Yes') }}</span>@else<span
                                                class="text-muted">{{ __('No') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ route('tenant.academics.grading_schemes.edit', $it) }}"
                                            title="{{ __('Edit') }}"><i class="bi bi-pencil"></i></a>
                                        <form method="post"
                                            action="{{ route('tenant.academics.grading_schemes.destroy', $it) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('{{ __('Delete this grading system?') }}');">@csrf
                                            @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit"
                                                title="{{ __('Delete') }}"><i class="bi bi-trash"></i></button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('No grading systems found matching your search.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $items->links() }}
            </div>
        </div>
    @endif
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('International Grading Systems') }}</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">{{ __('Common grading systems from around the world:') }}</p>
                    <ul class="small mb-0">
                        <li><strong>UK:</strong> A-Level (A*-U), GCSE (9-1)</li>
                        <li><strong>US:</strong> GPA Scale (A-F, 4.0 system)</li>
                        <li><strong>Kenya:</strong> KCSE (A-E with variants)</li>
                        <li><strong>Nigeria:</strong> WAEC (A1-F9)</li>
                        <li><strong>South Africa:</strong> NSC (1-7)</li>
                        <li><strong>India:</strong> CBSE (A1-E2)</li>
                        <li><strong>Australia:</strong> ATAR (99.95-10)</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('How Grading Works') }}</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">{{ __('Grades are automatically assigned based on score ranges:') }}
                    </p>
                    <ul class="small mb-0">
                        <li>{{ __('Each grading band has a minimum and maximum score') }}</li>
                        <li>{{ __('Bands cannot overlap within the same scheme') }}</li>
                        <li>{{ __('Students receive the band that matches their score') }}</li>
                        <li>{{ __('One scheme can be marked as "current" for automatic grading') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
