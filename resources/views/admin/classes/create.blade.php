@extends('tenant.layouts.app')

@section('title', 'Create Class')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Create Class</h1>
        <p class="text-muted mb-0">Set up a new class for this school.</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>There were some problems with your input:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.classes.store') }}">
            @include('admin.classes._form', ['class' => new \App\Models\SchoolClass()])
        </form>
    </div>
</div>
@endsection
