<?php

namespace App\Services;

use App\Models\Hotel;
use App\Repositories\Contracts\HotelRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Orquesta los casos de uso sobre hoteles. La validación de entrada vive en
 * los Form Requests; aquí se coordina la persistencia a través del repositorio.
 */
class HotelService
{
    public function __construct(
        private readonly HotelRepositoryInterface $hotels,
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->hotels->paginate($perPage);
    }

    public function show(Hotel $hotel): Hotel
    {
        return $this->hotels->findWithDetails($hotel);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Hotel
    {
        return $this->hotels->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Hotel $hotel, array $data): Hotel
    {
        return $this->hotels->update($hotel, $data);
    }

    public function delete(Hotel $hotel): void
    {
        $this->hotels->delete($hotel);
    }
}
