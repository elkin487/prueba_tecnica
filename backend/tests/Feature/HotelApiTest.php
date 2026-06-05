<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\City;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de la API REST de hoteles, incluyendo los criterios de aceptación
 * de unicidad (CA-2) y capacidad.
 */
class HotelApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'DECAMERON CARTAGENA',
            'address' => 'CALLE 23 58-25',
            'city_id' => City::value('id'),
            'nit' => '12345678-9',
            'number_of_rooms' => 42,
        ], $overrides);
    }

    public function test_lista_los_hoteles(): void
    {
        Hotel::factory()->count(3)->create();

        $this->getJson('/api/hotels')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'name', 'address', 'nit', 'number_of_rooms', 'configured_rooms', 'available_rooms']],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_crea_un_hotel(): void
    {
        $this->postJson('/api/hotels', $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'DECAMERON CARTAGENA')
            ->assertJsonPath('data.available_rooms', 42);

        $this->assertDatabaseHas('hotels', ['nit' => '12345678-9']);
    }

    public function test_no_permite_nombre_repetido(): void
    {
        Hotel::factory()->create(['name' => 'DECAMERON CARTAGENA']);

        $this->postJson('/api/hotels', $this->validPayload(['nit' => '000-1']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_no_permite_nit_repetido(): void
    {
        Hotel::factory()->create(['nit' => '12345678-9']);

        $this->postJson('/api/hotels', $this->validPayload(['name' => 'OTRO HOTEL']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('nit');
    }

    public function test_valida_los_campos_requeridos(): void
    {
        $this->postJson('/api/hotels', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'address', 'city_id', 'nit', 'number_of_rooms']);
    }

    public function test_muestra_un_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $this->getJson("/api/hotels/{$hotel->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $hotel->id);
    }

    public function test_actualiza_un_hotel(): void
    {
        $hotel = Hotel::factory()->create(['number_of_rooms' => 50]);

        $this->putJson("/api/hotels/{$hotel->id}", $this->validPayload([
            'name' => 'NUEVO NOMBRE',
            'number_of_rooms' => 60,
        ]))
            ->assertOk()
            ->assertJsonPath('data.name', 'NUEVO NOMBRE');

        $this->assertDatabaseHas('hotels', ['id' => $hotel->id, 'name' => 'NUEVO NOMBRE', 'number_of_rooms' => 60]);
    }

    public function test_no_permite_reducir_habitaciones_por_debajo_de_lo_configurado(): void
    {
        $hotel = Hotel::factory()->create(['number_of_rooms' => 42]);
        $estandar = RoomType::where('name', 'Estándar')->firstOrFail();
        $sencilla = Accommodation::where('name', 'Sencilla')->firstOrFail();
        $hotel->rooms()->create([
            'room_type_id' => $estandar->id,
            'accommodation_id' => $sencilla->id,
            'quantity' => 25,
        ]);

        $this->putJson("/api/hotels/{$hotel->id}", [
            'name' => $hotel->name,
            'address' => $hotel->address,
            'city_id' => $hotel->city_id,
            'nit' => $hotel->nit,
            'number_of_rooms' => 20,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('number_of_rooms');
    }

    public function test_elimina_un_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $this->deleteJson("/api/hotels/{$hotel->id}")->assertNoContent();

        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }
}
