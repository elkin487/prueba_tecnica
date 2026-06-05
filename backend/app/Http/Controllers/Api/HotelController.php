<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;
use App\Services\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * API REST de hoteles.
 */
class HotelController extends Controller
{
    public function __construct(
        private readonly HotelService $service,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return HotelResource::collection($this->service->list());
    }

    public function store(StoreHotelRequest $request): JsonResponse
    {
        $hotel = $this->service->create($request->validated());

        return HotelResource::make($hotel->load('city'))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Hotel $hotel): HotelResource
    {
        return HotelResource::make($this->service->show($hotel));
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): HotelResource
    {
        $hotel = $this->service->update($hotel, $request->validated());

        return HotelResource::make($hotel->load('city'));
    }

    public function destroy(Hotel $hotel): Response
    {
        $this->service->delete($hotel);

        return response()->noContent();
    }
}
