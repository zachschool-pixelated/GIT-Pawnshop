@extends('layouts.app')

@section('title', 'Profile - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-person-circle"></i> My Profile
</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">Account Summary</div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> {{ $user->name }}</p>
                <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="mb-2"><strong>Role:</strong> <span class="badge bg-info text-dark">{{ $user->role }}</span></p>
                <p class="mb-2">
                    <strong>Email Verified:</strong>
                    @if ($user->email_verified_at)
                        <span class="badge bg-success">Yes</span>
                    @else
                        <span class="badge bg-warning text-dark">No</span>
                    @endif
                </p>
                <p class="mb-0"><strong>Joined:</strong> {{ $user->created_at?->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Update Profile Information</div>
            <div class="card-body">
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success">Profile details updated successfully.</div>
                @endif

                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <form method="POST" action="{{ route('profile.update') }}" data-confirm-title="Update Profile" data-confirm-message="Save changes to your profile information?" data-confirm-button="Save Profile" data-confirm-button-class="btn-primary">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}"
                            required
                            autofocus
                            autocomplete="name"
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}"
                            required
                            autocomplete="username"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-warning d-flex justify-content-between align-items-center">
                            <div>Your email address is unverified.</div>
                            <button form="send-verification" class="btn btn-sm btn-outline-dark" type="submit">
                                Re-send Verification Email
                            </button>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success">
                                A new verification link has been sent to your email address.
                            </div>
                        @endif
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Save Profile
                    </button>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Update Password</div>
            <div class="card-body">
                @if (session('status') === 'password-updated')
                    <div class="alert alert-success">Password updated successfully.</div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" data-confirm-title="Update Password" data-confirm-message="Change your account password now?" data-confirm-button="Update Password" data-confirm-button-class="btn-primary">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                            autocomplete="current-password"
                        >
                        @if($errors->updatePassword->has('current_password'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                            autocomplete="new-password"
                        >
                        @if($errors->updatePassword->has('password'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                            autocomplete="new-password"
                        >
                        @if($errors->updatePassword->has('password_confirmation'))
                            <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-shield-lock"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        @if ($user->role === 'admin')
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">Delete Account</div>
                <div class="card-body">
                    <p class="text-muted">
                        Once your account is deleted, all of its resources and data will be permanently deleted.
                        This action cannot be undone.
                    </p>

                    <form method="POST" action="{{ route('profile.destroy') }}" data-confirm-title="Delete Account" data-confirm-message="Are you sure you want to permanently delete your account? This cannot be undone." data-confirm-button="Delete Account" data-confirm-button-class="btn-danger">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label for="delete_password" class="form-label">Confirm Password</label>
                            <input
                                id="delete_password"
                                name="password"
                                type="password"
                                class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                                placeholder="Enter your current password"
                            >
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete My Account
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
