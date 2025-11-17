@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Assignments')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Assignments</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">Create and manage assignments for your classes.</p>
            </div>
        </div>
    </div>
</div>
@endsection
