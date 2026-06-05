import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { deleteHotel, listHotels } from '../api/hotels'

/**
 * Listado de hoteles con accesos a crear, ver, editar y eliminar.
 */
export default function HotelsListPage() {
  const [hotels, setHotels] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [reloadKey, setReloadKey] = useState(0)

  useEffect(() => {
    let active = true
    listHotels()
      .then((data) => {
        if (active) setHotels(data)
      })
      .catch(() => {
        if (active) setError('No se pudieron cargar los hoteles.')
      })
      .finally(() => {
        if (active) setLoading(false)
      })
    return () => {
      active = false
    }
  }, [reloadKey])

  const handleDelete = async (hotel) => {
    if (!window.confirm(`¿Eliminar el hotel "${hotel.name}"?`)) {
      return
    }
    await deleteHotel(hotel.id)
    setReloadKey((key) => key + 1)
  }

  if (loading) {
    return <p className="text-slate-500">Cargando…</p>
  }

  if (error) {
    return <p className="text-red-600">{error}</p>
  }

  return (
    <div>
      <div className="mb-4 flex items-center justify-between">
        <h1 className="text-xl font-semibold">Hoteles</h1>
        <Link
          to="/hotels/new"
          className="rounded bg-sky-700 px-3 py-2 text-sm font-medium text-white hover:bg-sky-800"
        >
          + Nuevo hotel
        </Link>
      </div>

      {hotels.length === 0 ? (
        <p className="rounded border border-dashed bg-white p-8 text-center text-slate-500">
          Aún no hay hoteles registrados.
        </p>
      ) : (
        <div className="overflow-x-auto rounded border bg-white shadow-sm">
          <table className="min-w-full text-sm">
            <thead className="bg-slate-100 text-left text-slate-600">
              <tr>
                <th className="px-3 py-2">Nombre</th>
                <th className="px-3 py-2">Ciudad</th>
                <th className="px-3 py-2">NIT</th>
                <th className="px-3 py-2 text-right">Habitaciones</th>
                <th className="px-3 py-2 text-right">Configuradas</th>
                <th className="px-3 py-2" />
              </tr>
            </thead>
            <tbody>
              {hotels.map((hotel) => (
                <tr key={hotel.id} className="border-t hover:bg-slate-50">
                  <td className="px-3 py-2 font-medium">
                    <Link to={`/hotels/${hotel.id}`} className="text-sky-700 hover:underline">
                      {hotel.name}
                    </Link>
                  </td>
                  <td className="px-3 py-2">{hotel.city?.name ?? '—'}</td>
                  <td className="px-3 py-2">{hotel.nit}</td>
                  <td className="px-3 py-2 text-right">{hotel.number_of_rooms}</td>
                  <td className="px-3 py-2 text-right">
                    {hotel.configured_rooms} / {hotel.number_of_rooms}
                  </td>
                  <td className="whitespace-nowrap px-3 py-2 text-right">
                    <Link
                      to={`/hotels/${hotel.id}/edit`}
                      className="mr-3 text-slate-600 hover:underline"
                    >
                      Editar
                    </Link>
                    <button
                      type="button"
                      onClick={() => handleDelete(hotel)}
                      className="text-red-600 hover:underline"
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  )
}
