# Diagrama de Clases

Arquitectura en capas del backend (Laravel), con separación de responsabilidades
según SOLID. El flujo de una petición atraviesa: **Controller → Form Request
(validación) → Service (lógica) → Repository (datos) → Model**, y la respuesta se
serializa con un **API Resource**.

## Capas y dependencias

```mermaid
classDiagram
    direction LR

    class HotelController {
        -HotelService service
        +index() AnonymousResourceCollection
        +store(StoreHotelRequest) JsonResponse
        +show(Hotel) HotelResource
        +update(UpdateHotelRequest, Hotel) HotelResource
        +destroy(Hotel) Response
    }

    class HotelRoomController {
        -HotelRoomService service
        +index(Hotel) AnonymousResourceCollection
        +store(StoreHotelRoomRequest, Hotel) JsonResponse
        +update(UpdateHotelRoomRequest, Hotel, HotelRoom) HotelRoomResource
        +destroy(Hotel, HotelRoom) Response
    }

    class HotelService {
        -HotelRepositoryInterface hotels
        +list(int perPage) LengthAwarePaginator
        +show(Hotel) Hotel
        +create(array) Hotel
        +update(Hotel, array) Hotel
        +delete(Hotel) void
    }

    class HotelRoomService {
        -HotelRoomRepositoryInterface rooms
        +listForHotel(Hotel) Collection
        +create(Hotel, array) HotelRoom
        +update(HotelRoom, array) HotelRoom
        +delete(HotelRoom) void
    }

    class HotelRepositoryInterface {
        <<interface>>
        +paginate(int) LengthAwarePaginator
        +findWithDetails(Hotel) Hotel
        +create(array) Hotel
        +update(Hotel, array) Hotel
        +delete(Hotel) void
    }

    class HotelRoomRepositoryInterface {
        <<interface>>
        +forHotel(Hotel) Collection
        +create(array) HotelRoom
        +update(HotelRoom, array) HotelRoom
        +delete(HotelRoom) void
    }

    class HotelRepository {
        +paginate(int) LengthAwarePaginator
        +findWithDetails(Hotel) Hotel
        +create(array) Hotel
        +update(Hotel, array) Hotel
        +delete(Hotel) void
    }

    class HotelRoomRepository {
        +forHotel(Hotel) Collection
        +create(array) HotelRoom
        +update(HotelRoom, array) HotelRoom
        +delete(HotelRoom) void
    }

    class StoreHotelRoomRequest {
        +rules() array
        +withValidator(Validator) void
    }

    class ValidAccommodationForRoomType {
        <<ValidationRule>>
        -mixed roomTypeId
        +validate(string, mixed, Closure) void
    }

    HotelController ..> StoreHotelRoomRequest : valida con
    HotelController --> HotelService : usa
    HotelRoomController --> HotelRoomService : usa
    HotelRoomController ..> StoreHotelRoomRequest : valida con
    HotelService --> HotelRepositoryInterface : depende de
    HotelRoomService --> HotelRoomRepositoryInterface : depende de
    HotelRepository ..|> HotelRepositoryInterface : implementa
    HotelRoomRepository ..|> HotelRoomRepositoryInterface : implementa
    StoreHotelRoomRequest ..> ValidAccommodationForRoomType : aplica regla
```

> El contenedor de Laravel enlaza cada interfaz con su implementación en
> `RepositoryServiceProvider` (`HotelRepositoryInterface → HotelRepository`,
> `HotelRoomRepositoryInterface → HotelRoomRepository`). Así los servicios
> dependen de **abstracciones**, no de Eloquent (inversión de dependencias).

## Modelo de dominio (Eloquent)

```mermaid
classDiagram
    direction LR

    class Hotel {
        +int id
        +string name
        +string address
        +string nit
        +int number_of_rooms
        +city() BelongsTo
        +rooms() HasMany
        +configuredRoomsCount() int
    }

    class HotelRoom {
        +int id
        +int quantity
        +hotel() BelongsTo
        +roomType() BelongsTo
        +accommodation() BelongsTo
    }

    class City {
        +int id
        +string name
        +hotels() HasMany
    }

    class RoomType {
        +int id
        +string name
        +accommodations() BelongsToMany
        +hotelRooms() HasMany
    }

    class Accommodation {
        +int id
        +string name
        +roomTypes() BelongsToMany
        +hotelRooms() HasMany
    }

    City "1" --> "*" Hotel : tiene
    Hotel "1" --> "*" HotelRoom : configura
    RoomType "1" --> "*" HotelRoom
    Accommodation "1" --> "*" HotelRoom
    RoomType "*" --> "*" Accommodation : combinaciones válidas
```

## Principios SOLID evidenciados

- **S** — cada clase tiene una sola razón de cambio (controller orquesta, service
  decide, repository persiste, resource serializa).
- **O/L** — repositorios sustituibles por su interfaz sin tocar los servicios.
- **I** — interfaces de repositorio pequeñas y específicas por agregado.
- **D** — los servicios dependen de `*RepositoryInterface`, no de implementaciones.
