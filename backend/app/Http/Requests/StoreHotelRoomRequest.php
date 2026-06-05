<?php

namespace App\Http\Requests;

use App\Rules\ValidAccommodationForRoomType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación para agregar una configuración de habitaciones a un hotel.
 *
 * Reglas de negocio aplicadas:
 *  - La acomodación debe ser válida para el tipo de habitación.
 *  - No se puede repetir el par (tipo, acomodación) dentro del hotel.
 *  - La suma de cantidades no puede superar el máximo del hotel.
 */
class StoreHotelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'accommodation_id' => [
                'required',
                'integer',
                'exists:accommodations,id',
                new ValidAccommodationForRoomType($this->input('room_type_id')),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hotel = $this->route('hotel');
            if (! $hotel) {
                return;
            }

            // No repetir el par (tipo, acomodación) en el mismo hotel.
            $duplicated = $hotel->rooms()
                ->where('room_type_id', $this->input('room_type_id'))
                ->where('accommodation_id', $this->input('accommodation_id'))
                ->exists();

            if ($duplicated) {
                $validator->errors()->add(
                    'accommodation_id',
                    'Ya existe una configuración con este tipo de habitación y acomodación para el hotel.'
                );
            }

            // No superar el máximo de habitaciones del hotel.
            $configured = (int) $hotel->rooms()->sum('quantity');
            $requested = (int) $this->input('quantity');

            if ($configured + $requested > $hotel->number_of_rooms) {
                $available = max(0, $hotel->number_of_rooms - $configured);
                $validator->errors()->add(
                    'quantity',
                    "La cantidad supera el máximo de habitaciones del hotel. Disponibles: {$available}."
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'room_type_id' => 'tipo de habitación',
            'accommodation_id' => 'acomodación',
            'quantity' => 'cantidad',
        ];
    }
}
