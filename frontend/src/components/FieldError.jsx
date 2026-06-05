/**
 * Muestra el primer mensaje de error de validación de un campo, si existe.
 * Recibe el arreglo de mensajes que devuelve la API en `errors[campo]`.
 */
export default function FieldError({ messages }) {
  if (!messages || messages.length === 0) {
    return null
  }

  return <p className="mt-1 text-xs text-red-600">{messages[0]}</p>
}
