import { useEffect, useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import { getCities } from '../api/catalogs'
import { createHotel, getHotel, updateHotel } from '../api/hotels'
import FieldError from '../components/FieldError'

const EMPTY = { name: '', address: '', city_id: '', nit: '', number_of_rooms: '' }

/**
 * Formulario de creación y edición de hoteles. Reutiliza el mismo componente
 * para ambos casos según exista o no el parámetro :id en la ruta.
 */
export default function HotelFormPage() {
  const { id } = useParams()
  const isEdit = Boolean(id)
  const navigate = useNavigate()

  const [form, setForm] = useState(EMPTY)
  const [cities, setCities] = useState([])
  const [errors, setErrors] = useState({})
  const [saving, setSaving] = useState(false)
  const [loading, setLoading] = useState(isEdit)

  useEffect(() => {
    getCities().then(setCities)

    if (isEdit) {
      getHotel(id)
        .then((hotel) => {
          setForm({
            name: hotel.name,
            address: hotel.address,
            city_id: hotel.city?.id ?? '',
            nit: hotel.nit,
            number_of_rooms: hotel.number_of_rooms,
          })
        })
        .finally(() => setLoading(false))
    }
  }, [id, isEdit])

  const handleChange = (event) => {
    setForm({ ...form, [event.target.name]: event.target.value })
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    setSaving(true)
    setErrors({})

    const payload = {
      ...form,
      city_id: form.city_id ? Number(form.city_id) : null,
      number_of_rooms: form.number_of_rooms ? Number(form.number_of_rooms) : null,
    }

    try {
      if (isEdit) {
        await updateHotel(id, payload)
      } else {
        await createHotel(payload)
      }
      navigate('/hotels')
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors ?? {})
      } else {
        setErrors({ general: ['Ocurrió un error al guardar el hotel.'] })
      }
    } finally {
      setSaving(false)
    }
  }

  if (loading) {
    return <p className="text-slate-500">Cargando…</p>
  }

  const inputClass =
    'mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500'

  return (
    <div className="mx-auto max-w-xl">
      <h1 className="mb-4 text-xl font-semibold">
        {isEdit ? 'Editar hotel' : 'Nuevo hotel'}
      </h1>

      {errors.general && (
        <p className="mb-4 rounded bg-red-50 p-3 text-sm text-red-700">{errors.general[0]}</p>
      )}

      <form onSubmit={handleSubmit} className="space-y-4 rounded border bg-white p-6 shadow-sm">
        <div>
          <label className="block text-sm font-medium" htmlFor="name">
            Nombre
          </label>
          <input id="name" name="name" value={form.name} onChange={handleChange} className={inputClass} />
          <FieldError messages={errors.name} />
        </div>

        <div>
          <label className="block text-sm font-medium" htmlFor="address">
            Dirección
          </label>
          <input id="address" name="address" value={form.address} onChange={handleChange} className={inputClass} />
          <FieldError messages={errors.address} />
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label className="block text-sm font-medium" htmlFor="city_id">
              Ciudad
            </label>
            <select id="city_id" name="city_id" value={form.city_id} onChange={handleChange} className={inputClass}>
              <option value="">Seleccione…</option>
              {cities.map((city) => (
                <option key={city.id} value={city.id}>
                  {city.name}
                </option>
              ))}
            </select>
            <FieldError messages={errors.city_id} />
          </div>

          <div>
            <label className="block text-sm font-medium" htmlFor="nit">
              NIT
            </label>
            <input id="nit" name="nit" value={form.nit} onChange={handleChange} className={inputClass} />
            <FieldError messages={errors.nit} />
          </div>
        </div>

        <div>
          <label className="block text-sm font-medium" htmlFor="number_of_rooms">
            Número de habitaciones
          </label>
          <input
            id="number_of_rooms"
            name="number_of_rooms"
            type="number"
            min="1"
            value={form.number_of_rooms}
            onChange={handleChange}
            className={inputClass}
          />
          <FieldError messages={errors.number_of_rooms} />
        </div>

        <div className="flex items-center justify-end gap-3 pt-2">
          <Link to="/hotels" className="text-sm text-slate-600 hover:underline">
            Cancelar
          </Link>
          <button
            type="submit"
            disabled={saving}
            className="rounded bg-sky-700 px-4 py-2 text-sm font-medium text-white hover:bg-sky-800 disabled:opacity-60"
          >
            {saving ? 'Guardando…' : 'Guardar'}
          </button>
        </div>
      </form>
    </div>
  )
}
