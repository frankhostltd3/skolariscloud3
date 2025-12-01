@extends('layouts.app')

@section('title', $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="display-4 fw-bold mb-4">{{ $page->title }}</h1>

                <div class="content">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
@endsection
