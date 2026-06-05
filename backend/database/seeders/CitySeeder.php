<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

/**
 * Catálogo de ciudades. Datos de referencia (no administrables vía UI).
 * Idempotente: puede ejecutarse varias veces sin duplicar registros.
 */
class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Cartagena',
            'Bogotá',
            'Medellín',
            'Cali',
            'Barranquilla',
            'Santa Marta',
            'San Andrés',
            'Pereira',
        ];

        foreach ($cities as $name) {
            City::firstOrCreate(['name' => $name]);
        }
    }
}
