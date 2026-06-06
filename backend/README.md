# Backend — API Hoteles Decameron (Laravel)

API RESTful que gestiona hoteles y la configuración de sus habitaciones, con las
validaciones de negocio como fuente de verdad. Forma parte del
[monorepo de la prueba técnica](../README.md).

## 🧰 Stack

- **Laravel 13** (PHP 8.3+)
- **PostgreSQL**
- **PHPUnit** (pruebas) · **Laravel Pint** (estilo de código)

## 🏛️ Arquitectura en capas (SOLID)

Cada petición atraviesa capas con una única responsabilidad:

```
Ruta (routes/api.php)
   └─► Controller      → orquesta (recibe, delega, responde). Sin lógica de negocio.
        └─► FormRequest → valida entrada + reglas de negocio (combinación, capacidad, unicidad)
             └─► Service     → coordina el caso de uso
                  └─► Repository → acceso a datos (abstrae Eloquent vía interfaz)
                       └─► Model      → entidad Eloquent
   ◄─ API Resource    → serializa la respuesta JSON
```

- Los **servicios dependen de interfaces** de repositorio
  (`HotelRepositoryInterface`, `HotelRoomRepositoryInterface`); el enlace con la
  implementación Eloquent se hace en `App\Providers\RepositoryServiceProvider`.
- La combinación válida tipo ↔ acomodación es una **regla de validación**
  (`App\Rules\ValidAccommodationForRoomType`) que consulta el catálogo, no un `if`.

Diagramas en [../docs/uml](../docs/uml/).

## 📂 Estructura principal

```
app/
├── Http/
│   ├── Controllers/Api/   # HotelController, HotelRoomController, catálogos
│   ├── Requests/          # Store/Update Hotel y HotelRoom (validación)
│   └── Resources/         # Serialización JSON
├── Models/                # Hotel, HotelRoom, City, RoomType, Accommodation
├── Repositories/          # Contratos + implementaciones Eloquent
├── Rules/                 # ValidAccommodationForRoomType
└── Services/              # HotelService, HotelRoomService
database/
├── migrations/            # Esquema (con UNIQUE y claves foráneas)
└── seeders/               # Catálogos: ciudades, tipos, acomodaciones, combinaciones
routes/api.php             # Rutas REST (prefijo /api)
```

## ⚙️ Puesta en marcha

Ver la [guía completa en el README principal](../README.md#-instalación-paso-a-paso).
Resumen:

```bash
composer install
cp .env.example .env
php artisan key:generate
# configura la conexión PostgreSQL en .env
php artisan migrate --seed
php artisan serve            # http://127.0.0.1:8000
```

## 🔌 Endpoints

Contrato completo (cuerpos, respuestas y códigos HTTP) en
[../docs/api.md](../docs/api.md).

- Hoteles: `GET|POST /api/hotels`, `GET|PUT|DELETE /api/hotels/{hotel}`
- Habitaciones: `GET|POST /api/hotels/{hotel}/rooms`, `PUT|DELETE /api/hotels/{hotel}/rooms/{room}`
- Catálogos: `GET /api/cities`, `/api/room-types`, `/api/room-types/{id}/accommodations`, `/api/accommodations`

## 🧪 Pruebas y estilo

```bash
php artisan test            # PHPUnit (Unit + Feature)
./vendor/bin/pint --test    # verificación de estilo (sin modificar)
./vendor/bin/pint           # aplica el estilo
```

## 🔐 Variables de entorno

Se documentan en `.env.example`. Las relevantes para la base de datos:
`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
El archivo `.env` está fuera de Git por seguridad.
