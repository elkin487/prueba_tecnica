<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hoteles de la compañía con sus datos básicos y tributarios.
 *
 * Reglas de unicidad (criterio "no deben existir hoteles repetidos"):
 *  - name único
 *  - nit único
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address');
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->string('nit')->unique();
            $table->unsignedInteger('number_of_rooms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
