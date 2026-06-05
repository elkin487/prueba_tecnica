# Prueba Técnica — Sistema de Hoteles Decameron

Sistema web para registrar hoteles de la compañía y configurarles tipos de habitación con
sus acomodaciones, aplicando validaciones de negocio. Desarrollado como prueba técnica.

## 🏗️ Arquitectura

Aplicación **desacoplada** (front y back independientes, comunicados vía API REST/JSON):

- **Backend:** API RESTful en **Laravel** (PHP) sobre **PostgreSQL**.
- **Frontend:** SPA en **React** (Vite), responsive para portátiles de 13"/15" (Firefox y Chrome).

## 📂 Estructura del repositorio (monorepo)

```
.
├── backend/      # API Laravel (se crea en la Fase 1)
├── frontend/     # SPA React  (se crea en la Fase 4)
├── docs/         # Documentación: misión/plan, diagramas UML, dump de BD
└── README.md     # Este archivo
```

## 🧰 Stack

| Capa          | Tecnología                          |
|---------------|-------------------------------------|
| Backend       | Laravel (PHP 8.2+) — API RESTful    |
| Frontend      | React + Vite                        |
| Base de datos | PostgreSQL                          |
| Tests         | PHPUnit (back) · Vitest + RTL (front) |
| CI            | GitHub Actions                      |
| Despliegue    | DigitalOcean                        |

## ✅ Requisitos que cubre

- Diseño Responsive · Integración Continua · Pruebas Unitarias
- Buenas prácticas: SOLID, patrones de diseño, código documentado
- API totalmente RESTful, front/back desacoplados
- Documentación con diagramas UML y dump de la base de datos

## 🚀 Cómo ejecutar

> ⏳ _Pendiente — se documentará el paso a paso de instalación y despliegue conforme avancen
> las fases (ver `docs/MISION.md`)._

## 📋 Plan del proyecto

El plan completo por fases, las reglas de negocio y los criterios de aceptación están en
[`docs/MISION.md`](docs/MISION.md).
