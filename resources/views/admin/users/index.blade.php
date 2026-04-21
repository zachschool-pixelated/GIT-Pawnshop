@extends('layouts.app')

@section('title', 'Manage Accounts - Admin')

@section('content')
<h1 class="page-title">
    <i class="bi bi-person-gear"></i> Manage Accounts
</h1>

<div class="card mb-3">
    <div class="card-header">Create New Account</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}" class="row g-3" data-confirm-title="Create Account" data-confirm-message="Create this user account with the selected role?" data-confirm-button="Create Account" data-confirm-button-class="btn-primary">
            @csrf
            <div class="col-md-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" name="name" type="text" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}">{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input id="create_password" name="password" type="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="create_password" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <label for="password_confirmation" class="form-label">Confirm</label>
                <div class="input-group">
                    <input id="create_password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="create_password_confirmation" aria-label="Show password confirmation">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label class="form-label" for="search">Search</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by name or email">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="filter_role">Role</label>
                <select id="filter_role" name="role" class="form-select">
                    <option value="">All</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th style="min-width: 320px;">Update Account</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->email }}</td>
                        <td><span class="badge bg-info text-dark">{{ ucwords(str_replace('_', ' ', $account->role)) }}</span></td>
                        <td>{{ $account->created_at?->format('M d, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.update', $account->id) }}" class="row g-2" data-confirm-title="Update Account" data-confirm-message="Save changes to this account?" data-confirm-button="Save Changes" data-confirm-button-class="btn-primary">
                                @csrf
                                @method('PATCH')
                                <div class="col-12">
                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $account->name }}" required>
                                </div>
                                <div class="col-12">
                                    <input type="email" name="email" class="form-control form-control-sm" value="{{ $account->email }}" required>
                                </div>
                                <div class="col-12">
                                    <select name="role" class="form-select form-select-sm" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected($account->role === $role)>{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="input-group input-group-sm">
                                        <input id="update_password_{{ $account->id }}" type="password" name="password" class="form-control form-control-sm" placeholder="New password (optional)">
                                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="update_password_{{ $account->id }}" aria-label="Show password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group input-group-sm">
                                        <input id="update_password_confirmation_{{ $account->id }}" type="password" name="password_confirmation" class="form-control form-control-sm" placeholder="Confirm new password">
                                        <button type="button" class="btn btn-outline-secondary toggle-password-btn" data-target="update_password_confirmation_{{ $account->id }}" aria-label="Show password confirmation">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-save"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.destroy', $account->id) }}" data-confirm-title="Delete Account" data-confirm-message="Delete account {{ $account->email }}? This action cannot be undone." data-confirm-button="Delete Account" data-confirm-button-class="btn-danger">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No user accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-password-btn');

    toggleButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = button.querySelector('i');

            if (!input || !icon) {
                return;
            }

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !isHidden);
            icon.classList.toggle('bi-eye-slash', isHidden);
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    });
});
</script>
@endsection
