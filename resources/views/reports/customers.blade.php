@extends('layouts.app')

@section('title', 'Customer Reports - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-people"></i> Customer Report
</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.customers') }}" class="row g-2 align-items-end">
            <div class="col-md-10">
                <label class="form-label" for="search">Search Customer</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by first or last name">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Total Customers</h5>
            <div class="value">{{ number_format($totalCustomers) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Total Transactions</h5>
            <div class="value">{{ number_format($totalTransactions) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Avg Loans / Customer</h5>
            <div class="value">{{ number_format($averageLoansPerCustomer, 2) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Transactions</th>
                    <th>Total Loaned</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->full_name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email ?: 'N/A' }}</td>
                        <td><span class="badge bg-primary">{{ $customer->transactions_count }}</span></td>
                        <td>₱{{ number_format($customer->transactions_sum_loan_amount ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $customers->withQueryString()->links() }}
    </div>
</div>
@endsection
