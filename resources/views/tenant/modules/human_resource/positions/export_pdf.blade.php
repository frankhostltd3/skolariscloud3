@extends('tenant.layouts.app')

@section('content')
<div class="container mt-4">
  <h1 class="h4 mb-3">{{ __('Positions List') }}</h1>
  <table class="table table-bordered table-sm">
    <thead>
      <tr>
        <th>{{ __('Title') }}</th>
        <th>{{ __('Department') }}</th>
        <th>{{ __('Code') }}</th>
        <th>{{ __('Description') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($positions as $position)
        <tr>
          <td>{{ $position->title }}</td>
          <td>{{ $position->department ? $position->department->name : '' }}</td>
          <td>{{ $position->code }}</td>
          <td>{{ $position->description }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
