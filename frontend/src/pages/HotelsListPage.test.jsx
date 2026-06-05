import { beforeEach, describe, expect, it, vi } from 'vitest'
import { render, screen } from '@testing-library/react'
import { MemoryRouter } from 'react-router-dom'
import HotelsListPage from './HotelsListPage'
import { listHotels } from '../api/hotels'

vi.mock('../api/hotels')

const renderPage = () =>
  render(
    <MemoryRouter>
      <HotelsListPage />
    </MemoryRouter>
  )

describe('HotelsListPage', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('muestra los hoteles devueltos por la API', async () => {
    listHotels.mockResolvedValue([
      {
        id: 1,
        name: 'DECAMERON CARTAGENA',
        city: { name: 'Cartagena' },
        nit: '12345678-9',
        number_of_rooms: 42,
        configured_rooms: 25,
      },
    ])

    renderPage()

    expect(await screen.findByText('DECAMERON CARTAGENA')).toBeInTheDocument()
    expect(screen.getByText('Cartagena')).toBeInTheDocument()
  })

  it('muestra un mensaje cuando no hay hoteles', async () => {
    listHotels.mockResolvedValue([])

    renderPage()

    expect(await screen.findByText(/no hay hoteles registrados/i)).toBeInTheDocument()
  })
})
