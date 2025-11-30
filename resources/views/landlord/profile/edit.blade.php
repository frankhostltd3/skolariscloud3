@extends('landlord.layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('My Profile') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('landlord.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Column: Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-5">
                    <div class="position-relative d-inline-block mb-3">
                        @if ($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}"
                                class="rounded-circle img-thumbnail object-fit-cover" style="width: 150px; height: 150px;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto fs-1 fw-bold"
                                style="width: 150px; height: 150px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        <label for="profile_photo"
                            class="position-absolute bottom-0 end-0 btn btn-sm btn-light rounded-circle shadow-sm border"
                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                            title="Change Photo">
                            <i class="bi bi-camera-fill text-primary"></i>
                        </label>
                    </div>

                    <h3 class="h4 fw-bold mb-1">{{ $user->name }}</h3>
                    <p class="text-muted mb-3">{{ $user->email }}</p>

                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                            <i class="bi bi-shield-lock-fill me-1"></i>{{ __('Super Admin') }}
                        </span>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ __('Active') }}
                        </span>
                    </div>

                    <div class="border-top pt-4 text-start">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded p-2 me-3 text-primary">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('Phone') }}</small>
                                <span class="fw-medium">{{ $user->phone ?? __('Not set') }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded p-2 me-3 text-primary">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('Location') }}</small>
                                <span class="fw-medium">{{ $user->address ?? __('Not set') }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-2 me-3 text-primary">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ __('Joined') }}</small>
                                <span class="fw-medium">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" id="general-tab" data-bs-toggle="tab"
                                data-bs-target="#general" type="button" role="tab" aria-controls="general"
                                aria-selected="true">
                                <i class="bi bi-person-lines-fill me-2"></i>{{ __('General Information') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="security-tab" data-bs-toggle="tab"
                                data-bs-target="#security" type="button" role="tab" aria-controls="security"
                                aria-selected="false">
                                <i class="bi bi-shield-lock me-2"></i>{{ __('Security') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('landlord.profile.update') }}" method="post" enctype="multipart/form-data"
                        id="photo-preview-form">
                        @csrf
                        @method('put')

                        <!-- Hidden file input triggered by the camera icon -->
                        <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*"
                            onchange="this.form.submit()">

                        <div class="tab-content" id="profileTabsContent">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel"
                                aria-labelledby="general-tab">
                                <h5 class="mb-4 text-secondary">{{ __('Personal Details') }}</h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-medium">{{ __('Full Name') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $user->name) }}"
                                                required>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email"
                                            class="form-label fw-medium">{{ __('Email Address') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                name="email" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone"
                                            class="form-label fw-medium">{{ __('Phone Number') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                                            <input type="text"
                                                class="form-control @error('phone') is-invalid @enderror" id="phone"
                                                name="phone" value="{{ old('phone', $user->phone) }}"
                                                placeholder="+1 234 567 890">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label fw-medium">{{ __('Address') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2"
                                                placeholder="123 Main St, City, Country">{{ old('address', $user->address) }}</textarea>
                                        </div>
                                        @error('address')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Security Tab -->
                            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                <h5 class="mb-4 text-secondary">{{ __('Change Password') }}</h5>
                                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis mb-4">
                                    <i
                                        class="bi bi-info-circle me-2"></i>{{ __('Leave these fields blank if you do not want to change your password.') }}
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="password"
                                            class="form-label fw-medium">{{ __('New Password') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password" autocomplete="new-password">
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="password_confirmation"
                                            class="form-label fw-medium">{{ __('Confirm New Password') }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-check2-square"></i></span>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('landlord.dashboard') }}"
                                class="btn btn-light border">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>{{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
