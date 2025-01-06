<?php

use App\Models\Fecha;

if (! function_exists('formatearPrecioFecha') ) {
    function formatearPrecioFecha(Fecha $fecha) {
        $precio = 'N/A';
        $iso = $fecha->moneda ? $fecha->moneda->iso : null;
        $gratis = $fecha->gratis;
        $precioBajo = $fecha->precio_bajo;
        $precioAlto = $fecha->precio_alto;
        // Gratis
        if ( $gratis ) {
            $precio = 'Gratis';
        } 
        // Rango de precios
        else {
            if( $precioBajo ) {
                $precio = '$'.$precioBajo . ( $iso ?  $iso : '');
            }

            if( $precioAlto ) {
                $precio = $precio .' - '. '$'.$precioAlto . ( $iso ?  $iso : '');
            }
        }

        return $precio;
    }
}