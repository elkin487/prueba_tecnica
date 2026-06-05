import client from './client'

/** Lista de hoteles (primera página). */
export const listHotels = () => client.get('/hotels').then((r) => r.data.data)

/** Detalle de un hotel con sus habitaciones. */
export const getHotel = (id) => client.get(`/hotels/${id}`).then((r) => r.data.data)

export const createHotel = (payload) =>
  client.post('/hotels', payload).then((r) => r.data.data)

export const updateHotel = (id, payload) =>
  client.put(`/hotels/${id}`, payload).then((r) => r.data.data)

export const deleteHotel = (id) => client.delete(`/hotels/${id}`)

/* ----- Configuración de habitaciones (anidada bajo un hotel) ----- */

export const addRoom = (hotelId, payload) =>
  client.post(`/hotels/${hotelId}/rooms`, payload).then((r) => r.data.data)

export const updateRoom = (hotelId, roomId, payload) =>
  client.put(`/hotels/${hotelId}/rooms/${roomId}`, payload).then((r) => r.data.data)

export const deleteRoom = (hotelId, roomId) =>
  client.delete(`/hotels/${hotelId}/rooms/${roomId}`)
