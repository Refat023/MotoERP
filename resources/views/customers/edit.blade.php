@extends('layouts.app')

@section('title', 'Edit Customer - Super Shop POS')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <h1 class="mb-0"><i class="bi bi-pencil-circle"></i> Edit Customer</h1>
        </div>

        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h5 class="card-title mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                id="contact_person" name="contact_person" value="{{ old('contact_person', $customer->contact_person) }}">
                            @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', $customer->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                            id="address" name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="tax_id" class="form-label">Tax ID (BIR/TIN)</label>
                        <input type="text" class="form-control @error('tax_id') is-invalid @enderror" 
                            id="tax_id" name="tax_id" value="{{ old('tax_id', $customer->tax_id) }}">
                        @error('tax_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="credit_limit" class="form-label"><i class="bi bi-credit-card"></i> Credit Limit</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" class="form-control @error('credit_limit') is-invalid @enderror" 
                                    id="credit_limit" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" 
                                    step="0.01" min="0">
                            </div>
                            @error('credit_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Update Information
                        </button>
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
