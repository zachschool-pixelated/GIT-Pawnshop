@extends('layouts.app')

@section('title', 'Audit Logs - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-clock-history"></i> Audit Log Report
</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.audit-logs') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="date_from">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="date_to">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="action">Action</label>
                <input type="text" id="action" name="action" class="form-control" value="{{ request('action') }}" placeholder="created, updated, deleted">
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Action Summary</div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @forelse ($actionSummary as $summary)
                <span class="badge bg-info text-dark p-2">{{ ucfirst($summary->action) }}: {{ $summary->count }}</span>
            @empty
                <span class="text-muted">No action summary available.</span>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Model</th>
                    <th>Model ID</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($auditLogs as $log)
                    <tr>
                        <td>{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                        <td>{{ $log->user->name ?? 'System' }}</td>
                        <td>{{ ucfirst($log->action) }}</td>
                        <td>{{ $log->model_type }}</td>
                        <td>{{ $log->model_id }}</td>
                        <td>{{ $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No audit records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $auditLogs->withQueryString()->links() }}
    </div>
</div>
@endsection
