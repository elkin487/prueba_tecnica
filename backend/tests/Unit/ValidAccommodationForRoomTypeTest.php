<?php

namespace Tests\Unit;

use App\Models\Accommodation;
use App\Models\RoomType;
use App\Rules\ValidAccommodationForRoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prueba unitaria de la regla de negocio que valida las combinaciones
 * tipo de habitación <-> acomodación.
 */
class ValidAccommodationForRoomTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // catálogos + combinaciones válidas
    }

    /**
     * Invoca la regla y devuelve el mensaje de error (o null si pasa).
     */
    private function runRule(int $roomTypeId, int $accommodationId): ?string
    {
        $message = null;
        (new ValidAccommodationForRoomType($roomTypeId))->validate(
            'accommodation_id',
            $accommodationId,
            function (string $msg) use (&$message): void {
                $message = $msg;
            }
        );

        return $message;
    }

    public function test_acepta_acomodacion_valida_para_el_tipo(): void
    {
        $estandar = RoomType::where('name', 'Estándar')->firstOrFail();
        $sencilla = Accommodation::where('name', 'Sencilla')->firstOrFail();

        $this->assertNull($this->runRule($estandar->id, $sencilla->id));
    }

    public function test_rechaza_acomodacion_invalida_para_el_tipo(): void
    {
        // Estándar NO admite Triple.
        $estandar = RoomType::where('name', 'Estándar')->firstOrFail();
        $triple = Accommodation::where('name', 'Triple')->firstOrFail();

        $this->assertNotNull($this->runRule($estandar->id, $triple->id));
    }

    public function test_suite_admite_sencilla_doble_y_triple(): void
    {
        $suite = RoomType::where('name', 'Suite')->firstOrFail();

        foreach (['Sencilla', 'Doble', 'Triple'] as $name) {
            $acc = Accommodation::where('name', $name)->firstOrFail();
            $this->assertNull(
                $this->runRule($suite->id, $acc->id),
                "Suite debería admitir {$name}"
            );
        }
    }

    public function test_junior_no_admite_sencilla(): void
    {
        $junior = RoomType::where('name', 'Junior')->firstOrFail();
        $sencilla = Accommodation::where('name', 'Sencilla')->firstOrFail();

        $this->assertNotNull($this->runRule($junior->id, $sencilla->id));
    }
}
