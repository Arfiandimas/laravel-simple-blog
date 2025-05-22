<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'content' => fake()->text($maxNbChars = 1000),
            'publish_date' => fake()->dateTimeBetween('-5 days', '+5 days')->format('Y-m-d'),
            'is_draft' => fake()->boolean(30),
            'created_by' => User::factory()
        ];
    }

    public function withCreator($userId)
    {
        return $this->state(fn (array $attributes) => [
            'created_by' => $userId,
        ]);
    }
}
