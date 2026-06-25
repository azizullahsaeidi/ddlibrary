<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'title'       => $this->faker->sentence(),
            'body'        => $this->faker->paragraphs(2, true),
            'is_locked'   => false,
            'is_pinned'   => false,
            'last_post_at' => null,
        ];
    }

    public function locked(): static
    {
        return $this->state(['is_locked' => true]);
    }

    public function pinned(): static
    {
        return $this->state(['is_pinned' => true]);
    }
}