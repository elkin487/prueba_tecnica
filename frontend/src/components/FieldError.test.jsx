import { describe, expect, it } from 'vitest'
import { render, screen } from '@testing-library/react'
import FieldError from './FieldError'

describe('FieldError', () => {
  it('no renderiza nada cuando no hay mensajes', () => {
    const { container } = render(<FieldError messages={[]} />)
    expect(container).toBeEmptyDOMElement()
  })

  it('muestra el primer mensaje de error', () => {
    render(<FieldError messages={['El nombre es obligatorio.', 'mensaje secundario']} />)
    expect(screen.getByText('El nombre es obligatorio.')).toBeInTheDocument()
  })
})
