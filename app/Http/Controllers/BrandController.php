<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->paginate(10);
        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);
        Brand::create($validated);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo && \Storage::disk('public')->exists($brand->logo)) {
                \Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);
        $brand->update($validated);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return redirect()->route('brands.index')->with('error', 'Cannot delete brand with products.');
        }

        // Delete logo if exists
        if ($brand->logo && \Storage::disk('public')->exists($brand->logo)) {
            \Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
}
