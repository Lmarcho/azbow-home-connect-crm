<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lead;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class LeadApiTest extends TestCase
{
    public function test_create_lead()
    {
        $user = User::factory()->create();
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
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $lead = Lead::factory()->create();

        $response = $this->getJson("/api/leads/{$lead->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $lead->id]);
    }
}
