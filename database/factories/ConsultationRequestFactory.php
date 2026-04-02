<?php

namespace Database\Factories;

use App\Models\ConsultationRequest;
use App\Models\Dietitian;
use App\Models\UserExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsultationRequest>
 */
class ConsultationRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_experience_id' => UserExperience::factory(),
            'dietitian_id' => Dietitian::factory(),
            'preferred_date' => fake()->dateTimeBetween('now', '+2 weeks'),
            'note' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
