# MISIÓN, PLAN Y REGLAS — Prueba Técnica Decameron

> Documento maestro del proyecto. Define **qué** vamos a construir, **cómo** lo haremos
> por fases y **bajo qué reglas** trabajaremos. Es la fuente de verdad: ante cualquier
> duda de alcance o criterio, este archivo manda.

---

## 1. Misión

Construir y desplegar un sistema web que permita a **Hoteles Decameron de Colombia**
registrar sus hoteles (datos básicos + tributarios) y configurarles tipos de habitación
con sus acomodaciones, aplicando validaciones de negocio estrictas.

El entregable debe **evidenciar de forma demostrable** dominio de buenas prácticas de
ingeniería de software, y será calificado explícitamente sobre estos cinco pilares:

1. **Buenas prácticas** — SOLID, patrones de diseño, código documentado.
2. **Framework de frontend** — React.
3. **Framework de PHP** — Laravel.
4. **Entrega en GIT** — repositorio público.
5. **Evidencia obligatoria** de: Diseño Responsive · Integración Continua · Pruebas Unitarias.

**Definición de éxito:** un evaluador clona el repositorio, sigue el `README` "para la
abuelita", levanta la aplicación sin fricción (o entra por el link en la nube), prueba el
flujo completo de hoteles + habitaciones, ve los tests pasando, el pipeline de CI en verde
y la UI funcionando correctamente en un portátil de 13"/15" en Firefox y Chrome.

---

## 2. Contexto del negocio (dominio)

El gerente de operaciones hoteleras necesita administrar hoteles. Cada hotel tiene:

| Campo            | Ejemplo               |
|------------------|-----------------------|
| Nombre           | DECAMERON CARTAGENA   |
| Dirección        | CALLE 23 58-25        |
| Ciudad           | CARTAGENA             |
| NIT              | 12345678-9            |
| Número de Hab.   | 42                    |

A cada hotel se le configuran filas de **(cantidad · tipo de habitación · acomodación)**:

| Cantidad | Tipo Habitación | Acomodación |
|----------|-----------------|-------------|
| 25       | ESTÁNDAR        | SENCILLA    |
| 12       | JUNIOR          | TRIPLE      |
| 5        | ESTÁNDAR        | DOBLE       |

---

## 3. Reglas de negocio (validaciones obligatorias)

Estas reglas se validan en el **backend** (fuente de verdad) y se reflejan en el **frontend**
(UX: deshabilitar opciones inválidas, mostrar errores claros).

### 3.1 Combinaciones válidas tipo ↔ acomodación

| Tipo de habitación | Acomodaciones permitidas        |
|--------------------|---------------------------------|
| **Estándar**       | Sencilla, Doble                 |
| **Junior**         | Triple, Cuádruple               |
| **Suite**          | Sencilla, Doble, Triple         |

> Estas combinaciones se modelan como datos de catálogo (tabla pivote sembrada), **no**
> como condicionales `if` hardcodeados — así son extensibles sin tocar código.

### 3.2 Criterios de aceptación

- **CA-1** La suma de las cantidades configuradas **no debe superar** el número de
  habitaciones del hotel.
- **CA-2** **No deben existir hoteles repetidos** (nombre único; el NIT también debe ser único).
- **CA-3** **No debe repetirse** el par *(tipo de habitación, acomodación)* dentro del mismo
  hotel.
- **CA-4** Los catálogos (ciudades, tipos de habitación, acomodaciones) **no requieren CRUD
  de administración**: se siembran con *seeders* y se exponen solo como lectura.
- **CA-5** Responsive para portátiles de **13" y 15"**; soportado en **Firefox y Chrome**.

---

## 4. Stack tecnológico

| Capa            | Tecnología                                              |
|-----------------|---------------------------------------------------------|
| Backend         | **Laravel** (PHP 8.2+), API **RESTful** y desacoplada   |
| Frontend        | **React** (Vite) + React Router + Axios                 |
| Base de datos   | **PostgreSQL**                                           |
| Estilos         | (a definir en Fase 0: Tailwind CSS recomendado)         |
| Tests back      | **PHPUnit** (o Pest) — Feature + Unit                   |
| Tests front     | **Vitest** + React Testing Library                      |
| Calidad código  | Laravel **Pint** (back) · ESLint + Prettier (front)     |
| CI              | **GitHub Actions**                                      |
| Repositorio     | **Monorepo** Git público (`/backend` + `/frontend`)     |
| Despliegue      | **DigitalOcean** (App Platform o Droplet + Managed PG)  |

---

## 5. Arquitectura

```
┌──────────────────────────┐         HTTP/JSON (REST)        ┌──────────────────────────┐
│   Frontend (React/Vite)  │  ───────────────────────────▶  │   Backend (Laravel API)  │
│  - Componentes UI        │  ◀───────────────────────────  │  - Controllers (REST)    │
│  - Servicios (Axios)     │                                 │  - Form Requests         │
│  - Estado / hooks        │                                 │  - Services (lógica)     │
│  - Responsive (13"/15")  │                                 │  - Repositories          │
└──────────────────────────┘                                 │  - API Resources         │
                                                              │  - Eloquent Models       │
                                                              └────────────┬─────────────┘
                                                                           │
                                                                  ┌────────▼────────┐
                                                                  │  PostgreSQL     │
                                                                  └─────────────────┘
```

**Backend en capas (separación de responsabilidades / SOLID):**
- **Controller** → solo orquesta (recibe request, delega, responde). Sin lógica de negocio.
- **Form Request** → validación de entrada + reglas custom (combinación válida, máximo).
- **Service** → lógica de negocio (única razón de cambio por servicio).
- **Repository** → acceso a datos (abstrae Eloquent; testeable / sustituible).
- **API Resource** → serialización de salida consistente.
- **Model** → entidad Eloquent + relaciones.

---

## 6. Modelo de datos

**Catálogos (solo lectura, sembrados con seeders):**

- `cities` — `id`, `name`
- `room_types` — `id`, `name`  → *Estándar, Junior, Suite*
- `accommodations` — `id`, `name`  → *Sencilla, Doble, Triple, Cuádruple*
- `room_type_accommodation` (pivote de combinaciones válidas) — `room_type_id`, `accommodation_id`

**Entidades principales:**

- `hotels` — `id`, `name` (UNIQUE), `address`, `city_id` (FK), `nit` (UNIQUE),
  `number_of_rooms` (entero > 0), `timestamps`
- `hotel_rooms` (configuración de habitaciones) — `id`, `hotel_id` (FK),
  `room_type_id` (FK), `accommodation_id` (FK), `quantity` (entero > 0), `timestamps`
  - **UNIQUE** `(hotel_id, room_type_id, accommodation_id)` → cumple **CA-3**

```
cities 1───* hotels 1───* hotel_rooms *───1 room_types
                                     *───1 accommodations
room_types *───* accommodations  (vía room_type_accommodation: combinaciones válidas)
```

---

## 7. API REST (contrato)

Base: `/api`. Respuestas JSON. Errores con códigos HTTP correctos (422 validación, 404, etc.).

**Hoteles**
- `GET    /api/hotels` — listar (paginado)
- `POST   /api/hotels` — crear
- `GET    /api/hotels/{id}` — detalle (con habitaciones)
- `PUT    /api/hotels/{id}` — actualizar
- `DELETE /api/hotels/{id}` — eliminar

**Configuración de habitaciones del hotel**
- `GET    /api/hotels/{id}/rooms` — listar configuración
- `POST   /api/hotels/{id}/rooms` — agregar configuración
- `PUT    /api/hotels/{id}/rooms/{roomId}` — actualizar
- `DELETE /api/hotels/{id}/rooms/{roomId}` — eliminar

**Catálogos (lectura)**
- `GET /api/cities`
- `GET /api/room-types`
- `GET /api/accommodations`
- `GET /api/room-types/{id}/accommodations` — acomodaciones válidas para un tipo

---

## 8. Plan por fases

> Cada fase termina con su evidencia (commit, test verde, captura o link). No se avanza a la
> siguiente sin cerrar la anterior.

### Fase 0 — Cimientos
- [ ] Inicializar **monorepo** Git público (`/backend`, `/frontend`, `/docs`, `README.md`).
- [ ] `.gitignore`, licencia, estructura de carpetas.
- [ ] Decidir librería de estilos (recomendado Tailwind) y convención de commits.
- [ ] Primer commit + push al repo remoto público.

### Fase 1 — Backend: base y datos
- [ ] Crear proyecto Laravel + conexión PostgreSQL (`.env`, `.env.example`).
- [ ] Migraciones (sección 6) + restricciones UNIQUE.
- [ ] Seeders de catálogos (ciudades, tipos, acomodaciones, combinaciones válidas).
- [ ] Modelos Eloquent + relaciones.

### Fase 2 — Backend: API + lógica de negocio
- [ ] Controllers REST (resource controllers).
- [ ] Form Requests + reglas custom (combinación válida CA-3.1, máximo CA-1, unicidad CA-2/CA-3).
- [ ] Capa de Services + Repositories.
- [ ] API Resources (serialización).
- [ ] Manejo de errores y respuestas JSON consistentes.
- [ ] CORS configurado (front desacoplado).

### Fase 3 — Backend: pruebas unitarias
- [ ] Feature tests de cada endpoint (happy path + errores 422/404).
- [ ] Unit tests de las reglas de negocio (combinaciones, máximo, duplicados).
- [ ] Cobertura de los criterios de aceptación CA-1…CA-3.

### Fase 4 — Frontend: React
- [ ] Proyecto React (Vite) + Axios + React Router.
- [ ] Listado de hoteles, crear/editar/eliminar hotel.
- [ ] Gestión de habitaciones por hotel (combobox dependiente tipo→acomodación válida).
- [ ] Validaciones reflejadas en UI + mensajes de error claros.
- [ ] **Responsive** verificado a 13"/15" en Firefox y Chrome.

### Fase 5 — Frontend: pruebas unitarias
- [ ] Tests de componentes clave (formularios, lista, validación de combinaciones) con Vitest + RTL.

### Fase 6 — Integración Continua
- [ ] GitHub Actions: job **backend** (Pint + PHPUnit con servicio PostgreSQL).
- [ ] GitHub Actions: job **frontend** (ESLint + Vitest + build).
- [ ] Badge de estado del pipeline en el `README`. Pipeline en **verde**.

### Fase 7 — Despliegue en DigitalOcean
- [ ] Provisionar app + base de datos PostgreSQL gestionada en DigitalOcean.
- [ ] Variables de entorno, migraciones y seeders en producción.
- [ ] Verificar la app pública en Firefox y Chrome. Obtener **link de acceso**.

### Fase 8 — Documentación y entrega
- [ ] `README` con guía de despliegue **paso a paso "para la abuelita"**.
- [ ] Diagramas **UML** en `/docs` (clases, entidad-relación, casos de uso; secuencia opcional).
- [ ] **Dump** de la BD (`pg_dump`) listo para instalar.
- [ ] Link de la app desplegada + link del repositorio.
- [ ] Revisión final contra el checklist de la sección 10.

---

## 9. Buenas prácticas a evidenciar (cómo se califica)

- **SOLID** — controllers delgados, servicios con responsabilidad única, repositorios que
  abstraen datos, reglas de validación como clases (`Rule` objects) en lugar de `if` dispersos.
- **Patrones de diseño** — Repository, Service Layer, Form Request (Validation), API Resource
  (DTO de salida); inyección de dependencias vía contenedor de Laravel.
- **Código documentado** — PHPDoc en métodos públicos, comentarios en reglas de negocio no
  obvias, nombres expresivos; JSDoc/comentarios en componentes complejos del front.
- **REST correcto** — verbos y códigos HTTP adecuados, recursos en plural, sin estado.
- **Commits atómicos** y mensajes claros que cuenten la historia del proyecto.

---

## 10. Reglas de trabajo (las seguimos siempre)

1. **El backend es la fuente de verdad** de toda validación. El frontend mejora la UX pero
   nunca reemplaza la validación del servidor.
2. **Nada se hardcodea** si puede ser dato de catálogo (ej.: combinaciones tipo↔acomodación).
3. **Front y back desacoplados** — se comunican solo por la API REST/JSON. Sin vistas Blade
   sirviendo el front.
4. **Toda regla de negocio nueva nace con su prueba** (test primero o junto al código).
5. **No se hace merge con el CI en rojo.** El pipeline debe quedar siempre en verde.
6. **Commits pequeños y atómicos**, en español, descriptivos. Push frecuente al repo público.
7. **Secretos fuera de Git** — `.env` ignorado; siempre mantener `.env.example` actualizado.
8. **Responsive primero para 13"/15"** y verificado en Firefox y Chrome antes de dar por
   cerrada cualquier vista.
9. **La documentación se actualiza junto al código**, no al final como relleno.
10. **Idioma:** comunicación y documentación en **español**; identificadores de código en
    inglés (convención estándar).
11. **Cerrar fase = tener evidencia** (test verde, captura o link). Sin evidencia, la fase
    sigue abierta.
12. Ante decisiones de alcance ambiguas, **preguntar antes de asumir**.

---

## 11. Entregables finales (checklist de entrega)

- [ ] Repositorio **Git público** con el monorepo completo.
- [ ] **Código** backend (Laravel) + frontend (React) funcional y desacoplado.
- [ ] **API RESTful** completa sobre **PostgreSQL**.
- [ ] **Pruebas unitarias** back y front, pasando.
- [ ] **Integración Continua** (GitHub Actions) en verde, con badge.
- [ ] **Diseño responsive** verificado (13"/15", Firefox y Chrome).
- [ ] **Despliegue en DigitalOcean** + **link** de acceso público.
- [ ] **Dump** de la base de datos listo para instalar.
- [ ] **README** con paso a paso de despliegue "para la abuelita".
- [ ] **Diagramas UML** y documentación en `/docs`.
- [ ] Buenas prácticas evidenciadas: SOLID, patrones, código documentado.

---

## 12. Notas / decisiones abiertas

- **Estilos:** Tailwind CSS recomendado (rápido + responsive utilitario). Confirmar en Fase 0.
- **Tests backend:** PHPUnit (estándar de Laravel) salvo preferencia por Pest.
- **DigitalOcean:** elegir entre **App Platform** (más simple, recomendado para la prueba) o
  **Droplet + Managed PostgreSQL** (más control). Se decide en Fase 7.
- **Autenticación:** el instructivo no la exige; se omite salvo indicación contraria (mantener
  el alcance enfocado en lo solicitado).
