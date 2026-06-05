<?php

namespace App\Providers;

use App\Repositories\Contracts\HotelRepositoryInterface;
use App\Repositories\Contracts\HotelRoomRepositoryInterface;
use App\Repositories\HotelRepository;
use App\Repositories\HotelRoomRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Enlaza los contratos de repositorio con sus implementaciones Eloquent.
 * Centralizar los bindings facilita sustituir implementaciones (p. ej. en
 * pruebas) sin modificar los servicios que dependen de las interfaces.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        HotelRepositoryInterface::class => HotelRepository::class,
        HotelRoomRepositoryInterface::class => HotelRoomRepository::class,
    ];
}
