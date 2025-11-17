@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h1 class="h4 fw-semibold mb-0">{{ __('Create Expense Category') }}</h1>
    <div class="small text-secondary">{{ __('Add a new category to organize your expenses.') }}</div>
  </div>
  <a href="{{ route('tenant.modules.financials.expense_categories') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Categories') }}
  </a>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="POST" action="{{ route('tenant.modules.financials.expense_categories.store') }}" enctype="multipart/form-data">
          @csrf

          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label">{{ __('Category Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                     id="name" name="name" value="{{ old('name') }}" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="parent_id" class="form-label">{{ __('Parent Category') }}</label>
              <select class="form-select @error('parent_id') is-invalid @enderror"
                      id="parent_id" name="parent_id">
                <option value="">{{ __('Select parent category (optional)') }}</option>
                @foreach($parentCategories as $parent)
                  <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                  </option>
                @endforeach
              </select>
              @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12">
              <label for="description" class="form-label">{{ __('Description') }}</label>
              <textarea class="form-control @error('description') is-invalid @enderror"
                        id="description" name="description" rows="3">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="icon" class="form-label">{{ __('Icon') }}</label>
              <div class="input-group">
                <span class="input-group-text"><i id="iconPreview" class="bi bi-tag"></i></span>
                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                       id="icon" name="icon" value="{{ old('icon', 'bi-tag') }}"
                       placeholder="bi-tag">
                @error('icon')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-text">{{ __('Bootstrap icon class (e.g., bi-tag, bi-cash, bi-building)') }}</div>
            </div>

            <div class="col-md-6">
              <label for="color" class="form-label">{{ __('Color') }}</label>
              <div class="input-group">
                <span class="input-group-text">
                  <span class="badge rounded-pill" id="colorPreview" style="width: 16px; height: 16px; background-color: #007bff;"></span>
                </span>
                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                       id="color" name="color" value="{{ old('color', '#007bff') }}">
                @error('color')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                  {{ __('Active') }}
                </label>
              </div>
              <div class="form-text">{{ __('Inactive categories will not be available for new expenses.') }}</div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('tenant.modules.financials.expense_categories') }}" class="btn btn-outline-secondary">
              {{ __('Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-1"></i>{{ __('Create Category') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const iconInput = document.getElementById('icon');
  const iconPreview = document.getElementById('iconPreview');
  const colorInput = document.getElementById('color');
  const colorPreview = document.getElementById('colorPreview');

  function updateIconPreview() {
    const iconClass = iconInput.value || 'bi-tag';
    iconPreview.className = 'bi ' + iconClass;
  }

  function updateColorPreview() {
    colorPreview.style.backgroundColor = colorInput.value;
  }

  iconInput.addEventListener('input', updateIconPreview);
  colorInput.addEventListener('input', updateColorPreview);

  // Initialize previews
  updateIconPreview();
  updateColorPreview();
});
</script>
@endsection