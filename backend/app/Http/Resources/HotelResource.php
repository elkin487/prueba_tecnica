<?php

namespace App\Http\Resources;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Hotel */
class HotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Total de habitaciones ya configuradas. Se obtiene del agregado
        // withSum('rooms', 'quantity') cuando el repositorio lo precarga.
        $configured = (int) ($this->rooms_sum_quantity ?? 0);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'nit' => $this->nit,
            'number_of_rooms' => $this->number_of_rooms,
            'configured_rooms' => $configured,
            'available_rooms' => max(0, $this->number_of_rooms - $configured),
            'city' => new CityResource($this->whenLoaded('city')),
            'rooms' => HotelRoomResource::collection($this->whenLoaded('rooms')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
