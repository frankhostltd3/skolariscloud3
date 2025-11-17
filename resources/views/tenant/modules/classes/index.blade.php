@extends('tenant.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h4 fw-semibold mb-0">{{ __('Classes') }}
    @can('create', App\Models\SchoolClass::class)
      <span class="badge text-bg-success ms-2">{{ __('Can create') }}</span>
    @endcan
    @cannot('create', App\Models\SchoolClass::class)
      <span class="badge text-bg-secondary ms-2">{{ __('Read-only') }}</span>
    @endcannot
  </h1>
  @can('create', App\Models\SchoolClass::class)
    <a class="btn btn-primary" href="{{ route('tenant.modules.classes.create') }}">{{ __('Create Class') }}</a>
  @endcan
</div>
<div class="card shadow-sm">
  <div class="card-body">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form method="get" class="row g-2 mb-3">
      <div class="col-auto">
        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="{{ __('Search class name') }}" />
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
        @if (!empty($q))
          <a class="btn btn-link" href="{{ route('tenant.modules.classes.index') }}">{{ __('Clear') }}</a>
        @endif
      </div>
    </form>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('Name') }}</th>
            <th class="text-end">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($classes as $c)
            <tr>
              <td><a href="{{ route('tenant.modules.classes.show', $c) }}">{{ $c->id }}</a></td>
              <td><a href="{{ route('tenant.modules.classes.show', $c) }}">{{ $c->name }}</a></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('tenant.modules.classes.show', $c) }}">{{ __('Show') }}</a>
                @can('update', $c)
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('tenant.modules.classes.edit', $c) }}">{{ __('Edit') }}</a>
                @endcan
                @can('delete', $c)
                  <form action="{{ route('tenant.modules.classes.destroy', $c) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('Delete this class?') }}');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">{{ __('Delete') }}</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center text-muted py-4">{{ __('No classes yet.') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div>{{ $classes->links() }}</div>
  </div>
</div>
@endsection
