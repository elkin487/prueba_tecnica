<?php

namespace App\Http\Resources;

use App\Models\HotelRoom;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin HotelRoom */
class HotelRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'room_type_id' => $this->room_type_id,
            'accommodation_id' => $this->accommodation_id,
            'quantity' => $this->quantity,
            'room_type' => new RoomTypeResource($this->whenLoaded('roomType')),
            'accommodation' => new AccommodationResource($this->whenLoaded('accommodation')),
        ];
    }
}
