<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class LeadController extends Controller
{

    /**
     * Retrieve Leads with Filtering
     */
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('assigned_agent_id')) {
            $query->where('assigned_agent_id', $request->assigned_agent_id);
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'source' => 'required|in:Zillow,Realtor.com,Google Ads,Facebook Ads,Landing Page',
        ]);

        $lead = Lead::create([
            'name' => $request->name,
            'contact_info' => $request->contact_info,
            'source' => $request->source,
        ]);

        return response()->json(['message' => 'Lead created successfully', 'lead' => $lead], 201);
    }

    /**
     * Assign a Lead to a Sales Agent
     */
        public function assign(Request $request, Lead $lead)
    {
        if ($lead->status !== 'Unassigned') {
            return response()->json(['error' => 'Only unassigned leads can be assigned'], 400);
        }

        $request->validate(['assigned_agent_id' => 'required|exists:users,id']);

        $lead->update(['assigned_agent_id' => $request->assigned_agent_id, 'status' => 'Assigned']);

        LeadStatusLog::create([
            'lead_id' => $lead->id,
            'previous_status' => 'Unassigned',
            'new_status' => 'Assigned',
            'changed_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Lead assigned successfully', 'lead' => $lead]);
    }

    /**
     * Progress a Lead through Lifecycle
     */
    public function progress(Request $request, Lead $lead)
    {
        $validTransitions = [
            'Assigned' => 'Reserved',
            'Reserved' => 'Financial Approved',
            'Financial Approved' => 'Legal Finalized',
            'Legal Finalized' => 'Sold',
        ];

        if (!isset($validTransitions[$lead->status])) {
            return response()->json(['error' => 'Invalid lead status transition'], 400);
        }

        $newStatus = $validTransitions[$lead->status];
        $lead->update(['status' => $newStatus]);

        LeadStatusLog::create([
            'lead_id' => $lead->id,
            'previous_status' => $lead->status,
            'new_status' => $newStatus,
            'changed_by' => Auth::id(),
        ]);

        return response()->json(['message' => "Lead moved to $newStatus", 'lead' => $lead]);
    }


    /**
     * Cancel a Lead (Only allowed in Reservation stage)
     */
    public function cancel(Request $request, Lead $lead)
    {
        if ($lead->status !== 'Reserved') {
            return response()->json(['error' => 'Leads can only be canceled during the reservation stage'], 400);
        }

        $request->validate(['reason' => 'required|string']);

        $lead->update(['status' => 'Unassigned']);

        LeadStatusLog::create([
            'lead_id' => $lead->id,
            'previous_status' => 'Reserved',
            'new_status' => 'Unassigned',
            'changed_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Lead reservation canceled', 'lead' => $lead]);
    }

    /**
     * Get a Single Lead by ID
     */
    public function show(string $id)
    {
        $lead = Lead::with(['agent', 'reservations', 'statusLogs'])->find($id);

        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        return response()->json($lead);
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
