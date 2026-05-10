@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Edit Supplier</h1>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to list</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $supplier->address) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $supplier->city) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $supplier->state) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ZIP Code</label>
                        <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code', $supplier->zip_code) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Opening Balance</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $supplier->opening_balance) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Due Balance</label>
                        <input type="number" step="0.01" name="due_balance" class="form-control" value="{{ old('due_balance', $supplier->due_balance) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $supplier->notes) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
