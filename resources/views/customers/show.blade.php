@extends('layouts.app')

@section('title', 'Customer Details - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-person-badge"></i> Customer Details
</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Customers
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('transactions.createPawn') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Pawn Transaction
        </a>
        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" data-confirm-title="Delete Customer" data-confirm-message="Delete this customer and related records? This cannot be undone." data-confirm-button="Delete Customer" data-confirm-button-class="btn-danger">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">KYC Information</div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> {{ $customer->full_name }}</p>
                <p class="mb-2"><strong>Phone:</strong> {{ $customer->phone }}</p>
                <p class="mb-2"><strong>Email:</strong> {{ $customer->email ?: 'N/A' }}</p>
                <p class="mb-2"><strong>Address:</strong> {{ $customer->address }}</p>
                <p class="mb-2"><strong>ID Type:</strong> {{ ucwords(str_replace('_', ' ', $customer->id_type)) }}</p>
                <p class="mb-2"><strong>ID Number:</strong> {{ $customer->id_number }}</p>
                <p class="mb-0"><strong>Occupation:</strong> {{ $customer->occupation ?: 'N/A' }}</p>
            </div>
            <div class="card-footer small text-muted">
                Registered {{ $customer->created_at?->format('M d, Y h:i A') }}
            </div>
        </div>

        <div class="card">
            <div class="card-header">Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Transactions</span>
                    <span class="badge bg-primary">{{ $customer->transactions->count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Active Transactions</span>
                    <span class="badge bg-warning text-dark">{{ $customer->transactions->where('status', 'active')->count() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Total Loaned Amount</span>
                    <strong>₱{{ number_format($customer->transactions->sum('loan_amount'), 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Transactions</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket #</th>
                            <th>Item</th>
                            <th>Loan</th>
                            <th>Maturity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->pawn_ticket_number }}</td>
                                <td>{{ $transaction->item->item_name ?? 'N/A' }}</td>
                                <td>₱{{ number_format($transaction->loan_amount, 2) }}</td>
                                <td>{{ $transaction->maturity_date?->format('M d, Y') }}</td>
                                <td>
                                    @if ($transaction->status === 'active')
                                        <span class="badge bg-warning text-dark">Active</span>
                                    @elseif ($transaction->status === 'redeemed')
                                        <span class="badge bg-success">Redeemed</span>
                                    @elseif ($transaction->status === 'forfeited')
                                        <span class="badge bg-danger">Forfeited</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No transactions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Pawned Items</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Condition</th>
                            <th>Assessed Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customer->items as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->category }}</td>
                                <td>{{ ucfirst($item->condition) }}</td>
                                <td>₱{{ number_format($item->assessed_value, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No items recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Audit History</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>User</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                                <td><span class="badge bg-info text-dark">{{ ucfirst($log->action) }}</span></td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td>{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No audit entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
