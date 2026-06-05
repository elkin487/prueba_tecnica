<?php

namespace App\Repositories\Contracts;

use App\Models\Hotel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Contrato de acceso a datos para hoteles. Permite sustituir la
 * implementación (Eloquent, otra fuente, mock en pruebas) sin tocar
 * la capa de servicios.
 */
interface HotelRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findWithDetails(Hotel $hotel): Hotel;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Hotel;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Hotel $hotel, array $data): Hotel;

    public function delete(Hotel $hotel): void;
}
