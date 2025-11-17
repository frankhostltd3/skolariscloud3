@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Subjects')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Subjects</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">View all your enrolled subjects and course information.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
