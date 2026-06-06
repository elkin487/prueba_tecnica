# Dump de la base de datos

`dump.sql` es un volcado generado con `pg_dump` (PostgreSQL 17) que contiene:

- **Toda la estructura** (tablas, índices, claves foráneas y restricciones UNIQUE).
- **Los datos de los catálogos** ya sembrados:
  - `cities` — ciudades de Colombia.
  - `room_types` — Estándar, Junior, Suite.
  - `accommodations` — Sencilla, Doble, Triple, Cuádruple.
  - `room_type_accommodation` — combinaciones válidas tipo ↔ acomodación.
  - `migrations` — estado de las migraciones (para que Laravel las reconozca).

> **No incluye datos de `hotels` ni `hotel_rooms`**: esas tablas quedan vacías,
> listas para que el usuario registre sus propios hoteles.

## Cómo restaurarlo

Es una alternativa a `php artisan migrate --seed`. Necesitas un PostgreSQL en
ejecución.

### 1. Crear la base de datos

```bash
createdb -h 127.0.0.1 -U postgres prueba_tecnica
# o desde psql:  CREATE DATABASE prueba_tecnica;
```

### 2. Importar el dump

```bash
psql -h 127.0.0.1 -U postgres -d prueba_tecnica -f docs/database/dump.sql
```

### 3. Verificar

```sql
SELECT count(*) FROM cities;                  -- ciudades
SELECT count(*) FROM room_type_accommodation; -- combinaciones válidas
```

## Cómo regenerarlo

Si cambian las migraciones o los seeders, regenera el dump así (desde la raíz
del repositorio):

```bash
pg_dump -h 127.0.0.1 -p 5432 -U postgres -d prueba_tecnica \
  --no-owner --no-privileges \
  --exclude-table-data='public.hotels' \
  --exclude-table-data='public.hotel_rooms' \
  -f docs/database/dump.sql
```

`--no-owner` y `--no-privileges` hacen el dump portable (no queda atado a un
usuario concreto de PostgreSQL).
