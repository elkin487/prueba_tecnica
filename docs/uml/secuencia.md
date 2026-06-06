# Diagrama de Secuencia

Flujo más representativo del sistema: **agregar una configuración de habitaciones
a un hotel** (`POST /api/hotels/{hotel}/rooms`). Muestra cómo se aplican, en
orden, las reglas de negocio antes de persistir.

```mermaid
sequenceDiagram
    actor U as SPA React
    participant R as Router api.php
    participant FR as StoreHotelRoomRequest
    participant Rule as ValidAccommodationForRoomType
    participant C as HotelRoomController
    participant S as HotelRoomService
    participant Repo as HotelRoomRepository
    participant DB as PostgreSQL

    U->>R: POST /api/hotels/42/rooms<br/>{room_type_id, accommodation_id, quantity}
    R->>FR: resuelve la petición y dispara la validación

    Note over FR: rules() — required / integer / exists
    FR->>Rule: ¿acomodación válida para el tipo?
    Rule->>DB: SELECT en room_type_accommodation
    DB-->>Rule: existe / no existe
    Rule-->>FR: ok / mensaje de error

    Note over FR: withValidator() — reglas dependientes del hotel
    FR->>DB: ¿par (tipo, acomodación) ya configurado?
    DB-->>FR: sí / no
    FR->>DB: SUM(quantity) de las habitaciones del hotel
    DB-->>FR: total configurado

    alt Validación falla
        FR-->>U: 422 Unprocessable Entity<br/>{ errors }
    else Datos válidos
        FR->>C: validated()
        C->>S: create(hotel, data)
        S->>Repo: create(data + hotel_id)
        Repo->>DB: INSERT INTO hotel_rooms
        DB-->>Repo: HotelRoom
        Repo-->>S: HotelRoom
        S-->>C: HotelRoom
        C-->>U: 201 Created<br/>HotelRoomResource
    end
```

## Notas

- La **validación de entrada** y las **reglas de negocio** viven en el Form
  Request (`StoreHotelRoomRequest`): `rules()` para formato y la regla custom de
  combinación válida; `withValidator()` para las reglas que dependen del estado
  del hotel (par duplicado y capacidad máxima).
- El **controller** queda delgado: solo orquesta (recibe la petición ya validada,
  delega en el servicio y responde con el código HTTP correcto).
- El **service** coordina el caso de uso y delega la persistencia en el
  **repositorio**, que es la única capa que conoce Eloquent.
- Los códigos HTTP siguen la semántica REST: `201` al crear, `422` ante errores
  de validación, `204` al eliminar, `404` si el recurso no existe.
