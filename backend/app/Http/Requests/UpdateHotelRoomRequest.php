<?php

namespace App\Http\Requests;

use App\Rules\ValidAccommodationForRoomType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación para actualizar una configuración de habitaciones de un hotel.
 * Igual que la creación, pero excluyendo el propio registro de los chequeos
 * de unicidad y de capacidad.
 */
class UpdateHotelRoomRequest extends FormRequest
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
            $room = $this->route('room');
            if (! $hotel) {
                return;
            }

            $currentId = $room?->id;

            // Unicidad del par (tipo, acomodación), excluyendo el propio registro.
            $duplicated = $hotel->rooms()
                ->where('room_type_id', $this->input('room_type_id'))
                ->where('accommodation_id', $this->input('accommodation_id'))
                ->whereKeyNot($currentId)
                ->exists();

            if ($duplicated) {
                $validator->errors()->add(
                    'accommodation_id',
                    'Ya existe una configuración con este tipo de habitación y acomodación para el hotel.'
                );
            }

            // Capacidad, excluyendo la cantidad del propio registro.
            $configured = (int) $hotel->rooms()->whereKeyNot($currentId)->sum('quantity');
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
