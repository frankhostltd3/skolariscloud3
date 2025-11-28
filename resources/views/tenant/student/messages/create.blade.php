@extends('layouts.tenant.student')

@section('title', 'New Message')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <a href="{{ route('tenant.student.messages.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Messages') }}
                    </a>
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>{{ __('Compose New Message') }}
                    </h4>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('tenant.student.messages.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="recipient_id" class="form-label">{{ __('To (Recipient)') }} <span
                                        class="text-danger">*</span></label>
                                <select name="recipient_id" id="recipient_id"
                                    class="form-select @error('recipient_id') is-invalid @enderror" required>
                                    <option value="">{{ __('-- Select Teacher --') }}</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->user->id }}"
                                            {{ old('recipient_id') == $teacher->user->id ? 'selected' : '' }}>
                                            {{ $teacher->name }} - {{ $teacher->subjects->pluck('name')->join(', ') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('recipient_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">{{ __('Subject') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="subject" id="subject"
                                    class="form-control @error('subject') is-invalid @enderror"
                                    placeholder="{{ __('Enter message subject...') }}" value="{{ old('subject') }}"
                                    maxlength="255" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">{{ __('Message') }} <span
                                        class="text-danger">*</span></label>
                                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="10"
                                    placeholder="{{ __('Type your message here...') }}" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="attachment" class="form-label">{{ __('Attach File (Optional)') }}</label>
                                <input type="file" name="attachment" id="attachment"
                                    class="form-control @error('attachment') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                                <small
                                    class="text-muted">{{ __('Maximum file size: 10MB. Allowed formats: PDF, DOC, DOCX, JPG, PNG, GIF') }}</small>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>{{ __('Tips:') }}</strong>
                                <ul class="mb-0 mt-2">
                                    <li>{{ __('Be clear and specific in your subject line') }}</li>
                                    <li>{{ __('Keep your message concise and to the point') }}</li>
                                    <li>{{ __('Be respectful and professional') }}</li>
                                    <li>{{ __('Your teacher will be notified about your message') }}</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tenant.student.messages.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>{{ __('Send Message') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
