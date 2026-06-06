# Estrategia y cobertura de pruebas

Las pruebas son **evidencia de primera clase** del proyecto: cada regla de negocio
nace con su prueba y el pipeline de Integración Continua no se da por válido en rojo.

| Capa | Herramientas | Nº de pruebas |
|------|--------------|---------------|
| Backend | **PHPUnit** (Unit + Feature) sobre **PostgreSQL** | 26 pruebas / 82 aserciones |
| Frontend | **Vitest** + **React Testing Library** (jsdom) | 8 pruebas |

## Cómo ejecutarlas

```bash
# Backend (desde backend/)
php artisan test            # toda la suite (Unit + Feature)
./vendor/bin/pint --test    # estilo de código (no modifica)

# Frontend (desde frontend/)
npm run test                # Vitest (modo run)
npm run lint                # ESLint
```

El **CI** (`.github/workflows/ci.yml`) ejecuta exactamente esto en cada push/PR:
el job *backend* levanta un servicio PostgreSQL y corre Pint + PHPUnit; el job
*frontend* corre ESLint + Vitest + build.

---

## Backend

- **Pruebas unitarias** (`tests/Unit`): aíslan una regla de negocio sin pasar por HTTP.
- **Pruebas de integración / Feature** (`tests/Feature`): ejercitan la API real
  (rutas → validación → servicio → repositorio → BD) con `getJson/postJson/...`.
- Cada clase usa `RefreshDatabase` y siembra los catálogos en `setUp()` (`$this->seed()`),
  de modo que las combinaciones válidas tipo↔acomodación están disponibles.
- Los hoteles de prueba se generan con **factories** (`Hotel::factory()`).

### `tests/Unit/ValidAccommodationForRoomTypeTest`

Prueba la regla `App\Rules\ValidAccommodationForRoomType` de forma aislada.

| Prueba | Verifica |
|--------|----------|
| `test_acepta_acomodacion_valida_para_el_tipo` | Estándar admite Sencilla. |
| `test_rechaza_acomodacion_invalida_para_el_tipo` | Estándar **no** admite Triple. |
| `test_suite_admite_sencilla_doble_y_triple` | Suite admite sus tres acomodaciones. |
| `test_junior_no_admite_sencilla` | Junior **no** admite Sencilla. |

### `tests/Feature/HotelApiTest`

| Prueba | Verifica |
|--------|----------|
| `test_lista_los_hoteles` | `GET /api/hotels` (estructura + conteo). |
| `test_crea_un_hotel` | `POST /api/hotels` → 201 y persistencia. |
| `test_no_permite_nombre_repetido` | **CA-2** — nombre único (422). |
| `test_no_permite_nit_repetido` | **CA-2** — NIT único (422). |
| `test_valida_los_campos_requeridos` | 422 con todos los campos obligatorios. |
| `test_muestra_un_hotel` | `GET /api/hotels/{id}`. |
| `test_actualiza_un_hotel` | `PUT /api/hotels/{id}`. |
| `test_no_permite_reducir_habitaciones_por_debajo_de_lo_configurado` | **CA-1** — capacidad al editar. |
| `test_elimina_un_hotel` | `DELETE /api/hotels/{id}` → 204. |

### `tests/Feature/HotelRoomApiTest`

| Prueba | Verifica |
|--------|----------|
| `test_agrega_una_configuracion_valida` | `POST .../rooms` → 201. |
| `test_rechaza_combinacion_invalida` | Combinación tipo↔acomodación inválida (422). |
| `test_rechaza_par_tipo_acomodacion_duplicado` | **CA-3** — par único en el hotel (422). |
| `test_rechaza_cuando_supera_el_maximo` | **CA-1** — la suma no supera el máximo (422). |
| `test_actualiza_una_configuracion` | `PUT .../rooms/{id}`. |
| `test_elimina_una_configuracion` | `DELETE .../rooms/{id}` → 204. |
| `test_no_accede_a_una_habitacion_de_otro_hotel` | Binding *scoped*: 404 si la habitación no pertenece al hotel. |

### `tests/Feature/CatalogApiTest`

| Prueba | Verifica |
|--------|----------|
| `test_lista_ciudades` | `GET /api/cities`. |
| `test_lista_tipos_de_habitacion` | `GET /api/room-types`. |
| `test_lista_acomodaciones` | `GET /api/accommodations`. |
| `test_devuelve_acomodaciones_validas_por_tipo` | `GET /api/room-types/{id}/accommodations` (Estándar → Sencilla, Doble). |

---

## Frontend

Las pruebas de componentes **mockean la capa de API** (`vi.mock('../api/...')`),
de modo que verifican el comportamiento de la UI sin red ni backend.

### `src/components/RoomForm.test.jsx`

| Prueba | Verifica |
|--------|----------|
| carga los tipos de habitación al montar | Se piden los tipos al renderizar. |
| mantiene la acomodación deshabilitada hasta elegir un tipo | UX del combo dependiente. |
| al elegir un tipo carga solo sus acomodaciones válidas | Refleja la regla de combinaciones en el front. |
| envía la configuración y notifica al componente padre | Envío correcto + callback `onAdded`. |

### `src/components/FieldError.test.jsx`

| Prueba | Verifica |
|--------|----------|
| no renderiza nada cuando no hay mensajes | Render condicional. |
| muestra el primer mensaje de error | Presentación del error. |

### `src/pages/HotelsListPage.test.jsx`

| Prueba | Verifica |
|--------|----------|
| muestra los hoteles devueltos por la API | Render del listado. |
| muestra un mensaje cuando no hay hoteles | Estado vacío. |

---

## Mapa de criterios de aceptación → pruebas

| Criterio | Dónde se prueba |
|----------|-----------------|
| **CA-1** — la suma de cantidades no supera el máximo del hotel | `HotelRoomApiTest::test_rechaza_cuando_supera_el_maximo`, `HotelApiTest::test_no_permite_reducir_habitaciones_por_debajo_de_lo_configurado` |
| **CA-2** — no hay hoteles repetidos (nombre y NIT) | `HotelApiTest::test_no_permite_nombre_repetido`, `::test_no_permite_nit_repetido` |
| **CA-3** — no se repite el par (tipo, acomodación) en un hotel | `HotelRoomApiTest::test_rechaza_par_tipo_acomodacion_duplicado` |
| **Combinaciones válidas** tipo ↔ acomodación | `ValidAccommodationForRoomTypeTest` (unit), `HotelRoomApiTest::test_rechaza_combinacion_invalida`, `RoomForm.test.jsx` (front) |
| **CA-4** — catálogos de solo lectura | `CatalogApiTest` |
| **Aislamiento de recursos anidados** | `HotelRoomApiTest::test_no_accede_a_una_habitacion_de_otro_hotel` |

> Las reglas de negocio se validan en el **backend** (fuente de verdad) y se
> reflejan en el **frontend** (UX). Por eso la combinación tipo↔acomodación tiene
> prueba en ambas capas.
