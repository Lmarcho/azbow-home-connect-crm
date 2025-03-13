<?php

namespace App\Http\Controllers;

use App\Models\LeadStatusLog;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadStatusLogController extends Controller
{
    /**
     * Retrieve status change history for a lead.
     */
    public function index($lead_id)
    {
        $lead = Lead::find($lead_id);

        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        //  Ensure only assigned agent or admin can view logs
        if (!auth()->user()->isAdmin() && auth()->id() !== $lead->assigned_agent_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $logs = LeadStatusLog::where('lead_id', $lead_id)
            ->orderBy('changed_at', 'desc')
            ->get();

        if ($logs->isEmpty()) {
            return response()->json(['message' => 'No status changes recorded for this lead'], 404);
        }

        return response()->json($logs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
