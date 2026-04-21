@extends('layouts.app')

@section('title', 'Payment Reports - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-cash-coin"></i> Payment Report
</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.payments') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="date_from">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="date_to">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="payment_type">Payment Type</label>
                <select id="payment_type" name="payment_type" class="form-select">
                    <option value="">All</option>
                    <option value="interest_payment" @selected(request('payment_type') === 'interest_payment')>Interest Payment</option>
                    <option value="full_redemption" @selected(request('payment_type') === 'full_redemption')>Full Redemption</option>
                    <option value="partial" @selected(request('payment_type') === 'partial')>Partial</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="stat-card">
            <h5>Total Amount</h5>
            <div class="value">₱{{ number_format($totals['total_amount'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <h5>Payments Count</h5>
            <div class="value">{{ number_format($totals['count']) }}</div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Payment Type Breakdown</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Type</th>
                    <th>Count</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($totals['by_type'] as $row)
                    <tr>
                        <td>{{ ucwords(str_replace('_', ' ', $row->payment_type)) }}</td>
                        <td>{{ $row->count }}</td>
                        <td>₱{{ number_format($row->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">No summary data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Receipt #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Ticket #</th>
                    <th>Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                        <td>{{ $payment->transaction->customer->full_name ?? 'N/A' }}</td>
                        <td>{{ $payment->transaction->pawn_ticket_number ?? 'N/A' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->payment_type)) }}</td>
                        <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No payments found for selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $payments->withQueryString()->links() }}
    </div>
</div>
@endsection
