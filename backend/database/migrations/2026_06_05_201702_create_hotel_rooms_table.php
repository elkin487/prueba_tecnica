<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Configuración de habitaciones de cada hotel:
 * cantidad de habitaciones de un tipo y acomodación determinados.
 *
 * La restricción única (hotel_id, room_type_id, accommodation_id) cumple el
 * criterio "no debe existir tipos de habitaciones y acomodaciones repetidas
 * para el mismo hotel".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->restrictOnDelete();
            $table->foreignId('accommodation_id')->constrained('accommodations')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->unique(['hotel_id', 'room_type_id', 'accommodation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
