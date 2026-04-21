@extends('layouts.app')

@section('title', 'Transaction Reports - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-graph-up"></i> Transaction Report
</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.transactions') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="date_from">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="date_to">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="redeemed" @selected(request('status') === 'redeemed')>Redeemed</option>
                    <option value="forfeited" @selected(request('status') === 'forfeited')>Forfeited</option>
                    <option value="auctioned" @selected(request('status') === 'auctioned')>Auctioned</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="transaction_type">Type</label>
                <select id="transaction_type" name="transaction_type" class="form-select">
                    <option value="">All</option>
                    <option value="pawn" @selected(request('transaction_type') === 'pawn')>Pawn</option>
                    <option value="renewal" @selected(request('transaction_type') === 'renewal')>Renewal</option>
                    <option value="redemption" @selected(request('transaction_type') === 'redemption')>Redemption</option>
                    <option value="auction" @selected(request('transaction_type') === 'auction')>Auction</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Apply</button>
            </div>
        </form>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('reports.transactions') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            <a href="{{ route('reports.export-transactions', request()->query()) }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Total Loans</h5>
            <div class="value">₱{{ number_format($totals['total_loans'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Total Interest</h5>
            <div class="value">₱{{ number_format($totals['total_interest'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Total Paid</h5>
            <div class="value">₱{{ number_format($totals['total_paid'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h5>Transactions</h5>
            <div class="value">{{ number_format($totals['count']) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Ticket #</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Loan</th>
                    <th>Interest</th>
                    <th>Paid</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->pawn_ticket_number }}</td>
                        <td>{{ $transaction->customer->full_name ?? 'N/A' }}</td>
                        <td>{{ $transaction->item->item_name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($transaction->transaction_type) }}</td>
                        <td>₱{{ number_format($transaction->loan_amount, 2) }}</td>
                        <td>₱{{ number_format($transaction->total_interest, 2) }}</td>
                        <td>₱{{ number_format($transaction->amount_paid, 2) }}</td>
                        <td><span class="badge bg-info text-dark">{{ ucfirst($transaction->status) }}</span></td>
                        <td>{{ $transaction->created_at?->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No transactions found for selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $transactions->withQueryString()->links() }}
    </div>
</div>
@endsection
