@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'My Assignments')

@section('content')
<div class="container-fluid">
    <h4>My Assignments</h4>
    
    @if(!$student)
        <div class="alert alert-warning">Student not found</div>
    @else
        <div class="row mb-3">
            <div class="col-md-2"><div class="card"><div class="card-body"><h6>Total</h6><h4>{{ $statistics['total'] ?? 0 }}</h4></div></div></div>
            <div class="col-md-2"><div class="card"><div class="card-body"><h6>Pending</h6><h4>{{ $statistics['pending'] ?? 0 }}</h4></div></div></div>
            <div class="col-md-2"><div class="card"><div class="card-body"><h6>Overdue</h6><h4>{{ $statistics['overdue'] ?? 0 }}</h4></div></div></div>
            <div class="col-md-2"><div class="card"><div class="card-body"><h6>Submitted</h6><h4>{{ $statistics['submitted'] ?? 0 }}</h4></div></div></div>
            <div class="col-md-2"><div class="card"><div class="card-body"><h6>Graded</h6><h4>{{ $statistics['graded'] ?? 0 }}</h4></div></div></div>
            <div class="col-md-2"><div class="card"><div class="card-body"><h6>Avg</h6><h4>{{ number_format($statistics['average_score'] ?? 0, 1) }}%</h4></div></div></div>
        </div>
        
        @if($assignments->count() > 0)
            @foreach($assignments as $assignment)
                <div class="card mb-2">
                    <div class="card-body">
                        <h6>{{ $assignment->title }}</h6>
                        <a href="{{ route('tenant.student.assignments.show', $assignment) }}" class="btn btn-sm btn-primary">View</a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">No assignments</div>
        @endif
    @endif
</div>
@endsection

