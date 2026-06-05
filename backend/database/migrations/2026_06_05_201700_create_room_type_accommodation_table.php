<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla pivote que define las combinaciones VÁLIDAS de tipo de habitación
 * y acomodación. Modelar la regla como datos (y no como condicionales en
 * código) permite extenderla sin tocar la lógica de la aplicación.
 *
 *   Estándar -> Sencilla, Doble
 *   Junior   -> Triple, Cuádruple
 *   Suite    -> Sencilla, Doble, Triple
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_type_accommodation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('accommodation_id')->constrained('accommodations')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['room_type_id', 'accommodation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_type_accommodation');
    }
};
