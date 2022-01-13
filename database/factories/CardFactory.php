<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "type" => $this->faker->creditCardType,
            "number" => $this->faker->creditCardNumber,
            "name" => $this->faker->name,
            "expirationDate" => $this->faker->creditCardExpirationDateString(null, 'm/y'),
        ];
    }
}
