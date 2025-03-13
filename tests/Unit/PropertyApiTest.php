<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class PropertyApiTest extends TestCase
{
    public function test_create_property()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/properties', [
            'location' => 'New York, NY',
            'price' => 250000,
            'status' => 'Available'
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Property created successfully']);
    }
}
