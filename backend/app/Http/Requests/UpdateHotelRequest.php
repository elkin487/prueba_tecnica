<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación para la actualización de un hotel. Respeta la unicidad
 * (ignorando el propio registro) e impide reducir el número de habitaciones
 * por debajo de las ya configuradas.
 */
class UpdateHotelRequest extends FormRequest
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
        $hotelId = $this->route('hotel')?->id;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('hotels', 'name')->ignore($hotelId)],
            'address' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'nit' => ['required', 'string', 'max:50', Rule::unique('hotels', 'nit')->ignore($hotelId)],
            'number_of_rooms' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hotel = $this->route('hotel');
            if (! $hotel) {
                return;
            }

            $configured = (int) $hotel->rooms()->sum('quantity');

            if ($this->filled('number_of_rooms') && (int) $this->input('number_of_rooms') < $configured) {
                $validator->errors()->add(
                    'number_of_rooms',
                    "El hotel ya tiene {$configured} habitaciones configuradas; el número no puede ser menor."
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
            'name' => 'nombre',
            'address' => 'dirección',
            'city_id' => 'ciudad',
            'nit' => 'NIT',
            'number_of_rooms' => 'número de habitaciones',
        ];
    }
}
