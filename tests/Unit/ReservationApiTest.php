<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Lead;
use App\Models\Property;

class ReservationApiTest extends TestCase
{
    public function test_sales_agent_can_create_reservation()
    {
        $agent = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($agent);

        // ✅ Ensure the lead is assigned to the sales agent
        $lead = Lead::factory()->create(['assigned_agent_id' => $agent->id, 'status' => 'Assigned']);

        $property = Property::factory()->create();

        $response = $this->postJson('/api/reservations', [
            'lead_id' => $lead->id,
            'property_id' => $property->id,
            'reservation_fee' => 5000
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Reservation created successfully']);
    }
    public function test_admin_cannot_create_reservation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $lead = Lead::factory()->create();
        $property = Property::factory()->create();

        $response = $this->postJson('/api/reservations', [
            'lead_id' => $lead->id,
            'property_id' => $property->id,
            'reservation_fee' => 5000
        ]);

        $response->assertStatus(403); // Expect Forbidden
    }

    public function test_admin_can_approve_financials()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $reservation = Reservation::factory()->create([
            'financial_status' => 'Pending',
            'sale_status' => 'Reserved'
        ]);

        $response = $this->putJson("/api/reservations/{$reservation->id}/approve-financials", [
            'financial_status' => 'Approved'
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Financial status updated to Approved']);
    }

    public function test_sales_agent_cannot_approve_financials()
    {
        $agent = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($agent);

        $reservation = Reservation::factory()->create(['financial_status' => 'Pending']);

        $response = $this->putJson("/api/reservations/{$reservation->id}/approve-financials", [
            'financial_status' => 'Approved'
        ]);

        $response->assertStatus(403); // Expect Forbidden
    }

    public function test_financial_approval_fails_without_reservation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        // Ensure reservation is **NOT** in "Reserved" status
        $reservation = Reservation::factory()->create([
            'financial_status' => 'Pending',
            'sale_status' => 'Pending' // Not Reserved
        ]);

        $response = $this->putJson("/api/reservations/{$reservation->id}/approve-financials", [
            'financial_status' => 'Approved'
        ]);

        $response->assertStatus(400) // Expect Business Logic Error
        ->assertJson(['error' => 'Reservation must be in Reserved status']);
    }

    public function test_legal_finalization_fails_without_financial_approval()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        //Ensure financials are NOT approved
        $reservation = Reservation::factory()->create(['financial_status' => 'Pending', 'legal_status' => 'Pending']);

        $response = $this->putJson("/api/reservations/{$reservation->id}/finalize-legal");

        $response->assertStatus(400) // Expect Business Logic Error
        ->assertJson(['error' => 'Financials must be approved before finalizing legal process']);
    }


}

