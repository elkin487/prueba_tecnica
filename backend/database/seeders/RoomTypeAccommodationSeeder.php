<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

/**
 * Define las combinaciones VÁLIDAS de tipo de habitación y acomodación,
 * según las reglas del negocio:
 *
 *   Estándar -> Sencilla, Doble
 *   Junior   -> Triple, Cuádruple
 *   Suite    -> Sencilla, Doble, Triple
 *
 * Debe ejecutarse después de RoomTypeSeeder y AccommodationSeeder.
 */
class RoomTypeAccommodationSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            'Estándar' => ['Sencilla', 'Doble'],
            'Junior'   => ['Triple', 'Cuádruple'],
            'Suite'    => ['Sencilla', 'Doble', 'Triple'],
        ];

        foreach ($rules as $roomTypeName => $accommodationNames) {
            $roomType = RoomType::where('name', $roomTypeName)->firstOrFail();

            $accommodationIds = Accommodation::whereIn('name', $accommodationNames)
                ->pluck('id')
                ->all();

            // syncWithoutDetaching mantiene la idempotencia sin eliminar relaciones existentes.
            $roomType->accommodations()->syncWithoutDetaching($accommodationIds);
        }
    }
}
