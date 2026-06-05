<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Repositories\Contracts\HotelRoomRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Orquesta los casos de uso sobre las configuraciones de habitaciones de un
 * hotel. Las reglas de negocio (combinación válida, unicidad y capacidad) se
 * validan en los Form Requests antes de llegar aquí.
 */
class HotelRoomService
{
    public function __construct(
        private readonly HotelRoomRepositoryInterface $rooms,
    ) {}

    /**
     * @return Collection<int, HotelRoom>
     */
    public function listForHotel(Hotel $hotel): Collection
    {
        return $this->rooms->forHotel($hotel);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Hotel $hotel, array $data): HotelRoom
    {
        $data['hotel_id'] = $hotel->id;

        return $this->rooms->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(HotelRoom $room, array $data): HotelRoom
    {
        return $this->rooms->update($room, $data);
    }

    public function delete(HotelRoom $room): void
    {
        $this->rooms->delete($room);
    }
}
