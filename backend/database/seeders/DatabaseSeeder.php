<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Carga los catálogos base del sistema. El orden importa: las combinaciones
 * válidas (RoomTypeAccommodationSeeder) dependen de tipos y acomodaciones.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CitySeeder::class,
            RoomTypeSeeder::class,
            AccommodationSeeder::class,
            RoomTypeAccommodationSeeder::class,
        ]);
    }
}
