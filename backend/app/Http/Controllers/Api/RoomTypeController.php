<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccommodationResource;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Catálogo de tipos de habitación (solo lectura) y sus acomodaciones válidas.
 */
class RoomTypeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RoomTypeResource::collection(RoomType::orderBy('id')->get());
    }

    /**
     * Acomodaciones válidas para un tipo de habitación dado.
     * Útil para que el frontend ofrezca solo combinaciones permitidas.
     */
    public function accommodations(RoomType $roomType): AnonymousResourceCollection
    {
        return AccommodationResource::collection(
            $roomType->accommodations()->orderBy('id')->get()
        );
    }
}
