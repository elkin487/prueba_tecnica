<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hotel>
 */
class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Hotel '.fake()->unique()->company(),
            'address' => fake()->streetAddress(),
            'city_id' => City::factory(),
            'nit' => fake()->unique()->numerify('########-#'),
            'number_of_rooms' => fake()->numberBetween(20, 100),
        ];
    }
}
