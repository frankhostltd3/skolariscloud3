@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container">
  <h1 class="h4 mb-4">Add Salary Scale</h1>
  <form method="POST" action="{{ route('tenant.modules.human_resources.salary_scales.store') }}">
    @csrf
    <div class="mb-3">
      <label for="name" class="form-label">Name</label>
      <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="mb-3">
      <label for="grade" class="form-label">Grade</label>
      <input type="text" class="form-control" id="grade" name="grade">
    </div>
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="min_amount" class="form-label">Minimum Amount</label>
        <input type="number" class="form-control" id="min_amount" name="min_amount" step="0.01">
      </div>
      <div class="col-md-6">
        <label for="max_amount" class="form-label">Maximum Amount</label>
        <input type="number" class="form-control" id="max_amount" name="max_amount" step="0.01">
      </div>
    </div>
    <div class="mb-3">
      <label for="notes" class="form-label">Notes</label>
      <textarea class="form-control" id="notes" name="notes"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
  <a href="{{ route('tenant.modules.human_resources.salary_scales.index') }}" class="btn btn-secondary">Cancel</a>
  </form>
</div>
@endsection
