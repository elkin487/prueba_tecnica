<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Catálogo de ciudades (solo lectura).
 */
class CityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CityResource::collection(City::orderBy('name')->get());
    }
}
