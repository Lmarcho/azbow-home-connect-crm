<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Retrieve all properties (with optional filtering)
     */
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    /**
     * Store (Create) a new property
     */
    public function store(Request $request)
    {
        //  Only Admins Can Create Properties
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Reserved,Sold',
        ]);

        $property = Property::create($request->all());

        return response()->json(['message' => 'Property created successfully', 'property' => $property], 201);
    }

    /**
     * Delete a property
     */
    public function destroy(Property $property)
    {

        //  Only Admins Can Delete Properties
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($property->status !== 'Available') {
            return response()->json(['error' => 'Only available properties can be deleted'], 400);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }

    /**
     * Retrieve a single property by ID
     */
    public function show($id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        return response()->json($property);
    }

    /**
     * Update an existing property (Admin Only)
     */
    public function update(Request $request, Property $property)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:Available,Reserved,Sold',
        ]);

        $property->update($request->only(['location', 'price', 'status']));

        return response()->json(['message' => 'Property updated successfully', 'property' => $property]);
    }

}
