<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

/**
 * Catálogo de tipos de habitación: Estándar, Junior, Suite.
 */
class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Estándar', 'Junior', 'Suite'] as $name) {
            RoomType::firstOrCreate(['name' => $name]);
        }
    }
}
