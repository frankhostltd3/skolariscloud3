@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Students')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Students</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">View and manage students in your classes.</p>
            </div>
        </div>
    </div>
</div>
@endsection
