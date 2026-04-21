@extends('layouts.app')

@section('title', 'Transactions - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-file-earmark-text"></i> Transactions
</h1>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('transactions.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by ticket # or customer name" 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="redeemed" {{ request('status') == 'redeemed' ? 'selected' : '' }}>Redeemed</option>
                    <option value="forfeited" {{ request('status') == 'forfeited' ? 'selected' : '' }}>Forfeited</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('transactions.createPawn') }}" class="btn btn-success w-100">
                    <i class="bi bi-plus-circle"></i> New Pawn
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pawn Ticket</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Loan Amount</th>
                    <th>Interest Rate</th>
                    <th>Maturity Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>
                            <strong>{{ $transaction->pawn_ticket_number }}</strong>
                        </td>
                        <td>{{ $transaction->customer->full_name }}</td>
                        <td>{{ $transaction->item->item_name }}</td>
                        <td>₱{{ number_format($transaction->loan_amount, 2) }}</td>
                        <td>{{ $transaction->interest_rate }}%</td>
                        <td>{{ $transaction->maturity_date->format('M d, Y') }}</td>
                        <td>
                            @if ($transaction->status == 'active')
                                <span class="badge bg-warning">Active</span>
                            @elseif ($transaction->status == 'redeemed')
                                <span class="badge bg-success">Redeemed</span>
                            @elseif ($transaction->status == 'forfeited')
                                <span class="badge bg-danger">Forfeited</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('transactions.show', $transaction->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No transactions found. <a href="{{ route('transactions.createPawn') }}">Create a new pawn</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $transactions->links() }}
    </div>
</div>
@endsection