@extends('layouts.app')

@section('title', 'Add Product - Super Shop POS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Add New Product
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" id="productForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" placeholder="Enter product name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                               id="sku" name="sku" placeholder="e.g., PROD-001" value="{{ old('sku') }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="barcode" class="form-label">Barcode</label>
                                        <input type="text" class="form-control @error('barcode') is-invalid @enderror" 
                                               id="barcode" name="barcode" placeholder="Enter product barcode" value="{{ old('barcode') }}">
                                        @error('barcode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="weight" class="form-label">Weight (kg)</label>
                                        <input type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror" 
                                               id="weight" name="weight" placeholder="Enter weight" value="{{ old('weight') }}">
                                        @error('weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" placeholder="Enter product description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="specifications" class="form-label">Specifications</label>
                                    <textarea class="form-control @error('specifications') is-invalid @enderror" 
                                              id="specifications" name="specifications" rows="2" placeholder="Enter product specifications">{{ old('specifications') }}</textarea>
                                    @error('specifications')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Classification -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-tag"></i> Classification</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                                id="category_id" name="category_id" required onchange="loadSubcategories()">
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="sub_category_id" class="form-label">Sub-category</label>
                                        <select class="form-select @error('sub_category_id') is-invalid @enderror" 
                                                id="sub_category_id" name="sub_category_id">
                                            <option value="">-- Select Sub-category --</option>
                                        </select>
                                        @error('sub_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="brand_id" class="form-label">Brand</label>
                                        <select class="form-select @error('brand_id') is-invalid @enderror" 
                                                id="brand_id" name="brand_id">
                                            <option value="">-- Select Brand --</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="unit_id" class="form-label">Unit of Measurement <span class="text-danger">*</span></label>
                                    <select class="form-select @error('unit_id') is-invalid @enderror" 
                                            id="unit_id" name="unit_id" required>
                                        <option value="">-- Select Unit --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }} ({{ $unit->abbreviation }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-tag-fill"></i> Pricing</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" placeholder="0.00" value="{{ old('price') }}" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="mrp" class="form-label">MRP</label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" step="0.01" class="form-control @error('mrp') is-invalid @enderror" 
                                                   id="mrp" name="mrp" placeholder="0.00" value="{{ old('mrp') }}">
                                        </div>
                                        @error('mrp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="wholesale_price" class="form-label">Wholesale</label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" step="0.01" class="form-control @error('wholesale_price') is-invalid @enderror" 
                                                   id="wholesale_price" name="wholesale_price" placeholder="0.00" value="{{ old('wholesale_price') }}">
                                        </div>
                                        @error('wholesale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="offer_price" class="form-label">Offer</label>
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" step="0.01" class="form-control @error('offer_price') is-invalid @enderror" 
                                                   id="offer_price" name="offer_price" placeholder="0.00" value="{{ old('offer_price') }}">
                                        </div>
                                        @error('offer_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="pricing_type" class="form-label">Pricing Type</label>
                                    <select class="form-select @error('pricing_type') is-invalid @enderror" 
                                            id="pricing_type" name="pricing_type">
                                        <option value="fixed" {{ old('pricing_type', 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="tiered" {{ old('pricing_type') === 'tiered' ? 'selected' : '' }}>Tiered</option>
                                    </select>
                                    @error('pricing_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Inventory -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-box2-heart"></i> Inventory</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                               id="quantity" name="quantity" placeholder="0" value="{{ old('quantity', 0) }}" required>
                                        @error('quantity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" 
                                               id="reorder_level" name="reorder_level" placeholder="0" value="{{ old('reorder_level', 10) }}" required>
                                        @error('reorder_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="active" class="form-label">&nbsp;</label>
                                        <div class="form-check mt-2">
                                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1" 
                                                   {{ old('active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Product
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadSubcategories() {
    const categoryId = document.getElementById('category_id').value;
    const subCategorySelect = document.getElementById('sub_category_id');
    
    subCategorySelect.innerHTML = '<option value="">-- Select Sub-category --</option>';
    
    if (categoryId) {
        fetch(`/products/category/${categoryId}/subcategories`)
            .then(response => response.json())
            .then(data => {
                data.forEach(subCategory => {
                    const option = document.createElement('option');
                    option.value = subCategory.id;
                    option.textContent = subCategory.name;
                    subCategorySelect.appendChild(option);
                });
                @if(old('sub_category_id'))
                    subCategorySelect.value = {{ old('sub_category_id') }};
                @endif
            });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('category_id').value) {
        loadSubcategories();
    }
});
</script>
@endsection
