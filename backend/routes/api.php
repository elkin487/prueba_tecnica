<?php

use App\Http\Controllers\Api\AccommodationController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HotelRoomController;
use App\Http\Controllers\Api\RoomTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas API (prefijo /api)
|--------------------------------------------------------------------------
*/

// Catálogos (solo lectura).
Route::get('cities', [CityController::class, 'index']);
Route::get('room-types', [RoomTypeController::class, 'index']);
Route::get('room-types/{roomType}/accommodations', [RoomTypeController::class, 'accommodations']);
Route::get('accommodations', [AccommodationController::class, 'index']);

// Hoteles.
Route::apiResource('hotels', HotelController::class);

// Configuración de habitaciones anidada bajo cada hotel.
// scoped() garantiza que la habitación pertenezca al hotel de la ruta.
Route::apiResource('hotels.rooms', HotelRoomController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->scoped();
