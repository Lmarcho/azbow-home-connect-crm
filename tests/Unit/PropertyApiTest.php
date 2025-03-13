<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class PropertyApiTest extends TestCase
{
    public function test_admin_can_create_property()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/properties', [
            'location' => 'Havelock Road, Colombo 05',
            'price' => 250000,
            'status' => 'Available'
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Property created successfully']);
    }

    public function test_sales_agent_cannot_create_property()
    {
        $agent = User::factory()->create(['role' => 'sales_agent']);
        Sanctum::actingAs($agent);

        $response = $this->postJson('/api/properties', [
            'location' => 'Park Rd, Colombo 05',
            'price' => 350000,
            'status' => 'Available'
        ]);

        $response->assertStatus(403); // Expect Forbidden
    }
}
