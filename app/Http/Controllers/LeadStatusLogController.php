<?php

namespace App\Http\Controllers;

use App\Models\LeadStatusLog;
use Illuminate\Http\Request;

class LeadStatusLogController extends Controller
{
    /**
     * Retrieve status change history for a lead.
     */
    public function index($lead_id)
    {
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
