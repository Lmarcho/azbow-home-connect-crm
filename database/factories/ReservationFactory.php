<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        return [
            'lead_id' => Lead::factory(),
            'property_id' => Property::factory(),
            'reservation_fee' => $this->faker->numberBetween(1000, 10000),
            'financial_status' => 'Pending',
            'legal_status' => 'Pending',
            'sale_status' => $this->faker->randomElement(['Pending', 'Reserved', 'Sold']),
        ];
    }
}

