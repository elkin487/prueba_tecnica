<?php

namespace App\Rules;

use App\Models\RoomType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida que una acomodación pertenezca al conjunto de acomodaciones
 * permitidas para un tipo de habitación, según la tabla de combinaciones
 * válidas (room_type_accommodation).
 *
 *   Estándar -> Sencilla, Doble
 *   Junior   -> Triple, Cuádruple
 *   Suite    -> Sencilla, Doble, Triple
 */
class ValidAccommodationForRoomType implements ValidationRule
{
    public function __construct(private readonly mixed $roomTypeId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Si falta el tipo o la acomodación, dejar que las reglas
        // required/exists reporten el error correspondiente.
        if (empty($this->roomTypeId) || empty($value)) {
            return;
        }

        $roomType = RoomType::find($this->roomTypeId);
        if (! $roomType) {
            return; // 'exists:room_types,id' reportará el tipo inexistente.
        }

        $isValid = $roomType->accommodations()->whereKey($value)->exists();

        if (! $isValid) {
            $fail("La acomodación seleccionada no es válida para el tipo de habitación «{$roomType->name}».");
        }
    }
}
