# Contrato de la API REST

Base URL: `/api` · Formato: **JSON** · Sin estado (cada petición es independiente).
El backend es la **fuente de verdad** de todas las validaciones.

## Convenciones

- Verbos y códigos HTTP según REST: `200` OK, `201` Created, `204` No Content,
  `404` Not Found, `422` Unprocessable Entity (validación).
- Las respuestas de recursos se envuelven en `data`. Los listados de hoteles van
  paginados (`data`, `links`, `meta`).
- Errores de validación: `{ "message": "...", "errors": { "campo": ["..."] } }`.

---

## Catálogos (solo lectura)

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/cities` | Lista de ciudades. |
| GET | `/api/room-types` | Lista de tipos de habitación. |
| GET | `/api/room-types/{roomType}/accommodations` | Tipo con sus **acomodaciones válidas**. |
| GET | `/api/accommodations` | Lista de acomodaciones. |

**Ejemplo** — `GET /api/room-types/3/accommodations` (Suite):

```json
{
  "data": {
    "id": 3,
    "name": "Suite",
    "accommodations": [
      { "id": 1, "name": "Sencilla" },
      { "id": 2, "name": "Doble" },
      { "id": 3, "name": "Triple" }
    ]
  }
}
```

Este endpoint alimenta el combo dependiente de la SPA: al elegir un tipo, solo se
habilitan sus acomodaciones válidas.

---

## Hoteles

| Método | Ruta | Descripción | Éxito |
|--------|------|-------------|-------|
| GET | `/api/hotels` | Listado paginado. | 200 |
| POST | `/api/hotels` | Crear hotel. | 201 |
| GET | `/api/hotels/{hotel}` | Detalle (incluye habitaciones). | 200 |
| PUT/PATCH | `/api/hotels/{hotel}` | Actualizar. | 200 |
| DELETE | `/api/hotels/{hotel}` | Eliminar. | 204 |

### Cuerpo de la petición (crear / actualizar)

```json
{
  "name": "DECAMERON CARTAGENA",
  "address": "CALLE 23 58-25",
  "city_id": 1,
  "nit": "12345678-9",
  "number_of_rooms": 42
}
```

Reglas: `name` único, `nit` único, `city_id` debe existir, `number_of_rooms` ≥ 1.

### Respuesta (detalle) — `GET /api/hotels/{hotel}`

```json
{
  "data": {
    "id": 1,
    "name": "DECAMERON CARTAGENA",
    "address": "CALLE 23 58-25",
    "nit": "12345678-9",
    "number_of_rooms": 42,
    "configured_rooms": 37,
    "available_rooms": 5,
    "city": { "id": 1, "name": "Cartagena" },
    "rooms": [
      {
        "id": 10, "hotel_id": 1, "room_type_id": 1, "accommodation_id": 1,
        "quantity": 25,
        "room_type": { "id": 1, "name": "Estándar" },
        "accommodation": { "id": 1, "name": "Sencilla" }
      }
    ],
    "created_at": "2026-06-05T20:00:00.000000Z",
    "updated_at": "2026-06-05T20:00:00.000000Z"
  }
}
```

> En el **listado** (`GET /api/hotels`) cada hotel trae `city`, `configured_rooms`
> y `available_rooms`, pero **no** el detalle de `rooms` (se carga solo en el detalle).

---

## Configuración de habitaciones (anidada bajo un hotel)

Rutas *scoped*: la habitación debe pertenecer al hotel de la ruta.

| Método | Ruta | Descripción | Éxito |
|--------|------|-------------|-------|
| GET | `/api/hotels/{hotel}/rooms` | Listar configuración. | 200 |
| POST | `/api/hotels/{hotel}/rooms` | Agregar configuración. | 201 |
| PUT/PATCH | `/api/hotels/{hotel}/rooms/{room}` | Actualizar. | 200 |
| DELETE | `/api/hotels/{hotel}/rooms/{room}` | Eliminar. | 204 |

### Cuerpo de la petición (crear / actualizar)

```json
{
  "room_type_id": 1,
  "accommodation_id": 1,
  "quantity": 25
}
```

### Reglas de negocio (devuelven `422` con mensaje claro)

1. **Combinación válida** — la acomodación debe pertenecer al tipo
   (catálogo `room_type_accommodation`).
2. **Par único** — no se repite `(room_type_id, accommodation_id)` en el hotel.
3. **Capacidad** — la suma de `quantity` no puede superar `number_of_rooms`
   (el mensaje indica las habitaciones disponibles).

### Ejemplo de error `422`

```json
{
  "message": "La cantidad supera el máximo de habitaciones del hotel. Disponibles: 5.",
  "errors": {
    "quantity": ["La cantidad supera el máximo de habitaciones del hotel. Disponibles: 5."]
  }
}
```
