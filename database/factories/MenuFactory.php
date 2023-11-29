<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name(),
            'location' => $this->faker->address(),
            'path' => $this->faker->url(),
            'language' => 'en',
            'weight' => $this->faker->numberBetween(1, 200),
        ];
    }
}
