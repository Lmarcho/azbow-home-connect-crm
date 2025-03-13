<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lead;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'contact_info' => $this->faker->email,
            'source' => $this->faker->randomElement(['Zillow', 'Realtor.com', 'Google Ads', 'Facebook Ads', 'Landing Page']),
        ];
    }
}
