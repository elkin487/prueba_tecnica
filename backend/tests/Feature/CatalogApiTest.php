<?php

namespace Tests\Feature;

use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de los endpoints de catálogos (solo lectura).
 */
class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_lista_ciudades(): void
    {
        $this->getJson('/api/cities')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    public function test_lista_tipos_de_habitacion(): void
    {
        $this->getJson('/api/room-types')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    public function test_lista_acomodaciones(): void
    {
        $this->getJson('/api/accommodations')
            ->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    public function test_devuelve_acomodaciones_validas_por_tipo(): void
    {
        $estandar = RoomType::where('name', 'Estándar')->firstOrFail();

        $response = $this->getJson("/api/room-types/{$estandar->id}/accommodations")->assertOk();

        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertEqualsCanonicalizing(['Sencilla', 'Doble'], $names);
    }
}
