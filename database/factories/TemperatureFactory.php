<?php

namespace Database\Factories;

use App\Models\Temperature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Temperature>
 */
class TemperatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Temperature::class;

    public function definition(): array
    {
        return [
            'temperature' => $this->faker->randomFloat(2, 20, 40), // Suhu antara 20-40°C
            'humidity' => $this->faker->randomFloat(2, 30, 90), // Kelembaban 30-90%
            'air' => $this->faker->randomFloat(2, 200, 600), // Kelembaban 30-90%
            'status' => $this->faker->randomElement(['Normal', 'Detected']),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
