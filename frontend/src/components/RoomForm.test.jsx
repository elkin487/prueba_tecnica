import { beforeEach, describe, expect, it, vi } from 'vitest'
import { render, screen, waitFor } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import RoomForm from './RoomForm'
import { getAccommodationsForRoomType, getRoomTypes } from '../api/catalogs'
import { addRoom } from '../api/hotels'

vi.mock('../api/catalogs')
vi.mock('../api/hotels')

describe('RoomForm', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    getRoomTypes.mockResolvedValue([
      { id: 1, name: 'Estándar' },
      { id: 2, name: 'Junior' },
    ])
    getAccommodationsForRoomType.mockResolvedValue([
      { id: 1, name: 'Sencilla' },
      { id: 2, name: 'Doble' },
    ])
    addRoom.mockResolvedValue({ id: 99 })
  })

  it('carga los tipos de habitación al montar', async () => {
    render(<RoomForm hotelId={1} onAdded={() => {}} />)
    expect(await screen.findByRole('option', { name: 'Estándar' })).toBeInTheDocument()
  })

  it('mantiene la acomodación deshabilitada hasta elegir un tipo', async () => {
    render(<RoomForm hotelId={1} onAdded={() => {}} />)
    await screen.findByRole('option', { name: 'Estándar' })
    expect(screen.getByLabelText('Acomodación')).toBeDisabled()
  })

  it('al elegir un tipo carga solo sus acomodaciones válidas', async () => {
    const user = userEvent.setup()
    render(<RoomForm hotelId={1} onAdded={() => {}} />)
    await screen.findByRole('option', { name: 'Estándar' })

    await user.selectOptions(screen.getByLabelText('Tipo de habitación'), '1')

    expect(getAccommodationsForRoomType).toHaveBeenCalledWith('1')
    expect(await screen.findByRole('option', { name: 'Sencilla' })).toBeInTheDocument()
  })

  it('envía la configuración y notifica al componente padre', async () => {
    const onAdded = vi.fn()
    const user = userEvent.setup()
    render(<RoomForm hotelId={7} onAdded={onAdded} />)
    await screen.findByRole('option', { name: 'Estándar' })

    await user.selectOptions(screen.getByLabelText('Tipo de habitación'), '1')
    await screen.findByRole('option', { name: 'Sencilla' })
    await user.selectOptions(screen.getByLabelText('Acomodación'), '1')
    await user.type(screen.getByLabelText('Cantidad'), '10')
    await user.click(screen.getByRole('button', { name: /agregar/i }))

    await waitFor(() =>
      expect(addRoom).toHaveBeenCalledWith(7, {
        room_type_id: 1,
        accommodation_id: 1,
        quantity: 10,
      })
    )
    expect(onAdded).toHaveBeenCalled()
  })
})
