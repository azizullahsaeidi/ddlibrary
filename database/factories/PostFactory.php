<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'thread_id'   => Thread::factory(),
            'user_id'     => User::factory(),
            'body'        => $this->faker->paragraph(),
        ];
    }
}
