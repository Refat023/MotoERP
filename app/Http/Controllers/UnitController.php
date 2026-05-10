<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('products')->paginate(10);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'abbreviation' => 'required|string|max:10|unique:units',
            'description' => 'nullable|string',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'active' => 'boolean',
        ]);

        $validated['conversion_factor'] = $validated['conversion_factor'] ?? 1;
        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'abbreviation' => 'required|string|max:10|unique:units,abbreviation,' . $unit->id,
            'description' => 'nullable|string',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'active' => 'boolean',
        ]);

        $validated['conversion_factor'] = $validated['conversion_factor'] ?? 1;
        $unit->update($validated);

        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->count() > 0) {
            return redirect()->route('units.index')->with('error', 'Cannot delete unit with products.');
        }

        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
