<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category', 'subCategory', 'brand', 'unit')
            ->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();
        
        return view('products.create', compact('categories', 'brands', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products',
            'barcode' => 'nullable|string|unique:products',
            
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            
            'price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'pricing_type' => 'in:fixed,tiered',
            
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|string',
            'active' => 'boolean',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();
        $subCategories = $product->category_id ? 
            SubCategory::where('category_id', $product->category_id)->get() : 
            collect();
        
        return view('products.edit', compact('product', 'categories', 'brands', 'units', 'subCategories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            
            'price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'pricing_type' => 'in:fixed,tiered',
            
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function getSubCategories($categoryId)
    {
        $subCategories = SubCategory::where('category_id', $categoryId)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        
        return response()->json($subCategories);
    }
}
