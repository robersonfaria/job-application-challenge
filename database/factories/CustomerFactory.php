<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->name(),
            "address" => $this->faker->streetAddress(),
            "checked" => $this->faker->boolean(),
            "description" => $this->faker->text(),
            "interest" => $this->faker->text(),
            "date_of_birth" => $this->faker->date(),
            "email" => $this->faker->email(),
            "account" => $this->faker->randomNumber(9),
        ];
    }

    public function acceptedAge()
    {
        return $this->state(function (array $attributes) {
            return [
                "date_of_birth" => $this->faker->dateTimeBetween( '-64 years', '-19 years')
            ];
        });
    }
}
