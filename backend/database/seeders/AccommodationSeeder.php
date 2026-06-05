<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;

/**
 * Catálogo de acomodaciones: Sencilla, Doble, Triple, Cuádruple.
 */
class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Sencilla', 'Doble', 'Triple', 'Cuádruple'] as $name) {
            Accommodation::firstOrCreate(['name' => $name]);
        }
    }
}
