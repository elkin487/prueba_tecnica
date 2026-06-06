# Diagrama de Casos de Uso

Actor principal: el **gerente de operaciones hoteleras**, que administra los
hoteles de la compañía y su configuración de habitaciones. Los catálogos son de
solo lectura (no requieren CRUD de administración).

```mermaid
flowchart LR
    gerente(("👤 Gerente de<br/>operaciones"))

    subgraph Hoteles
        uc1(["Listar hoteles"])
        uc2(["Crear hotel"])
        uc3(["Ver detalle del hotel"])
        uc4(["Editar hotel"])
        uc5(["Eliminar hotel"])
    end

    subgraph Habitaciones["Configuración de habitaciones"]
        uc6(["Listar configuración"])
        uc7(["Agregar configuración"])
        uc8(["Editar configuración"])
        uc9(["Eliminar configuración"])
    end

    subgraph Catalogos["Catálogos (solo lectura)"]
        uc10(["Consultar ciudades"])
        uc11(["Consultar tipos de habitación"])
        uc12(["Consultar acomodaciones válidas por tipo"])
    end

    gerente --- uc1 & uc2 & uc3 & uc4 & uc5
    gerente --- uc6 & uc7 & uc8 & uc9
    gerente --- uc10 & uc11 & uc12

    uc2 -. include .-> v1{{"Validar unicidad de nombre y NIT"}}
    uc7 -. include .-> v2{{"Validar combinación tipo↔acomodación"}}
    uc7 -. include .-> v3{{"Validar no superar el máximo de habitaciones"}}
    uc7 -. include .-> v4{{"Validar par (tipo, acomodación) no repetido"}}
    uc8 -. include .-> v2
    uc8 -. include .-> v3
    uc8 -. include .-> v4
    uc7 -. extend .-> uc12
```

## Detalle de los casos de uso con reglas de negocio

| Caso de uso | Reglas que aplica |
|-------------|-------------------|
| **Crear hotel** | Nombre único · NIT único · `number_of_rooms` > 0. |
| **Agregar / editar configuración** | La acomodación debe ser válida para el tipo (catálogo de combinaciones) · el par *(tipo, acomodación)* no puede repetirse en el hotel · la suma de cantidades no puede superar `number_of_rooms`. |
| **Consultar catálogos** | Solo lectura; alimentan los combos del formulario (incl. acomodaciones válidas por tipo, que habilita/deshabilita opciones en la UI). |

> Todas las validaciones son **autoritativas en el backend**. El frontend las
> refleja en la UX (deshabilitar opciones inválidas, mostrar errores), pero nunca
> reemplaza la validación del servidor.
