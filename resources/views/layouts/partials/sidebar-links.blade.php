@php
    $role = Auth::user()->role;
    $canCustomers = in_array($role, ['admin', 'branch_manager', 'teller'], true);
    $canTransactions = in_array($role, ['admin', 'branch_manager', 'teller'], true);
    $canPayments = in_array($role, ['admin', 'branch_manager', 'cashier'], true);
    $canReports = in_array($role, ['admin', 'branch_manager', 'auditor'], true);
    $canAudit = in_array($role, ['admin', 'auditor'], true);
    $isAdmin = $role === 'admin';
@endphp

<li class="nav-item">
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</li>
@if ($canTransactions)
    <li class="nav-item">
        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Transactions
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('transactions.createPawn') }}" class="nav-link {{ request()->routeIs('transactions.createPawn') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> New Pawn
        </a>
    </li>
@endif
@if ($canCustomers)
    <li class="nav-item">
        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customers
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('customers.create') }}" class="nav-link {{ request()->routeIs('customers.create') ? 'active' : '' }}">
            <i class="bi bi-person-plus"></i> Add Customer
        </a>
    </li>
@endif
@if ($canPayments)
    <li class="nav-item">
        <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Payments
        </a>
    </li>
@endif
@if ($isAdmin)
    <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> Admin Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Manage Accounts
        </a>
    </li>
@endif
@if ($canReports || $canAudit)
    <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
@endif
@if ($canReports)
    <li class="nav-item">
        <a href="{{ route('reports.transactions') }}" class="nav-link {{ request()->routeIs('reports.transactions') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('reports.financial-summary') }}" class="nav-link {{ request()->routeIs('reports.financial-summary') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Financial
        </a>
    </li>
@endif
@if ($canAudit)
    <li class="nav-item">
        <a href="{{ route('reports.audit-logs') }}" class="nav-link {{ request()->routeIs('reports.audit-logs') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Audit Logs
        </a>
    </li>
@endif
<hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
<li class="nav-item">
    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="bi bi-person"></i> Profile
    </a>
</li>
<li class="nav-item">
    <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="nav-link" style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </form>
</li>