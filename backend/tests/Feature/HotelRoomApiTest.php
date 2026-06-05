<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Pruebas de la API de configuración de habitaciones, cubriendo los criterios
 * de aceptación:
 *  - CA-1: la suma de cantidades no supera el máximo del hotel.
 *  - CA-3: no se repite el par (tipo, acomodación) en un hotel.
 *  - Combinación válida tipo <-> acomodación.
 *  - Aislamiento del recurso anidado (scoped binding).
 */
class HotelRoomApiTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;

    private RoomType $estandar;

    private Accommodation $sencilla;

    private Accommodation $doble;

    private Accommodation $triple;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->hotel = Hotel::factory()->create(['number_of_rooms' => 42]);
        $this->estandar = RoomType::where('name', 'Estándar')->firstOrFail();
        $this->sencilla = Accommodation::where('name', 'Sencilla')->firstOrFail();
        $this->doble = Accommodation::where('name', 'Doble')->firstOrFail();
        $this->triple = Accommodation::where('name', 'Triple')->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function addRoom(array $data): TestResponse
    {
        return $this->postJson("/api/hotels/{$this->hotel->id}/rooms", $data);
    }

    public function test_agrega_una_configuracion_valida(): void
    {
        $this->addRoom([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 25,
        ])
            ->assertCreated()
            ->assertJsonPath('data.quantity', 25);

        $this->assertDatabaseHas('hotel_rooms', [
            'hotel_id' => $this->hotel->id,
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 25,
        ]);
    }

    public function test_rechaza_combinacion_invalida(): void
    {
        // Estándar no admite Triple.
        $this->addRoom([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->triple->id,
            'quantity' => 5,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('accommodation_id');
    }

    public function test_rechaza_par_tipo_acomodacion_duplicado(): void
    {
        $this->hotel->rooms()->create([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 10,
        ]);

        $this->addRoom([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 5,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('accommodation_id');
    }

    public function test_rechaza_cuando_supera_el_maximo(): void
    {
        $this->hotel->rooms()->create([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 25,
        ]);

        // 25 + 20 = 45 > 42.
        $this->addRoom([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->doble->id,
            'quantity' => 20,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('quantity');
    }

    public function test_actualiza_una_configuracion(): void
    {
        $room = $this->hotel->rooms()->create([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 10,
        ]);

        $this->putJson("/api/hotels/{$this->hotel->id}/rooms/{$room->id}", [
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 30,
        ])
            ->assertOk()
            ->assertJsonPath('data.quantity', 30);
    }

    public function test_elimina_una_configuracion(): void
    {
        $room = $this->hotel->rooms()->create([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 10,
        ]);

        $this->deleteJson("/api/hotels/{$this->hotel->id}/rooms/{$room->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('hotel_rooms', ['id' => $room->id]);
    }

    public function test_no_accede_a_una_habitacion_de_otro_hotel(): void
    {
        $otroHotel = Hotel::factory()->create();
        $room = $otroHotel->rooms()->create([
            'room_type_id' => $this->estandar->id,
            'accommodation_id' => $this->sencilla->id,
            'quantity' => 5,
        ]);

        // La habitación pertenece a otro hotel: el binding scoped debe dar 404.
        $this->deleteJson("/api/hotels/{$this->hotel->id}/rooms/{$room->id}")
            ->assertNotFound();
    }
}
