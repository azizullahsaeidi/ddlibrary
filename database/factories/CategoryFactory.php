<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence(),
            'parent_id'   => null,
            'order'       => $this->faker->numberBetween(0, 10),
            'is_private'  => false,
        ];
    }

    public function private(): static
    {
        return $this->state(['is_private' => true]);
    }
}