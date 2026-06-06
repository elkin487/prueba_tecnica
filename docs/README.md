# Documentación — Sistema de Hoteles Decameron

Documentación técnica del proyecto. Para la guía de instalación y ejecución,
consulta el [README principal](../README.md).

## Contenido

### Diagramas UML

- [Entidad-Relación (ERD)](uml/entidad-relacion.md) — modelo de datos y restricciones.
- [Diagrama de Clases](uml/clases.md) — arquitectura en capas (SOLID) y dominio Eloquent.
- [Casos de Uso](uml/casos-de-uso.md) — acciones del gerente de operaciones.
- [Secuencia](uml/secuencia.md) — flujo de validación al configurar habitaciones.

> Los diagramas están escritos en **Mermaid** y se renderizan automáticamente al
> abrir cada archivo en GitHub.

### API

- [Contrato de la API REST](api.md) — endpoints, cuerpos, respuestas y códigos HTTP.

### Base de datos

- [Dump y restauración](database/README.md) — cómo importar `dump.sql`
  (estructura + catálogos).

## Arquitectura en una línea

```
SPA React (Vite)  ──HTTP/JSON──►  API Laravel  ──►  PostgreSQL
                                  Controller → FormRequest → Service → Repository → Model
```

Front y back están **desacoplados**: se comunican solo por la API REST.
