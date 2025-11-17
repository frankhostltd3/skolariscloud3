@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Edit Class Stream') }}</h1>
  <a class="btn btn-light" href="{{ route('tenant.modules.class_streams.show', $class_stream) }}">{{ __('Back') }}</a>
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
    <form action="{{ route('tenant.modules.class_streams.update', $class_stream) }}" method="post">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">{{ __('Class') }}</label>
          <select name="class_id" class="form-select @error('class_id') is-invalid @enderror">
            <option value="">{{ __('Select class') }}</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}" @selected(old('class_id', $class_stream->class_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
          @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('Stream Name') }}</label>
          <input type="text" name="name" value="{{ old('name', $class_stream->name) }}" class="form-control @error('name') is-invalid @enderror" />
          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary" type="submit">{{ __('Save changes') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
