@extends('tenant.layouts.app')

@section('title', 'Edit Room')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Room: {{ $room->name }}</h5>
                        <a href="{{ route('tenant.academics.rooms.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('tenant.academics.rooms.update', $room) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('tenant.academics.rooms._form')

                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Room
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
