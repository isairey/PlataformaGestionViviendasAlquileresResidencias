<?php

namespace App\Http\Controllers;

use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');

        $query = Property::query();

        if ($type) {
            $query->where('type', $type);
        }

        $properties = $query->paginate(12); // Or however many per page

        return view('landlord.property.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('landlord.property.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_column(PropertyType::cases(), 'value'))],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
        ]);

        // Assuming authenticated landlord is creating this
        $landlordId = auth()->user()->landlord->id;

        // Create new property
        Property::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'landlord_id' => $landlordId,
        ]);

        // Redirect with success message
        return redirect()->route('property.index')->with('toast.success', [
            'title' => 'Property Created',
            'content' => 'The property has been successfully created.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $property = Property::findOrFail($id);

        return view('landlord.property.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(array_column(PropertyType::cases(), 'value'))],
            'address' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $property->update($validated);

        return redirect()->route('property.index')->with('toast.success', [
            'title' => 'Property Updated',
            'content' => 'The property has been successfully updated.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('property.index')->with('toast.success', [
            'title' => 'Property Deleted',
            'content' => 'The property has been successfully deleted.',
        ]);
    }
}
