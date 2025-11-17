@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container py-3">
  <h1 class="h4 mb-3">Edit timetable entry</h1>
  <form method="post" action="{{ route('tenant.academics.timetable.update', $entry) }}" class="card card-body">
    @csrf
    @method('PUT')
    @include('tenant.academics.timetable._form', ['entry' => $entry])
    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-primary">Update</button>
      <a href="{{ route('tenant.academics.timetable.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
