import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { deleteRoom, getHotel } from '../api/hotels'
import RoomForm from '../components/RoomForm'

/**
 * Detalle de un hotel: datos básicos y gestión de sus configuraciones de
 * habitaciones (agregar y eliminar).
 */
export default function HotelDetailPage() {
  const { id } = useParams()
  const [hotel, setHotel] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)
  const [reloadKey, setReloadKey] = useState(0)

  const refresh = () => setReloadKey((key) => key + 1)

  useEffect(() => {
    let active = true
    getHotel(id)
      .then((data) => {
        if (active) setHotel(data)
      })
      .catch(() => {
        if (active) setError('No se pudo cargar el hotel.')
      })
      .finally(() => {
        if (active) setLoading(false)
      })
    return () => {
      active = false
    }
  }, [id, reloadKey])

  const handleDeleteRoom = async (room) => {
    if (!window.confirm('¿Eliminar esta configuración?')) {
      return
    }
    await deleteRoom(id, room.id)
    refresh()
  }

  if (loading) {
    return <p className="text-slate-500">Cargando…</p>
  }

  if (error) {
    return <p className="text-red-600">{error}</p>
  }

  return (
    <div className="space-y-6">
      <div>
        <Link to="/hotels" className="text-sm text-sky-700 hover:underline">
          ← Volver a hoteles
        </Link>
      </div>

      <div className="rounded border bg-white p-6 shadow-sm">
        <div className="flex flex-wrap items-start justify-between gap-4">
          <div>
            <h1 className="text-xl font-semibold">{hotel.name}</h1>
            <p className="text-sm text-slate-500">
              {hotel.address} · {hotel.city?.name}
            </p>
            <p className="mt-1 text-sm text-slate-500">NIT: {hotel.nit}</p>
          </div>
          <div className="text-right text-sm">
            <p>
              <span className="font-semibold">{hotel.configured_rooms}</span> /{' '}
              {hotel.number_of_rooms} configuradas
            </p>
            <p className="text-slate-500">{hotel.available_rooms} disponibles</p>
            <Link
              to={`/hotels/${hotel.id}/edit`}
              className="mt-2 inline-block text-sky-700 hover:underline"
            >
              Editar hotel
            </Link>
          </div>
        </div>
      </div>

      <div>
        <h2 className="mb-2 text-lg font-semibold">Configuración de habitaciones</h2>

        {hotel.rooms.length === 0 ? (
          <p className="mb-4 rounded border border-dashed bg-white p-6 text-center text-slate-500">
            Este hotel aún no tiene habitaciones configuradas.
          </p>
        ) : (
          <div className="mb-4 overflow-x-auto rounded border bg-white shadow-sm">
            <table className="min-w-full text-sm">
              <thead className="bg-slate-100 text-left text-slate-600">
                <tr>
                  <th className="px-3 py-2">Tipo</th>
                  <th className="px-3 py-2">Acomodación</th>
                  <th className="px-3 py-2 text-right">Cantidad</th>
                  <th className="px-3 py-2" />
                </tr>
              </thead>
              <tbody>
                {hotel.rooms.map((room) => (
                  <tr key={room.id} className="border-t">
                    <td className="px-3 py-2">{room.room_type?.name}</td>
                    <td className="px-3 py-2">{room.accommodation?.name}</td>
                    <td className="px-3 py-2 text-right">{room.quantity}</td>
                    <td className="whitespace-nowrap px-3 py-2 text-right">
                      <button
                        type="button"
                        onClick={() => handleDeleteRoom(room)}
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

        <RoomForm hotelId={hotel.id} onAdded={refresh} />
      </div>
    </div>
  )
}
