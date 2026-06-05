<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccommodationResource;
use App\Models\Accommodation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Catálogo de acomodaciones (solo lectura).
 */
class AccommodationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return AccommodationResource::collection(Accommodation::orderBy('id')->get());
    }
}
