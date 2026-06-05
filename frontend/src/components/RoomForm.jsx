import { useEffect, useState } from 'react'
import { getAccommodationsForRoomType, getRoomTypes } from '../api/catalogs'
import { addRoom } from '../api/hotels'
import FieldError from './FieldError'

const EMPTY = { room_type_id: '', accommodation_id: '', quantity: '' }

/**
 * Formulario para agregar una configuración de habitaciones a un hotel.
 * Al elegir el tipo de habitación, las acomodaciones disponibles se cargan
 * dinámicamente desde la API mostrando solo las combinaciones válidas.
 */
export default function RoomForm({ hotelId, onAdded }) {
  const [roomTypes, setRoomTypes] = useState([])
  const [accommodations, setAccommodations] = useState([])
  const [form, setForm] = useState(EMPTY)
  const [errors, setErrors] = useState({})
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    getRoomTypes().then(setRoomTypes)
  }, [])

  const handleTypeChange = async (event) => {
    const roomTypeId = event.target.value
    // Al cambiar el tipo se reinicia la acomodación seleccionada.
    setForm({ ...form, room_type_id: roomTypeId, accommodation_id: '' })

    // Cargar dinámicamente solo las acomodaciones válidas para el tipo elegido.
    if (!roomTypeId) {
      setAccommodations([])
      return
    }
    setAccommodations(await getAccommodationsForRoomType(roomTypeId))
  }

  const handleChange = (event) => {
    setForm({ ...form, [event.target.name]: event.target.value })
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    setSaving(true)
    setErrors({})

    try {
      await addRoom(hotelId, {
        room_type_id: Number(form.room_type_id),
        accommodation_id: Number(form.accommodation_id),
        quantity: Number(form.quantity),
      })
      setForm(EMPTY)
      setAccommodations([])
      onAdded()
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors ?? {})
      } else {
        setErrors({ general: ['No se pudo agregar la configuración.'] })
      }
    } finally {
      setSaving(false)
    }
  }

  const inputClass =
    'mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500'

  return (
    <form onSubmit={handleSubmit} className="rounded border bg-white p-4 shadow-sm">
      <h3 className="mb-3 text-sm font-semibold text-slate-700">Agregar configuración</h3>

      {errors.general && (
        <p className="mb-3 rounded bg-red-50 p-2 text-sm text-red-700">{errors.general[0]}</p>
      )}

      <div className="grid grid-cols-1 gap-3 sm:grid-cols-4 sm:items-end">
        <div>
          <label className="block text-xs font-medium text-slate-600" htmlFor="room_type_id">
            Tipo de habitación
          </label>
          <select
            id="room_type_id"
            name="room_type_id"
            value={form.room_type_id}
            onChange={handleTypeChange}
            className={inputClass}
          >
            <option value="">Seleccione…</option>
            {roomTypes.map((type) => (
              <option key={type.id} value={type.id}>
                {type.name}
              </option>
            ))}
          </select>
          <FieldError messages={errors.room_type_id} />
        </div>

        <div>
          <label className="block text-xs font-medium text-slate-600" htmlFor="accommodation_id">
            Acomodación
          </label>
          <select
            id="accommodation_id"
            name="accommodation_id"
            value={form.accommodation_id}
            onChange={handleChange}
            disabled={!form.room_type_id}
            className={`${inputClass} disabled:bg-slate-100`}
          >
            <option value="">{form.room_type_id ? 'Seleccione…' : 'Elija un tipo primero'}</option>
            {accommodations.map((acc) => (
              <option key={acc.id} value={acc.id}>
                {acc.name}
              </option>
            ))}
          </select>
          <FieldError messages={errors.accommodation_id} />
        </div>

        <div>
          <label className="block text-xs font-medium text-slate-600" htmlFor="quantity">
            Cantidad
          </label>
          <input
            id="quantity"
            name="quantity"
            type="number"
            min="1"
            value={form.quantity}
            onChange={handleChange}
            className={inputClass}
          />
          <FieldError messages={errors.quantity} />
        </div>

        <button
          type="submit"
          disabled={saving}
          className="h-[38px] rounded bg-sky-700 px-3 text-sm font-medium text-white hover:bg-sky-800 disabled:opacity-60"
        >
          {saving ? 'Agregando…' : 'Agregar'}
        </button>
      </div>
    </form>
  )
}
