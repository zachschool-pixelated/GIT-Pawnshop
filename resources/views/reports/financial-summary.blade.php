@extends('layouts.app')

@section('title', 'Financial Summary - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-bar-chart"></i> Financial Summary
</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.financial-summary') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label" for="date_from">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from', $dateFrom->format('Y-m-d')) }}">
            </div>
            <div class="col-md-5">
                <label class="form-label" for="date_to">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to', $dateTo->format('Y-m-d')) }}">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Total Loans Issued</h5>
            <div class="value">₱{{ number_format($summary['total_loans_issued'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Interest Charged</h5>
            <div class="value">₱{{ number_format($summary['total_interest_charged'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Interest Collected</h5>
            <div class="value">₱{{ number_format($summary['total_interest_collected'], 2) }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Total Redemptions</h5>
            <div class="value">₱{{ number_format($summary['total_redemptions'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Items Forfeited</h5>
            <div class="value">{{ number_format($summary['items_forfeited']) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h5>Redemption Rate</h5>
            <div class="value">{{ number_format($summary['redemption_rate'], 2) }}%</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Profitability Snapshot</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Net Income</h6>
                @php
                    $isPositive = $summary['net_income'] >= 0;
                @endphp
                <h3 class="{{ $isPositive ? 'text-success' : 'text-danger' }}">
                    ₱{{ number_format($summary['net_income'], 2) }}
                </h3>
                <p class="text-muted mb-0">Computed as interest collected minus interest charged.</p>
            </div>
            <div class="col-md-6">
                <h6>Selected Period</h6>
                <p class="mb-0">{{ $dateFrom->format('M d, Y') }} to {{ $dateTo->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
