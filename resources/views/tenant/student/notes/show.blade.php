@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', $note->title)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('tenant.student.notes.index') }}">{{ __('Notes') }}</a>
            </li>
            <li class="breadcrumb-item active">{{ $note->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-{{ $note->type == 'document' ? 'primary' : ($note->type == 'video' ? 'danger' : 'info') }} mb-2">
                                <i class="bi bi-{{ $note->type == 'document' ? 'file-text' : ($note->type == 'video' ? 'play-circle' : 'link-45deg') }} me-1"></i>
                                {{ $note->type_label }}
                            </span>
                            <h4 class="mb-2">{{ $note->title }}</h4>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($note->description)
                        <div class="mb-4">
                            <h6 class="text-muted">{{ __('Description') }}</h6>
                            <p>{{ $note->description }}</p>
                        </div>
                    @endif

                    <!-- Content Display -->
                    @if($note->type == 'youtube' && $note->youtube_embed_url)
                        <div class="ratio ratio-16x9 mb-4">
                            <iframe src="{{ $note->youtube_embed_url }}" 
                                    allowfullscreen
                                    class="rounded"></iframe>
                        </div>
                    @elseif($note->type == 'video' && $note->file_path)
                        <div class="ratio ratio-16x9 mb-4">
                            <video controls class="rounded">
                                <source src="{{ $note->file_url }}" type="{{ $note->file_mime }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @elseif($note->type == 'link' && $note->external_url)
                        <div class="alert alert-info">
                            <i class="bi bi-link-45deg me-2"></i>
                            <strong>{{ __('External Link:') }}</strong>
                            <a href="{{ $note->external_url }}" target="_blank" class="alert-link">
                                {{ $note->external_url }}
                            </a>
                        </div>
                    @elseif($note->type == 'document' && $note->file_path)
                        <div class="alert alert-secondary">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            <strong>{{ __('Document available for download') }}</strong>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        @if($note->is_downloadable && $note->file_path)
                            <a href="{{ route('tenant.student.notes.download', $note->id) }}" 
                               class="btn btn-success">
                                <i class="bi bi-download me-2"></i>{{ __('Download') }}
                            </a>
                        @endif

                        @if($note->external_url)
                            <a href="{{ $note->external_url }}" 
                               target="_blank" 
                               class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right me-2"></i>{{ __('Open Link') }}
                            </a>
                        @endif

                        <a href="{{ route('tenant.student.notes.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Notes') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Material Info -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-light border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>{{ __('Material Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted"><i class="bi bi-book me-2"></i>{{ __('Subject') }}</td>
                            <td class="text-end">
                                <strong>{{ $note->subject->name ?? 'N/A' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="bi bi-person me-2"></i>{{ __('Teacher') }}</td>
                            <td class="text-end">
                                <strong>{{ $note->teacher->name ?? 'N/A' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="bi bi-mortarboard me-2"></i>{{ __('Class') }}</td>
                            <td class="text-end">
                                <strong>{{ $note->class->name ?? 'N/A' }}</strong>
                            </td>
                        </tr>
                        @if($note->file_size)
                            <tr>
                                <td class="text-muted"><i class="bi bi-hdd me-2"></i>{{ __('File Size') }}</td>
                                <td class="text-end">
                                    <strong>{{ $note->file_size_formatted }}</strong>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted"><i class="bi bi-eye me-2"></i>{{ __('Views') }}</td>
                            <td class="text-end">
                                <strong>{{ $note->views_count }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="bi bi-download me-2"></i>{{ __('Downloads') }}</td>
                            <td class="text-end">
                                <strong>{{ $note->downloads_count }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="bi bi-calendar me-2"></i>{{ __('Added') }}</td>
                            <td class="text-end">
                                <strong>{{ $note->created_at->format('M d, Y') }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info bg-opacity-10 border-0">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>{{ __('Tips') }}
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li>{{ __('Download materials for offline study') }}</li>
                        <li>{{ __('Take personal notes while reviewing') }}</li>
                        <li>{{ __('Contact your teacher if you have questions') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

