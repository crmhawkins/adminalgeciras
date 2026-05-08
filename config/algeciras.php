<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Precio Abono
    |--------------------------------------------------------------------------
    | Precio por defecto de un abono de temporada en Taquilla presencial.
    | Sobreescribible via .env: PRECIO_ABONO=120
    */
    'precio_abono' => env('PRECIO_ABONO', 100),

];
