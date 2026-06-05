import axios from 'axios'

/**
 * Cliente HTTP central de la aplicación. La URL base de la API se toma de la
 * variable de entorno VITE_API_URL (ver .env.example).
 */
const client = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://prueba_tecnica.test/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

export default client
