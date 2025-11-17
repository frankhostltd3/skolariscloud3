@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-5">
        <div>
            <span class="badge text-bg-primary-subtle text-primary-emphasis px-3 py-2 mb-3">{{ __('Import tenants') }}</span>
            <h1 class="h3 fw-semibold mb-2">{{ __('Bulk import tenant data') }}</h1>
            <p class="text-secondary mb-0">{{ __('Import tenants from Excel files or SQL dumps.') }}</p>
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
                    <form method="POST" action="{{ route('landlord.tenants.import.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="import_type" class="form-label">{{ __('Import Type') }} <span class="text-danger">*</span></label>
                                <select id="import_type" name="import_type" class="form-select @error('import_type') is-invalid @enderror" required>
                                    <option value="">{{ __('Select import type') }}</option>
                                    <option value="excel" {{ old('import_type') === 'excel' ? 'selected' : '' }}>{{ __('Excel/CSV File') }}</option>
                                    <option value="sql" {{ old('import_type') === 'sql' ? 'selected' : '' }}>{{ __('SQL Dump') }}</option>
                                </select>
                                @error('import_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="import_file" class="form-label">{{ __('Import File') }} <span class="text-danger">*</span></label>
                                <input type="file" id="import_file" name="import_file" class="form-control @error('import_file') is-invalid @enderror"
                                       accept=".xlsx,.xls,.csv,.sql" required>
                                @error('import_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">{{ __('Import Guidelines') }}</h6>
                                    <div id="excel-guidelines" class="d-none">
                                        <p class="mb-2">{{ __('Excel/CSV files should have the following columns:') }}</p>
                                        <ul class="mb-0 small">
                                            <li><strong>Tenant ID</strong> - Unique identifier for the tenant</li>
                                            <li><strong>School Name</strong> - Name of the school</li>
                                            <li><strong>Admin Email</strong> - Email for the admin user</li>
                                            <li><strong>Admin Name</strong> - Name of the admin user</li>
                                            <li><strong>Plan</strong> - Subscription plan (starter, growth, premium, enterprise)</li>
                                            <li><strong>Country</strong> - Country code (e.g., KE, US)</li>
                                        </ul>
                                    </div>
                                    <div id="sql-guidelines" class="d-none">
                                        <p class="mb-2">{{ __('SQL files should contain INSERT statements for tenants and domains tables only.') }}</p>
                                        <p class="mb-0 small">{{ __('Warning: SQL import can be dangerous. Only use trusted SQL dumps.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="bi bi-upload me-2"></span>{{ __('Import Tenants') }}
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
                    <h6 class="mb-0">{{ __('Import Templates') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">{{ __('Download sample files to understand the expected format.') }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('landlord.tenants.export.excel') }}" class="btn btn-outline-primary btn-sm">
                            <span class="bi bi-download me-2"></span>{{ __('Download Excel Template') }}
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="downloadSampleSql()">
                            <span class="bi bi-download me-2"></span>{{ __('Download SQL Sample') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-warning-subtle">
                    <h6 class="mb-0 text-warning">{{ __('Important Notes') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><span class="bi bi-exclamation-triangle text-warning me-2"></span>{{ __('Tenant IDs must be unique') }}</li>
                        <li class="mb-2"><span class="bi bi-exclamation-triangle text-warning me-2"></span>{{ __('Admin users will be created with default password') }}</li>
                        <li class="mb-2"><span class="bi bi-exclamation-triangle text-warning me-2"></span>{{ __('SQL imports are potentially dangerous') }}</li>
                        <li><span class="bi bi-exclamation-triangle text-warning me-2"></span>{{ __('Always backup your data before importing') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('import_type').addEventListener('change', function() {
            const excelGuidelines = document.getElementById('excel-guidelines');
            const sqlGuidelines = document.getElementById('sql-guidelines');
            const fileInput = document.getElementById('import_file');

            if (this.value === 'excel') {
                excelGuidelines.classList.remove('d-none');
                sqlGuidelines.classList.add('d-none');
                fileInput.accept = '.xlsx,.xls,.csv';
            } else if (this.value === 'sql') {
                sqlGuidelines.classList.remove('d-none');
                excelGuidelines.classList.add('d-none');
                fileInput.accept = '.sql';
            } else {
                excelGuidelines.classList.add('d-none');
                sqlGuidelines.classList.add('d-none');
                fileInput.accept = '.xlsx,.xls,.csv,.sql';
            }
        });

        function downloadSampleSql() {
            const sampleSql = `-- Sample SQL Import for Tenants
-- Copy and modify this template for your import

INSERT INTO tenants (id, data, created_at, updated_at) VALUES ('sample-school', '{"school_name":"Sample School","admin_email":"admin@sample.com","admin_name":"Admin User","plan":"starter","country":"US"}', NOW(), NOW());

INSERT INTO domains (id, domain, tenant_id, created_at, updated_at) VALUES (NULL, 'sample-school.localhost', 'sample-school', NOW(), NOW());
`;

            const blob = new Blob([sampleSql], { type: 'application/sql' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sample-tenants-import.sql';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
@endsection
