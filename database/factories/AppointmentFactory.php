<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence(),
            'client_id' => Client::factory()->create()->id,
            'description' => fake()->paragraph(),
            'start_time' => $startTime = fake()->dateTimeBetween('-1 year', '+1 year'),
            'end_time' => Carbon::parse($startTime)->addHours(2),
            'status' => rand(1, 3),
        ];
    }
}
