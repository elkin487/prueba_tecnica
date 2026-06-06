# Prueba Técnica — Sistema de Hoteles Decameron

[![CI](https://github.com/elkin487/prueba_tecnica/actions/workflows/ci.yml/badge.svg)](https://github.com/elkin487/prueba_tecnica/actions/workflows/ci.yml)

Sistema web para registrar los hoteles de la compañía y configurarles tipos de
habitación con sus acomodaciones, aplicando validaciones de negocio. Desarrollado
como prueba técnica.

> 🌐 **Aplicación desplegada:** _pendiente (se publicará el enlace tras el despliegue)._

## 🏗️ Arquitectura

Aplicación **desacoplada**: el frontend y el backend son independientes y se
comunican únicamente vía API REST/JSON.

```
SPA React (Vite)  ──HTTP/JSON──►  API Laravel  ──►  PostgreSQL
                                  Controller → FormRequest → Service → Repository → Model
```

- **Backend:** API RESTful en **Laravel** (PHP) sobre **PostgreSQL**.
- **Frontend:** SPA en **React** (Vite) + Tailwind, responsive para portátiles de 13"/15".

## 🧰 Stack

| Capa          | Tecnología                              |
|---------------|-----------------------------------------|
| Backend       | Laravel 13 (PHP 8.3+) — API RESTful     |
| Frontend      | React 19 + Vite + Tailwind CSS          |
| Base de datos | PostgreSQL                              |
| Pruebas       | PHPUnit (back) · Vitest + RTL (front)   |
| Calidad       | Laravel Pint (back) · ESLint (front)    |
| CI            | GitHub Actions                          |

## 📂 Estructura del repositorio

```
.
├── backend/      # API Laravel (PostgreSQL)   → ver backend/README.md
├── frontend/     # SPA React (Vite)           → ver frontend/README.md
├── docs/         # Documentación: UML, API, dump de BD → docs/README.md
└── README.md     # Este archivo
```

---

## ✅ Requisitos previos

Antes de empezar, instala en tu equipo:

| Herramienta | Versión mínima | Para qué |
|-------------|----------------|----------|
| **PHP**      | 8.3   | Ejecutar el backend Laravel |
| **Composer** | 2.x   | Dependencias de PHP |
| **Node.js**  | 20+   | Ejecutar el frontend (recomendado 22) |
| **npm**      | 10+   | Dependencias de JavaScript |
| **PostgreSQL** | 14+ | Base de datos |

> Verifica que PostgreSQL esté **encendido** y que conoces el usuario y la
> contraseña (por defecto, este proyecto usa el usuario `postgres`).

---

## 🚀 Instalación paso a paso

Copia y pega los comandos en una terminal. Son dos partes: **backend** y **frontend**.

### 0. Clonar el repositorio

```bash
git clone https://github.com/elkin487/prueba_tecnica.git
cd prueba_tecnica
```

### 1. Backend (API Laravel)

```bash
cd backend

# 1.1 Instalar dependencias de PHP
composer install

# 1.2 Crear el archivo de configuración
cp .env.example .env

# 1.3 Generar la clave de la aplicación
php artisan key:generate
```

Ahora **edita `backend/.env`** y ajusta los datos de tu PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=prueba_tecnica
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña   # déjalo vacío si tu PostgreSQL no usa contraseña
```

Crea la base de datos (si aún no existe) y carga el esquema + los catálogos:

```bash
# 1.4 Crear la base de datos (una de estas opciones)
createdb -h 127.0.0.1 -U postgres prueba_tecnica
#   …o desde psql:  CREATE DATABASE prueba_tecnica;
#   …o desde tu gestor gráfico (TablePlus, DBeaver, pgAdmin).

# 1.5 Migrar y sembrar los catálogos (ciudades, tipos, acomodaciones, combinaciones)
php artisan migrate --seed
```

> 💡 **Alternativa al paso 1.5:** en lugar de `migrate --seed` puedes importar el
> dump ya preparado. Ver [docs/database/README.md](docs/database/README.md).

### 2. Frontend (SPA React)

En **otra terminal**, desde la raíz del proyecto:

```bash
cd frontend

# 2.1 Instalar dependencias
npm install

# 2.2 Crear el archivo de configuración
cp .env.example .env
```

Edita `frontend/.env` para que apunte a tu backend:

```env
# Si levantas el backend con "php artisan serve":
VITE_API_URL=http://127.0.0.1:8000/api
```

---

## ▶️ Ejecutar la aplicación

Necesitas el backend y el frontend corriendo a la vez (dos terminales):

```bash
# Terminal 1 — Backend (queda en http://127.0.0.1:8000)
cd backend && php artisan serve

# Terminal 2 — Frontend (queda en http://localhost:5173)
cd frontend && npm run dev
```

Abre **http://localhost:5173** en **Chrome** o **Firefox**. ¡Listo! 🎉

---

## 🧪 Ejecutar las pruebas

```bash
# Backend — pruebas de dominio y de la API (PHPUnit)
cd backend && php artisan test

# Frontend — pruebas de componentes (Vitest)
cd frontend && npm run test
```

Y la verificación de estilo/lint (lo mismo que valida el CI):

```bash
cd backend  && ./vendor/bin/pint --test   # estilo PHP
cd frontend && npm run lint                # ESLint
```

---

## 📐 Reglas de negocio (resumen)

- **Combinaciones válidas** tipo ↔ acomodación (modeladas como datos de catálogo):
  Estándar → Sencilla/Doble · Junior → Triple/Cuádruple · Suite → Sencilla/Doble/Triple.
- La **suma de cantidades** configuradas no puede superar el número de habitaciones del hotel.
- **No se repiten** hoteles (nombre y NIT únicos) ni el par *(tipo, acomodación)* dentro de un hotel.
- Los **catálogos** son de solo lectura (se siembran con seeders).

Todas las validaciones son autoritativas en el backend. Detalle en
[docs/api.md](docs/api.md).

---

## 📚 Documentación

- [Documentación técnica (índice)](docs/README.md)
- [Diagramas UML](docs/uml/) — ERD, clases, casos de uso, secuencia.
- [Contrato de la API REST](docs/api.md)
- [Dump de la base de datos](docs/database/README.md)

---

## ☁️ Despliegue

> ⏳ _Pendiente._ La guía de despliegue en DigitalOcean y el enlace público se
> documentarán al cerrar la fase de despliegue.
