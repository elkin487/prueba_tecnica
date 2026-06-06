# Diagrama Entidad-Relación (ERD)

Modelo de datos del sistema, derivado directamente de las migraciones de
`backend/database/migrations`. Las tablas de catálogo se siembran con *seeders*
y se exponen solo como lectura; las entidades principales son `hotels` y
`hotel_rooms`.

```mermaid
erDiagram
    cities ||--o{ hotels : "ubica"
    hotels ||--o{ hotel_rooms : "configura"
    room_types ||--o{ hotel_rooms : "tipifica"
    accommodations ||--o{ hotel_rooms : "acomoda"
    room_types ||--o{ room_type_accommodation : "permite"
    accommodations ||--o{ room_type_accommodation : "es_permitida_en"

    cities {
        bigint id PK
        varchar name UK "único"
        timestamp created_at
        timestamp updated_at
    }

    room_types {
        bigint id PK
        varchar name UK "Estándar, Junior, Suite"
        timestamp created_at
        timestamp updated_at
    }

    accommodations {
        bigint id PK
        varchar name UK "Sencilla, Doble, Triple, Cuádruple"
        timestamp created_at
        timestamp updated_at
    }

    room_type_accommodation {
        bigint id PK
        bigint room_type_id FK
        bigint accommodation_id FK
        timestamp created_at
        timestamp updated_at
    }

    hotels {
        bigint id PK
        varchar name UK "único (no repetir hotel)"
        varchar address
        bigint city_id FK
        varchar nit UK "único"
        integer number_of_rooms "máximo de habitaciones"
        timestamp created_at
        timestamp updated_at
    }

    hotel_rooms {
        bigint id PK
        bigint hotel_id FK
        bigint room_type_id FK
        bigint accommodation_id FK
        integer quantity "cantidad de habitaciones"
        timestamp created_at
        timestamp updated_at
    }
```

## Restricciones y reglas reflejadas en el esquema

| Tabla | Restricción | Regla de negocio que cumple |
|-------|-------------|------------------------------|
| `hotels` | `name` **UNIQUE** | No deben existir hoteles repetidos (nombre). |
| `hotels` | `nit` **UNIQUE** | No deben existir hoteles repetidos (NIT). |
| `hotel_rooms` | **UNIQUE** `(hotel_id, room_type_id, accommodation_id)` | No se repite el par *(tipo, acomodación)* en un mismo hotel. |
| `room_type_accommodation` | **UNIQUE** `(room_type_id, accommodation_id)` | Catálogo de combinaciones válidas, sin duplicados. |

## Integridad referencial (ON DELETE)

- `hotels.city_id` → `cities` : **RESTRICT** (no se borra una ciudad con hoteles).
- `hotel_rooms.hotel_id` → `hotels` : **CASCADE** (al borrar un hotel se borran sus configuraciones).
- `hotel_rooms.room_type_id` → `room_types` : **RESTRICT**.
- `hotel_rooms.accommodation_id` → `accommodations` : **RESTRICT**.
- `room_type_accommodation.*` → catálogos : **CASCADE**.

> La regla "la suma de cantidades no supera `number_of_rooms`" y "la acomodación
> debe ser válida para el tipo" no se expresan como *constraints* SQL, sino como
> validaciones de aplicación (ver [diagrama de secuencia](secuencia.md)), porque
> dependen de agregaciones y del catálogo de combinaciones.
