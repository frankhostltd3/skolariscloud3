@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Create a new class stream') }}</h1>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form action="{{ route('tenant.modules.class_streams.store') }}" method="post">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">{{ __('Class') }}</label>
          <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
            <option value="">-- {{ __('Select class') }} --</option>
            @php($prefill = old('class_id', request('class_id')))
            @foreach($classes as $class)
              <option value="{{ $class->id }}" @selected($prefill==$class->id)>{{ $class->name }}</option>
            @endforeach
          </select>
          @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Stream name') }}</label>
          <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="North" />
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
