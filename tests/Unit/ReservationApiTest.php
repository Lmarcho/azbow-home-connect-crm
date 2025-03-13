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
    public function test_create_reservation()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Create a test lead & property
        $lead = Lead::factory()->create([
            'status' => 'Unassigned'
        ]);

        // Assign the lead to an agent before reservation
        $lead->update(['status' => 'Assigned']);

        $property = Property::factory()->create();

        $response = $this->postJson('/api/reservations', [
            'lead_id' => $lead->id,
            'property_id' => $property->id,
            'reservation_fee' => 5000
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Reservation created successfully']);
    }
}

