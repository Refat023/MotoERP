<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subCategories = SubCategory::with('category')->withCount('products')->paginate(10);
        return view('subcategories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // Check if subcategory name is unique within the category
        $existing = SubCategory::where('category_id', $validated['category_id'])
            ->where('name', $validated['name'])
            ->first();

        if ($existing) {
            return back()->withErrors(['name' => 'This subcategory already exists in the selected category.']);
        }

        $validated['slug'] = Str::slug($validated['name']);
        SubCategory::create($validated);

        return redirect()->route('subcategories.index')->with('success', 'Subcategory created successfully.');
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('subcategories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        // Check if subcategory name is unique within the category (excluding current)
        $existing = SubCategory::where('category_id', $validated['category_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $subCategory->id)
            ->first();

        if ($existing) {
            return back()->withErrors(['name' => 'This subcategory already exists in the selected category.']);
        }

        $validated['slug'] = Str::slug($validated['name']);
        $subCategory->update($validated);

        return redirect()->route('subcategories.index')->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(SubCategory $subCategory)
    {
        if ($subCategory->products()->count() > 0) {
            return redirect()->route('subcategories.index')->with('error', 'Cannot delete subcategory with products.');
        }

        $subCategory->delete();
        return redirect()->route('subcategories.index')->with('success', 'Subcategory deleted successfully.');
    }
}
