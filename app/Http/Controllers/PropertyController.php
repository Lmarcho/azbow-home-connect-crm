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
        if ($property->status !== 'Available') {
            return response()->json(['error' => 'Only available properties can be deleted'], 400);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

}
