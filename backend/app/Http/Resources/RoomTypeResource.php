<?php

namespace App\Http\Resources;

use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin RoomType */
class RoomTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // Acomodaciones válidas para el tipo (solo si la relación fue cargada).
            'accommodations' => AccommodationResource::collection($this->whenLoaded('accommodations')),
        ];
    }
}
