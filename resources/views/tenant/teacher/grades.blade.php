@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'Grades')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Grades</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-0">Enter and manage student grades and assessments.</p>
            </div>
        </div>
    </div>
</div>
@endsection
