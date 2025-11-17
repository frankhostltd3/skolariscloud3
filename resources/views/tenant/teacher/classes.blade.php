@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'My Classes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-school me-2"></i>My Classes</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">Manage your assigned classes and view student information.</p>
            </div>
        </div>
    </div>
</div>
@endsection
