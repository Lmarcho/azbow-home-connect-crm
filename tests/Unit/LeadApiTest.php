<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\User;
use App\Models\LeadStatusLog;
use Laravel\Sanctum\Sanctum;

class LeadApiTest extends TestCase
{
    public function test_create_lead()
    {
        $user = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/leads', [
            'name' => 'Test Lead',
            'contact_info' => 'test@example.com',
            'source' => 'Zillow'
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Lead created successfully']);
    }

    public function test_get_lead_by_id()
    {
        $user = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($user);

        // ✅ Ensure the lead is assigned to the test user
        $lead = Lead::factory()->create(['assigned_agent_id' => $user->id]);

        $response = $this->getJson("/api/leads/{$lead->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $lead->id]);
    }

    public function test_progress_lead_logs_status_change()
    {
        $user = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($user);

        // ✅ Ensure the lead is assigned to the test user
        $lead = Lead::factory()->create([
            'status' => 'Assigned',
            'assigned_agent_id' => $user->id
        ]);

        $response = $this->putJson("/api/leads/{$lead->id}/progress");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Lead moved to Reserved']);

        // ✅ Verify LeadStatusLog entry
        $this->assertDatabaseHas('lead_status_logs', [
            'lead_id' => $lead->id,
            'previous_status' => 'Assigned',
            'new_status' => 'Reserved',
            'changed_by' => $user->id
        ]);
    }
}
