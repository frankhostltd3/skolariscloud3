@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Class Streams') }}
    @can('create', App\Models\ClassStream::class)
      <span class="badge text-bg-success ms-2">{{ __('Can create') }}</span>
    @endcan
    @cannot('create', App\Models\ClassStream::class)
      <span class="badge text-bg-secondary ms-2">{{ __('Read-only') }}</span>
    @endcannot
  </h1>
  @can('create', App\Models\ClassStream::class)
    <a class="btn btn-primary" href="{{ route('tenant.modules.class_streams.create') }}">{{ __('Create Class Stream') }}</a>
  @endcan
</div>
<div class="card shadow-sm">
  <div class="card-body">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form method="get" class="row g-2 mb-3">
      <div class="col-auto">
        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="{{ __('Search stream or class') }}" />
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
        @if (!empty($q))
          <a class="btn btn-link" href="{{ route('tenant.modules.class_streams.index') }}">{{ __('Clear') }}</a>
        @endif
      </div>
    </form>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('Class') }}</th>
            <th>{{ __('Stream') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($streams as $s)
            <tr>
              <td><a href="{{ route('tenant.modules.class_streams.show', $s) }}">{{ $s->id }}</a></td>
              <td>{{ optional($s->class)->name }}</td>
              <td><a href="{{ route('tenant.modules.class_streams.show', $s) }}">{{ $s->name }}</a></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('tenant.modules.class_streams.show', $s) }}">{{ __('Show') }}</a>
                @can('update', $s)
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('tenant.modules.class_streams.edit', $s) }}">{{ __('Edit') }}</a>
                @endcan
                @can('delete', $s)
                  <form action="{{ route('tenant.modules.class_streams.destroy', $s) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Delete this class stream?') }}');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('Delete') }}</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">{{ __('No class streams yet.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div>{{ $streams->links() }}</div>
  </div>
</div>
@endsection
