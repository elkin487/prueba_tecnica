<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHotelRoomRequest;
use App\Http\Requests\UpdateHotelRoomRequest;
use App\Http\Resources\HotelRoomResource;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Services\HotelRoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * API REST de configuraciones de habitaciones, anidada bajo un hotel.
 */
class HotelRoomController extends Controller
{
    public function __construct(
        private readonly HotelRoomService $service,
    ) {}

    public function index(Hotel $hotel): AnonymousResourceCollection
    {
        return HotelRoomResource::collection($this->service->listForHotel($hotel));
    }

    public function store(StoreHotelRoomRequest $request, Hotel $hotel): JsonResponse
    {
        $room = $this->service->create($hotel, $request->validated());

        return HotelRoomResource::make($room->load(['roomType', 'accommodation']))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateHotelRoomRequest $request, Hotel $hotel, HotelRoom $room): HotelRoomResource
    {
        $room = $this->service->update($room, $request->validated());

        return HotelRoomResource::make($room->load(['roomType', 'accommodation']));
    }

    public function destroy(Hotel $hotel, HotelRoom $room): Response
    {
        $this->service->delete($room);

        return response()->noContent();
    }
}
