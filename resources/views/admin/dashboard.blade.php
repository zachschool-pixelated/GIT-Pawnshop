@extends('layouts.app')

@section('title', 'Admin Dashboard - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-shield-check"></i> Admin Dashboard
</h1>

<div class="row">
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Total System Users</h5>
            <div class="value">{{ number_format($totalUsers) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Admin Accounts</h5>
            <div class="value">{{ number_format($adminsCount) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Teller Accounts</h5>
            <div class="value">{{ number_format($tellersCount) }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Users by Role</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Role</th>
                            <th>Accounts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usersByRole as $role)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $role->role)) }}</td>
                                <td><span class="badge bg-primary">{{ $role->count }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No user data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Recently Created Accounts</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentUsers as $account)
                            <tr>
                                <td>{{ $account->name }}</td>
                                <td>{{ $account->email }}</td>
                                <td><span class="badge bg-info text-dark">{{ ucwords(str_replace('_', ' ', $account->role)) }}</span></td>
                                <td>{{ $account->created_at?->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">System Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Customers</span>
                    <strong>{{ number_format($systemSummary['customers']) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Transactions</span>
                    <strong>{{ number_format($systemSummary['transactions']) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Active Transactions</span>
                    <strong>{{ number_format($systemSummary['active_transactions']) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-0">
                    <span>Total Revenue</span>
                    <strong>₱{{ number_format($systemSummary['total_revenue'], 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Admin Actions</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                    <i class="bi bi-person-gear"></i> Manage User Accounts
                </a>
                <a href="{{ route('reports.audit-logs') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-clock-history"></i> View Audit Logs
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-speedometer2"></i> Go to Operations Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
