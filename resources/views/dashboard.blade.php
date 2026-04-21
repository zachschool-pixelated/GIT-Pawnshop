@extends('layouts.app')

@section('title', 'Dashboard - Pawnshop Management System')

@section('content')
<h1 class="page-title">
    <i class="bi bi-speedometer2"></i> Dashboard
</h1>

<!-- Statistics Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Total Customers</h5>
            <div class="value">{{ $totalCustomers }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Total Transactions</h5>
            <div class="value">{{ $totalTransactions }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Active Pawns</h5>
            <div class="value">{{ $activeTransactions }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Redeemed Items</h5>
            <div class="value">{{ $redeemedTransactions }}</div>
        </div>
    </div>
</div>

<!-- Financial Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-coin"></i> Total Loaned Amount
            </div>
            <div class="card-body">
                <h3 class="text-primary">₱{{ number_format($totalLoanedAmount, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-coin"></i> Interest Collected
            </div>
            <div class="card-body">
                <h3 class="text-success">₱{{ number_format($totalInterestCollected, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-arrow-counterclockwise"></i> Total Redemptions
            </div>
            <div class="card-body">
                <h3 class="text-info">₱{{ number_format($totalRedemptions, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions and Payments -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Recent Transactions
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $transaction)
                            <tr>
                                <td>
                                    <a href="{{ route('transactions.show', $transaction->id) }}">
                                        {{ $transaction->pawn_ticket_number }}
                                    </a>
                                </td>
                                <td>{{ $transaction->customer->full_name }}</td>
                                <td>₱{{ number_format($transaction->loan_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->status == 'active' ? 'warning' : ($transaction->status == 'redeemed' ? 'success' : 'danger') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No transactions yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-receipt"></i> Recent Payments
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentPayments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('payments.show', $payment->id) }}">
                                        {{ $payment->receipt_number }}
                                    </a>
                                </td>
                                <td>{{ $payment->transaction->customer->full_name }}</td>
                                <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No payments yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Status Overview -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> Transaction Status Breakdown
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tbody>
                        @foreach ($transactionStatusBreakdown as $status => $count)
                            <tr>
                                <td>{{ ucfirst($status) }}</td>
                                <td class="text-end">
                                    <span class="badge bg-primary">{{ $count }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-tag"></i> Items by Category
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Count</th>
                            <th>Avg Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($itemsByCategory as $category)
                            <tr>
                                <td>{{ ucfirst($category->category) }}</td>
                                <td><span class="badge bg-info">{{ $category->count }}</span></td>
                                <td>₱{{ number_format($category->avg_value, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No items yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Top Customers -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-star"></i> Top Customers by Transaction Count
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Transactions</th>
                            <th>Total Loaned</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topCustomers as $customer)
                            <tr>
                                <td>{{ $customer->full_name }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td><span class="badge bg-warning">{{ $customer->transactions_count }}</span></td>
                                <td>₱{{ number_format($customer->transactions->sum('loan_amount'), 2) }}</td>
                                <td>
                                    <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No customers yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
