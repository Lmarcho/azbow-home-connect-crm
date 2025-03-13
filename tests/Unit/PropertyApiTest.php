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

    /**
     * Test Get All Properties
     */
    public function test_get_all_properties()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        Property::factory()->count(3)->create();

        $response = $this->getJson('/api/properties');

        $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => ['id', 'location', 'price', 'status', 'created_at', 'updated_at']
        ]);
    }

    public function test_sales_agent_can_retrieve_properties()
    {
        $agent = User::factory()->create(['role' => 'sales_agent']);
        $this->actingAs($agent);

        Property::factory()->count(3)->create();

        $response = $this->getJson('/api/properties');

        $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => ['id', 'location', 'price', 'status', 'created_at', 'updated_at']
        ]);
    }


    /**
     * Test Admin Can Update a Property
     */
    public function test_admin_can_update_property()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $property = Property::factory()->create(['location' => 'Initial Location']);

        $response = $this->putJson("/api/properties/{$property->id}", [
            'location' => 'Updated Location',
            'price' => 450000,
            'status'=> 'Sold'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Property updated successfully',
                'property' => ['location' => 'Updated Location', 'price' => 450000]
            ]);
    }

    /**
     * Test Sales Agent Cannot Update a Property
     */
    public function test_sales_agent_cannot_update_property()
    {
        $agent = User::factory()->create(['role' => 'sales_agent']);
        $this->actingAs($agent);

        $property = Property::factory()->create();

        $response = $this->putJson("/api/properties/{$property->id}", [
            'location' => 'Unauthorized Update'
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test Admin Can Delete a Property
     */
    public function test_admin_can_delete_property()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $property = Property::factory()->create(['status' => 'Available']);

        $response = $this->deleteJson("/api/properties/{$property->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Property deleted successfully']);
    }

    /**
     * Test Sales Agent Cannot Delete a Property
     */
    public function test_sales_agent_cannot_delete_property()
    {
        $agent = User::factory()->create(['role' => 'sales_agent']);
        $this->actingAs($agent);

        $property = Property::factory()->create(['status' => 'Available']);

        $response = $this->deleteJson("/api/properties/{$property->id}");

        $response->assertStatus(403);
    }
}
