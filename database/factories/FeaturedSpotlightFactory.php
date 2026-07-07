<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FeaturedSpotlightFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'subtitle' => $this->faker->sentence(8),
            'image_path' => null,
            'link_url' => $this->faker->url(),
            'link_text' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(['news', 'resource', 'collection', 'external']),
            'active' => true,
            'starts_at' => null,
            'ends_at' => null,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }

    public function notYetStarted(): static
    {
        return $this->state(['starts_at' => now()->addDay()]);
    }

    public function expired(): static
    {
        return $this->state(['ends_at' => now()->subDay()]);
    }
}