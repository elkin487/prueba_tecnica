<?php

namespace App\Repositories;

use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Repositories\Contracts\HotelRoomRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementación Eloquent del repositorio de configuraciones de habitaciones.
 */
class HotelRoomRepository implements HotelRoomRepositoryInterface
{
    /**
     * @return Collection<int, HotelRoom>
     */
    public function forHotel(Hotel $hotel): Collection
    {
        return $hotel->rooms()
            ->with(['roomType', 'accommodation'])
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): HotelRoom
    {
        return HotelRoom::create($data);
    }

    public function update(HotelRoom $room, array $data): HotelRoom
    {
        $room->update($data);

        return $room->refresh();
    }

    public function delete(HotelRoom $room): void
    {
        $room->delete();
    }
}
