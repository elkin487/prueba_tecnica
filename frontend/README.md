# Frontend — SPA Hoteles Decameron (React + Vite)

Single Page Application que consume la API REST del backend para gestionar
hoteles y la configuración de sus habitaciones. Forma parte del
[monorepo de la prueba técnica](../README.md).

## 🧰 Stack

- **React 19** + **Vite**
- **React Router** (navegación) · **Axios** (cliente HTTP)
- **Tailwind CSS** (estilos, responsive 13"/15")
- **Vitest** + **React Testing Library** (pruebas) · **ESLint** (lint)

## 📂 Estructura principal

```
src/
├── api/            # client.js (Axios) + servicios (hotels, catalogs)
├── components/     # Layout, RoomForm, FieldError (+ sus pruebas)
├── pages/          # HotelsListPage, HotelFormPage, HotelDetailPage
├── main.jsx        # punto de entrada
└── index.css       # estilos base (Tailwind)
```

## ⚙️ Puesta en marcha

```bash
npm install
cp .env.example .env     # configura VITE_API_URL
npm run dev              # http://localhost:5173
```

### Variable de entorno

La URL base de la API se toma de `VITE_API_URL` (ver `.env.example`):

```env
# Con el backend levantado vía "php artisan serve":
VITE_API_URL=http://127.0.0.1:8000/api
```

> El cliente Axios está centralizado en `src/api/client.js`. Si la variable no se
> define, usa un valor por defecto pensado para entornos locales con Herd.

## 📜 Scripts

| Comando | Descripción |
|---------|-------------|
| `npm run dev` | Servidor de desarrollo (HMR). |
| `npm run build` | Build de producción en `dist/`. |
| `npm run preview` | Previsualiza el build de producción. |
| `npm run lint` | Verifica el código con ESLint. |
| `npm run test` | Ejecuta las pruebas (Vitest). |

## 🧪 Pruebas

```bash
npm run test
```

Cubren los componentes clave: el combo dependiente tipo → acomodación de
`RoomForm`, el renderizado de errores (`FieldError`) y el listado de hoteles.

## ♿ Responsive

La interfaz está pensada y verificada para portátiles de **13" y 15"** en
**Firefox** y **Chrome**.
