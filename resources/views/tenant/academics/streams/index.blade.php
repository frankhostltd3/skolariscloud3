@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-semibold mb-1">{{ __('Class Streams') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.classes.index') }}">{{ __('Classes') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('tenant.academics.classes.show', $class) }}">{{ $class->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Streams') }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkCreateModal">
                <i class="bi bi-lightning me-1"></i>{{ __('Bulk Create') }}
            </button>
            <a class="btn btn-primary" href="{{ route('tenant.academics.streams.create', $class) }}">
                <i class="bi bi-plus-circle me-1"></i>{{ __('Add Stream') }}
            </a>
        </div>
    </div>

    @includeWhen(session('success'), 'partials.toast')
    @includeWhen(session('error'), 'partials.toast')

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($streams->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-diagram-3 text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">{{ __('No Streams Yet') }}</h3>
                    <p class="text-muted mb-4">
                        {{ __('Create streams to divide this class into sections (e.g., A, B, C or East, West, North).') }}
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('tenant.academics.streams.create', $class) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>{{ __('Create First Stream') }}
                        </a>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkCreateModal">
                            <i class="bi bi-lightning me-1"></i>{{ __('Bulk Create Multiple') }}
                        </button>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted d-block">{{ __('Example naming patterns:') }}</small>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark me-1">A, B, C, D</span>
                            <span class="badge bg-light text-dark me-1">1, 2, 3, 4</span>
                            <span class="badge bg-light text-dark me-1">East, West, North, South</span>
                            <span class="badge bg-light text-dark">Red, Blue, Green, Yellow</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Stream Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Capacity') }}</th>
                                <th>{{ __('Enrolled') }}</th>
                                <th>{{ __('Available') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($streams as $stream)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $stream->name }}</div>
                                        @if ($stream->description)
                                            <small class="text-muted">{{ Str::limit($stream->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($stream->code)
                                            <code class="text-muted">{{ $stream->code }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $stream->capacity ?? __('Not set') }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $stream->active_students_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if ($stream->capacity)
                                            @php
                                                $enrolled = $stream->active_students_count ?? 0;
                                                $available = max(0, $stream->capacity - $enrolled);
                                                $percentage = ($enrolled / $stream->capacity) * 100;
                                            @endphp
                                            <span
                                                class="{{ $available <= 0 ? 'text-danger' : ($percentage >= 80 ? 'text-warning' : 'text-success') }}">
                                                {{ $available }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $stream->is_active ? 'bg-success' : 'bg-warning' }}">
                                            {{ $stream->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('tenant.academics.streams.show', [$class, $stream]) }}"
                                                class="btn btn-outline-primary" title="{{ __('View Details') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('tenant.academics.streams.edit', [$class, $stream]) }}"
                                                class="btn btn-outline-secondary" title="{{ __('Edit') }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="confirmDelete({{ $stream->id }})" title="{{ __('Delete') }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-form-{{ $stream->id }}"
                                            action="{{ route('tenant.academics.streams.destroy', [$class, $stream]) }}"
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

                @if ($streams->hasPages())
                    <div class="mt-3">
                        {{ $streams->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Bulk Create Modal --}}
    <div class="modal fade" id="bulkCreateModal" tabindex="-1" aria-labelledby="bulkCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tenant.academics.streams.bulk-create', $class) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="bulkCreateModalLabel">{{ __('Bulk Create Streams') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="pattern" class="form-label">{{ __('Naming Pattern') }}</label>
                            <select class="form-select" id="pattern" name="pattern" required
                                onchange="toggleCustomNames()">
                                <option value="alphabetic">{{ __('Alphabetic (A, B, C, D...)') }}</option>
                                <option value="numeric">{{ __('Numeric (1, 2, 3, 4...)') }}</option>
                                <option value="cardinal">{{ __('Cardinal (East, West, North, South...)') }}</option>
                                <option value="custom">{{ __('Custom Names (comma-separated)') }}</option>
                            </select>
                            <small class="text-muted">{{ __('Choose how to name your streams automatically.') }}</small>
                        </div>

                        <div class="mb-3" id="customNamesGroup" style="display: none;">
                            <label for="custom_names" class="form-label">{{ __('Custom Names') }}</label>
                            <input type="text" class="form-control" id="custom_names" name="custom_names"
                                placeholder="Red, Blue, Green, Yellow">
                            <small class="text-muted">{{ __('Enter stream names separated by commas.') }}</small>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="count" class="form-label">{{ __('Number of Streams') }}</label>
                                <input type="number" class="form-control" id="count" name="count" min="1"
                                    max="26" value="4" required>
                            </div>
                            <div class="col-md-6">
                                <label for="bulk_capacity" class="form-label">{{ __('Capacity (Each)') }}</label>
                                <input type="number" class="form-control" id="bulk_capacity" name="capacity"
                                    min="1" max="500" placeholder="50">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="prefix" class="form-label">{{ __('Prefix (Optional)') }}</label>
                                <input type="text" class="form-control" id="prefix" name="prefix"
                                    placeholder="Class" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label for="suffix" class="form-label">{{ __('Suffix (Optional)') }}</label>
                                <input type="text" class="form-control" id="suffix" name="suffix"
                                    placeholder="Stream" maxlength="10">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="bulk_description" class="form-label">{{ __('Description (Optional)') }}</label>
                            <textarea class="form-control" id="bulk_description" name="description" rows="2" maxlength="500"></textarea>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>{{ __('Streams with duplicate names will be skipped automatically.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-lightning me-1"></i>{{ __('Create Streams') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(streamId) {
            if (confirm('{{ __('Are you sure you want to delete this stream? This action cannot be undone.') }}')) {
                document.getElementById('delete-form-' + streamId).submit();
            }
        }

        function toggleCustomNames() {
            const pattern = document.getElementById('pattern').value;
            const customNamesGroup = document.getElementById('customNamesGroup');
            const customNamesInput = document.getElementById('custom_names');

            if (pattern === 'custom') {
                customNamesGroup.style.display = 'block';
                customNamesInput.required = true;
            } else {
                customNamesGroup.style.display = 'none';
                customNamesInput.required = false;
            }
        }
    </script>
@endpush
