<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de CORS
    |--------------------------------------------------------------------------
    |
    | Permite que el frontend (SPA React, desacoplado) consuma la API desde
    | otro origen. En desarrollo se admite cualquier origen; en producción
    | conviene restringir 'allowed_origins' al dominio del frontend.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
