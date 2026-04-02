<?php

namespace Database\Factories;

use App\Models\Dietitian;
use App\Models\FeedbackRequest;
use App\Models\UserExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeedbackRequest>
 */
class FeedbackRequestFactory extends Factory
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
            'title' => fake()->sentence(4),
            'topic' => fake()->randomElement(['progress', 'nutrition', 'meal plan']),
            'tag' => 'general',
            'tag_tone' => 'slate',
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => fake()->randomElement(['pending', 'in-progress', 'completed']),
            'message' => fake()->paragraph(),
            'recommendations' => [fake()->sentence(), fake()->sentence()],
            'is_read' => false,
            'submitted_on' => fake()->date(),
        ];
    }
}
