@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
        <div>
            <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Edit tenant') }}</span>
            <h1 class="h3 fw-semibold mb-2">{{ __('Update tenant information') }}</h1>
            <p class="text-secondary mb-0">{{ __('Modify tenant details and admin information.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('landlord.tenants.index') }}" class="btn btn-outline-secondary btn-sm">
                <span class="bi bi-arrow-left me-2"></span>{{ __('Back to tenants') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('landlord.tenants.update', $tenant) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="school_name" class="form-label">{{ __('School Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="school_name" name="school_name" class="form-control @error('school_name') is-invalid @enderror"
                                       value="{{ old('school_name', $school_name) }}" required>
                                @error('school_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="admin_name" class="form-label">{{ __('Admin Name') }} <span class="text-danger">*</span></label>
                                <input type="text" id="admin_name" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror"
                                       value="{{ old('admin_name', $admin_name) }}" required>
                                @error('admin_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="admin_email" class="form-label">{{ __('Admin Email') }} <span class="text-danger">*</span></label>
                                <input type="email" id="admin_email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror"
                                       value="{{ old('admin_email', $admin_email) }}" required>
                                @error('admin_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">{{ __('Contact Email (billing/support)') }}</label>
                                <input type="email" id="contact_email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror"
                                       value="{{ old('contact_email', $contact_email) }}">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="phones" class="form-label">{{ __('Phone numbers (comma-separated)') }}</label>
                                <input type="text" id="phones" name="phones" class="form-control @error('phones') is-invalid @enderror"
                                       value="{{ old('phones', $phones) }}" placeholder="{{ __('e.g., +254700000000, +254711111111') }}">
                                @error('phones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="bi bi-check-lg me-2"></span>{{ __('Update Tenant') }}
                                    </button>
                                    <a href="{{ route('landlord.tenants.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">{{ __('Tenant Information') }}</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">{{ __('Tenant ID') }}</dt>
                        <dd class="col-sm-7">{{ $tenant->id }}</dd>

                        <dt class="col-sm-5">{{ __('Created') }}</dt>
                        <dd class="col-sm-7">{{ $tenant->created_at->format('M j, Y') }}</dd>

                        <dt class="col-sm-5">{{ __('Database') }}</dt>
                        <dd class="col-sm-7">
                            <code class="small">tenant{{ $tenant->id }}.sqlite</code>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-danger-subtle">
                    <h6 class="mb-0 text-danger">{{ __('Danger Zone') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ __('Deleting this tenant will permanently remove all associated data, including the database and domain.') }}</p>
                    <form method="POST" action="{{ route('landlord.tenants.destroy', $tenant) }}"
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this tenant? This action cannot be undone.') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <span class="bi bi-trash me-2"></span>{{ __('Delete Tenant') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection