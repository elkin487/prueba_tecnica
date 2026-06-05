<?php

namespace App\Repositories\Contracts;

use App\Models\Hotel;
use App\Models\HotelRoom;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contrato de acceso a datos para las configuraciones de habitaciones.
 */
interface HotelRoomRepositoryInterface
{
    /**
     * @return Collection<int, HotelRoom>
     */
    public function forHotel(Hotel $hotel): Collection;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): HotelRoom;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(HotelRoom $room, array $data): HotelRoom;

    public function delete(HotelRoom $room): void;
}
