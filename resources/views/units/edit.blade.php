@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Unit
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('units.update', $unit) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" placeholder="e.g., Kilogram, Piece, Liter" value="{{ old('name', $unit->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="abbreviation" class="form-label">Abbreviation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" 
                                   id="abbreviation" name="abbreviation" placeholder="e.g., kg, pcs, L" value="{{ old('abbreviation', $unit->abbreviation) }}" required>
                            @error('abbreviation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="conversion_factor" class="form-label">Conversion Factor</label>
                            <input type="number" step="0.0001" class="form-control @error('conversion_factor') is-invalid @enderror" 
                                   id="conversion_factor" name="conversion_factor" value="{{ old('conversion_factor', $unit->conversion_factor) }}">
                            @error('conversion_factor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" placeholder="Enter unit description">{{ old('description', $unit->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1" 
                                   {{ old('active', $unit->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Active
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Unit
                            </button>
                            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
