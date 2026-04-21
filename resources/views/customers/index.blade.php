@extends('layouts.app')

@section('title', 'Customers - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-people"></i> Customers
</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label">Search</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Search by name, phone, or email"
                >
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>ID Type</th>
                    <th>Transactions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->full_name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email ?: 'N/A' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $customer->id_type)) }}</td>
                        <td><span class="badge bg-primary">{{ $customer->transactions->count() }}</span></td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $customers->links() }}
    </div>
</div>
@endsection
