import { Link, Outlet } from 'react-router-dom'

/**
 * Estructura común de la aplicación: cabecera + contenedor responsive.
 * El ancho máximo (max-w-5xl) se ajusta cómodamente a portátiles de 13"/15".
 */
export default function Layout() {
  return (
    <div className="min-h-screen bg-slate-50 text-slate-800">
      <header className="bg-sky-700 text-white shadow">
        <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
          <Link to="/hotels" className="text-lg font-semibold tracking-tight">
            🏨 Hoteles Decameron
          </Link>
          <nav className="text-sm">
            <Link to="/hotels" className="hover:underline">
              Hoteles
            </Link>
          </nav>
        </div>
      </header>

      <main className="mx-auto max-w-5xl px-4 py-6">
        <Outlet />
      </main>

      <footer className="mx-auto max-w-5xl px-4 py-6 text-center text-xs text-slate-400">
        Prueba técnica · Sistema de hoteles
      </footer>
    </div>
  )
}
