@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.academics.partials.sidebar')
@endsection

@section('content')
    <div class="container py-3">
        <h1 class="h4 mb-3">Add timetable entry</h1>
        <form method="post" action="{{ route('tenant.academics.timetable.store') }}" class="card card-body">
            @csrf
            @include('tenant.academics.timetable._form')
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('tenant.academics.timetable.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
