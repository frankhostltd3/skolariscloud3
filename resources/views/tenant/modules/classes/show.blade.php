@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Class Details') }}</h1>
  <div>
    <a class="btn btn-light me-2" href="{{ route('tenant.modules.classes.index') }}">{{ __('Back') }}</a>
    @can('update', $class)
      <a class="btn btn-primary" href="{{ route('tenant.modules.classes.edit', $class) }}">{{ __('Edit') }}</a>
    @endcan
  </div>
</div>

@if (session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card shadow-sm">
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">{{ __('Name') }}</dt>
      <dd class="col-sm-9">{{ $class->name }}</dd>
      <dt class="col-sm-3">{{ __('Created') }}</dt>
      <dd class="col-sm-9">{{ $class->created_at?->format('Y-m-d H:i') }}</dd>
      <dt class="col-sm-3">{{ __('Updated') }}</dt>
      <dd class="col-sm-9">{{ $class->updated_at?->format('Y-m-d H:i') }}</dd>
    </dl>
  </div>
</div>

<div class="mt-4">
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h2 class="h5 mb-0">{{ __('Class Streams') }}</h2>
    @can('create', App\Models\ClassStream::class)
      <a class="btn btn-sm btn-primary" href="{{ route('tenant.modules.class_streams.create', ['class_id' => $class->id]) }}">{{ __('Add Stream') }}</a>
    @endcan
  </div>
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('Stream') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($class->streams as $stream)
            <tr>
              <td>{{ $stream->id }}</td>
              <td>{{ $stream->name }}</td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('tenant.modules.class_streams.show', $stream) }}">{{ __('Show') }}</a>
                @can('update', $stream)
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('tenant.modules.class_streams.edit', $stream) }}">{{ __('Edit') }}</a>
                @endcan
                @can('delete', $stream)
                  <form action="{{ route('tenant.modules.class_streams.destroy', $stream) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Delete this stream?') }}');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('Delete') }}</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center text-muted py-3">{{ __('No streams yet.') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
