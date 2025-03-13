<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Reservation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ReservationApiTest extends TestCase
{
    public function test_create_reservation()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/reservations', [
            'lead_id' => 1,
            'property_id' => 2,
            'reservation_fee' => 5000
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Reservation created successfully']);
    }
}

