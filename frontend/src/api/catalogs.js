import client from './client'

/** Catálogo de ciudades. */
export const getCities = () => client.get('/cities').then((r) => r.data.data)

/** Catálogo de tipos de habitación. */
export const getRoomTypes = () => client.get('/room-types').then((r) => r.data.data)

/** Acomodaciones válidas para un tipo de habitación dado. */
export const getAccommodationsForRoomType = (roomTypeId) =>
  client.get(`/room-types/${roomTypeId}/accommodations`).then((r) => r.data.data)
