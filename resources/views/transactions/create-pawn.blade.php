@extends('layouts.app')

@section('title', 'New Pawn Transaction - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-plus-circle"></i> New Pawn Transaction
</h1>

<form action="{{ route('transactions.storePawn') }}" method="POST" class="row" data-confirm-title="Create Pawn Transaction" data-confirm-message="Create this pawn transaction and issue a pawn ticket?" data-confirm-button="Create Transaction" data-confirm-button-class="btn-primary">
    @csrf
    
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">Select Customer</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Customer *</label>
                    <select class="form-select @error('customer_id') is-invalid @enderror" 
                            id="customer_id" name="customer_id" required>
                        <option value="">Select a customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->full_name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <a href="{{ route('customers.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-person-plus"></i> Add New Customer
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Item Details</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="item_name" class="form-label">Item Name *</label>
                    <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                           id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                    @error('item_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="item_description" class="form-label">Description *</label>
                    <textarea class="form-control @error('item_description') is-invalid @enderror" 
                              id="item_description" name="item_description" rows="3" required>{{ old('item_description') }}</textarea>
                    @error('item_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category *</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" 
                               id="category" name="category" placeholder="e.g., Jewelry, Gadgets" 
                               value="{{ old('category') }}" required>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="condition" class="form-label">Condition *</label>
                        <select class="form-select @error('condition') is-invalid @enderror" 
                                id="condition" name="condition" required>
                            <option value="">Select condition</option>
                            <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                            <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                        @error('condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="assessed_value" class="form-label">Assessed Value (₱) *</label>
                    <input type="number" class="form-control @error('assessed_value') is-invalid @enderror" 
                           id="assessed_value" name="assessed_value" step="0.01" 
                           value="{{ old('assessed_value') }}" required>
                    @error('assessed_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Transaction Terms</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="loan_amount" class="form-label">Loan Amount (₱) *</label>
                    <input type="number" class="form-control @error('loan_amount') is-invalid @enderror" 
                           id="loan_amount" name="loan_amount" step="0.01" 
                           value="{{ old('loan_amount') }}" required>
                    <small class="form-text text-muted">Recommended: 60-70% of assessed value</small>
                    @error('loan_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="interest_rate" class="form-label">Interest Rate (%) *</label>
                        <input type="number" class="form-control @error('interest_rate') is-invalid @enderror" 
                               id="interest_rate" name="interest_rate" step="0.01" min="0" max="100"
                               value="{{ old('interest_rate', '5') }}" required>
                        @error('interest_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="term_days" class="form-label">Term (Days) *</label>
                        <input type="number" class="form-control @error('term_days') is-invalid @enderror" 
                               id="term_days" name="term_days" min="1" max="365"
                               value="{{ old('term_days', '30') }}" required>
                        @error('term_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>System will automatically calculate:</strong>
                    <ul class="mb-0">
                        <li>Total Interest Amount</li>
                        <li>Maturity Date</li>
                        <li>Pawn Ticket Number</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">Summary</div>
            <div class="card-body" id="summary">
                <p class="text-muted">Fill in the fields to see summary</p>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle"></i> Create Pawn Transaction
                </button>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary w-100 mt-2">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loanAmount = document.getElementById('loan_amount');
    const interestRate = document.getElementById('interest_rate');
    const termDays = document.getElementById('term_days');
    const summary = document.getElementById('summary');

    function updateSummary() {
        const principal = parseFloat(loanAmount.value) || 0;
        const rate = parseFloat(interestRate.value) || 0;
        const days = parseInt(termDays.value) || 0;
        
        const interest = principal * (rate / 100) * (days / 365);
        const total = principal + interest;
        
        summary.innerHTML = `
            <table class="table table-sm">
                <tr>
                    <td>Loan Amount:</td>
                    <td class="text-end"><strong>₱${principal.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                </tr>
                <tr>
                    <td>Interest Rate:</td>
                    <td class="text-end"><strong>${rate}%</strong></td>
                </tr>
                <tr>
                    <td>Term:</td>
                    <td class="text-end"><strong>${days} days</strong></td>
                </tr>
                <tr class="table-warning">
                    <td>Interest Amount:</td>
                    <td class="text-end"><strong>₱${interest.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                </tr>
                <tr class="table-success">
                    <td>Total Amount Due:</td>
                    <td class="text-end"><strong>₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                </tr>
            </table>
        `;
    }

    loanAmount.addEventListener('change', updateSummary);
    interestRate.addEventListener('change', updateSummary);
    termDays.addEventListener('change', updateSummary);
});
</script>
@endsection