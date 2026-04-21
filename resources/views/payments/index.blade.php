@extends('layouts.app')

@section('title', 'Payments - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-cash-coin"></i> Payments
</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Receipt #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment->receipt_number }}</strong></td>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ $payment->transaction->customer->full_name }}</td>
                        <td>₱{{ number_format($payment->amount_paid, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $payment->payment_type == 'interest_payment' ? 'info' : 'success' }}">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No payments recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $payments->links() }}
    </div>
</div>
@endsection