@extends('layouts.app')

@section('title', 'Add Customer - Pawnshop')

@section('content')
<h1 class="page-title">
    <i class="bi bi-person-plus"></i> Add New Customer (KYC)
</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                Customer Information
            </div>
            <div class="card-body">
                <form action="{{ route('customers.store') }}" method="POST" data-confirm-title="Create Customer" data-confirm-message="Save this new customer record?" data-confirm-button="Create Customer" data-confirm-button-class="btn-primary">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" required
                                   inputmode="numeric" maxlength="11" pattern="09[0-9]{9}" placeholder="09XXXXXXXXX"
                                   oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11)">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_type" class="form-label">ID Type *</label>
                            <select class="form-select @error('id_type') is-invalid @enderror" 
                                    id="id_type" name="id_type" required>
                                <option value="">Select ID Type</option>
                                <option value="national_id" {{ old('id_type') == 'national_id' ? 'selected' : '' }}>
                                    National ID
                                </option>
                                <option value="passport" {{ old('id_type') == 'passport' ? 'selected' : '' }}>
                                    Passport
                                </option>
                                <option value="driver_license" {{ old('id_type') == 'driver_license' ? 'selected' : '' }}>
                                    Driver's License
                                </option>
                            </select>
                            @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_number" class="form-label">ID Number *</label>
                            <input type="text" class="form-control @error('id_number') is-invalid @enderror" 
                                   id="id_number" name="id_number" value="{{ old('id_number') }}" required>
                            @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="occupation" class="form-label">Occupation</label>
                        <input type="text" class="form-control @error('occupation') is-invalid @enderror" 
                               id="occupation" name="occupation" value="{{ old('occupation') }}">
                        @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Customer
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5><i class="bi bi-info-circle"></i> KYC Requirements</h5>
                <p class="small mb-0">
                    <strong>Know Your Customer (KYC)</strong> is required for all transactions.
                </p>
                <ul class="small mt-2 mb-0">
                    <li>Valid ID required (National, Passport, Driver's License)</li>
                    <li>All fields marked * are required</li>
                    <li>Phone number must be unique</li>
                    <li>ID number must be unique</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection