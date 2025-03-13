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

    public function test_admin_can_assign_lead()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $lead = Lead::factory()->create(['status' => 'Unassigned']);

        $agent = User::factory()->create(['role' => 'sales_agent']);

        $response = $this->putJson("/api/leads/{$lead->id}/assign", [
            'assigned_agent_id' => $agent->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Lead assigned successfully']);

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'assigned_agent_id' => $agent->id,
            'status' => 'Assigned',
        ]);
    }

    public function test_sales_agent_can_qualify_lead()
    {
        $agent = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($agent);

        $lead = Lead::factory()->create([
            'assigned_agent_id' => $agent->id,
            'status' => 'Assigned'
        ]);

        // Data to update the lead qualification details
        $data = [
            'budget' => 250000,
            'location_preference' => 'Downtown',
            'property_interests' => '2-bedroom apartments, sea view'
        ];

        // Send PUT request to qualify the lead
        $response = $this->putJson("/api/leads/{$lead->id}/qualify", $data);

        // Assertions
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Lead qualification updated successfully',
                'lead' => [
                    'id' => $lead->id,
                    'budget' => 250000,
                    'location_preference' => 'Downtown',
                    'property_interests' => '2-bedroom apartments, sea view'
                ]
            ]);

        // Ensure database is updated
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'budget' => 250000,
            'location_preference' => 'Downtown',
            'property_interests' => '2-bedroom apartments, sea view'
        ]);
    }


    public function test_get_lead_by_id()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);

        // Ensure the lead is assigned to the test user
        $lead = Lead::factory()->create(['assigned_agent_id' => $user->id]);

        $response = $this->getJson("/api/leads/{$lead->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $lead->id]);
    }

    public function test_progress_lead_logs_status_change()
    {
        $user = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($user);

        // Ensure the lead is assigned to the test user
        $lead = Lead::factory()->create([
            'status' => 'Assigned',
            'assigned_agent_id' => $user->id
        ]);

        $response = $this->putJson("/api/leads/{$lead->id}/progress");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Lead moved to Reserved']);

        // Verify LeadStatusLog entry
        $this->assertDatabaseHas('lead_status_logs', [
            'lead_id' => $lead->id,
            'previous_status' => 'Assigned',
            'new_status' => 'Reserved',
            'changed_by' => $user->id
        ]);
    }

    public function test_lead_creation_fails_without_source()
    {
        $user = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/leads', [
            'name' => 'Test Lead',
            'contact_info' => 'test@example.com'
        ]);

        $response->assertStatus(422) // Expect Validation Error
        ->assertJsonValidationErrors(['source']);
    }

    public function test_unassigned_lead_cannot_be_progressed()
    {
        $user = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($user);

        $lead = Lead::factory()->create(['status' => 'Unassigned']);

        $response = $this->putJson("/api/leads/{$lead->id}/progress");

        $response->assertStatus(403)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_lead_cannot_be_assigned_twice()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $agent = User::factory()->create(['role' => 'sales_agent']);
        $lead = Lead::factory()->create(['status' => 'Assigned', 'assigned_agent_id' => $agent->id]);

        $response = $this->putJson("/api/leads/{$lead->id}/assign", [
            'assigned_agent_id' => $agent->id
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Only unassigned leads can be assigned']);
    }



}
