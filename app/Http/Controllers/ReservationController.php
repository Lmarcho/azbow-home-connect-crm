<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Lead;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['lead', 'property']);

        if ($request->has('financial_status')) {
            $query->where('financial_status', $request->financial_status);
        }

        if ($request->has('legal_status')) {
            $query->where('legal_status', $request->legal_status);
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'property_id' => 'required|exists:properties,id',
            'reservation_fee' => 'nullable|numeric',
        ]);

        $lead = Lead::find($request->lead_id);

        if ($lead->status !== 'Assigned') {
            return response()->json(['error' => 'Lead must be in Assigned state to create a reservation'], 400);
        }

        $reservation = Reservation::create([
            'lead_id' => $request->lead_id,
            'property_id' => $request->property_id,
            'reservation_fee' => $request->reservation_fee,
            'financial_status' => 'Pending',
            'legal_status' => 'Pending',
            'sale_status' => 'Reserved',
        ]);

        $lead->update(['status' => 'Reserved']);

        return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation], 201);

    }

    /**
     * Approve or Reject Financials
     */
    public function approveFinancials(Request $request, Reservation $reservation)
    {
        // Ensure only Admins Can Approve Financials
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Ensure reservation is in "Reserved" state
        if ($reservation->sale_status !== 'Reserved') {
            return response()->json(['error' => 'Reservation must be in Reserved status'], 400);
        }

        // Ensure financial approval is only processed once
        if ($reservation->financial_status !== 'Pending') {
            return response()->json(['error' => 'Financial status is already processed'], 400);
        }

        $request->validate([
            'financial_status' => 'required|in:Approved,Rejected',
        ]);

        $reservation->update(['financial_status' => $request->financial_status]);

        if ($request->financial_status === 'Rejected') {
            $reservation->lead->update(['status' => 'Assigned']);
        } else {
            $reservation->lead->update(['status' => 'Financial Approved']);
        }

        return response()->json(['message' => "Financial status updated to {$request->financial_status}"]);

    }


    /**
     * Finalize Legal Process
     */
    public function finalizeLegal(Request $request, Reservation $reservation)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($reservation->financial_status !== 'Approved') {
            return response()->json(['error' => 'Financials must be approved before finalizing legal process'], 400);
        }

        if ($reservation->legal_status === 'Finalized') {
            return response()->json(['error' => 'Legal process is already finalized'], 400);
        }

        $reservation->update(['legal_status' => 'Finalized']);
        $reservation->lead->update(['status' => 'Legal Finalized']);

        return response()->json(['message' => 'Legal process finalized successfully']);
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
