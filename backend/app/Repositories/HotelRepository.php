<?php

namespace App\Repositories;

use App\Models\Hotel;
use App\Repositories\Contracts\HotelRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Implementación Eloquent del repositorio de hoteles.
 */
class HotelRepository implements HotelRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Hotel::query()
            ->with('city')
            ->withSum('rooms', 'quantity')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findWithDetails(Hotel $hotel): Hotel
    {
        return $hotel
            ->load(['city', 'rooms.roomType', 'rooms.accommodation'])
            ->loadSum('rooms', 'quantity');
    }

    public function create(array $data): Hotel
    {
        return Hotel::create($data);
    }

    public function update(Hotel $hotel, array $data): Hotel
    {
        $hotel->update($data);

        return $hotel->refresh();
    }

    public function delete(Hotel $hotel): void
    {
        $hotel->delete();
    }
}
