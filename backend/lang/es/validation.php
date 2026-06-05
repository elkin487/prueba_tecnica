<?php

/*
|--------------------------------------------------------------------------
| Líneas de idioma de validación (español)
|--------------------------------------------------------------------------
|
| Traducción al español de los mensajes de validación de Laravel. Solo se
| incluyen las reglas usadas por la aplicación; el resto puede ampliarse
| según se necesite.
|
*/

return [
    'accepted' => 'El campo :attribute debe ser aceptado.',
    'array' => 'El campo :attribute debe ser un conjunto.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'exists' => 'El valor seleccionado para :attribute no es válido.',
    'filled' => 'El campo :attribute es obligatorio.',
    'in' => 'El valor seleccionado para :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El campo :attribute no debe ser mayor que :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe ser mayor que :max caracteres.',
    ],
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El campo :attribute debe ser al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => 'El campo :attribute debe ser un número.',
    'present' => 'El campo :attribute debe estar presente.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'unique' => 'El valor de :attribute ya está en uso.',

    /*
    |--------------------------------------------------------------------------
    | Nombres de atributos personalizados
    |--------------------------------------------------------------------------
    |
    | Los nombres específicos por petición se definen en el método
    | attributes() de cada Form Request.
    |
    */

    'attributes' => [],
];
