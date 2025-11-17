@extends('tenant.layouts.app')

@section('title', 'Edit Class')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Edit Class</h1>
        <p class="text-muted mb-0">Update class details.</p>
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
        <form method="POST" action="{{ route('admin.classes.update', $class) }}">
            @method('PUT')
            @include('admin.classes._form', ['class' => $class])
        </form>
    </div>
</div>
@endsection
