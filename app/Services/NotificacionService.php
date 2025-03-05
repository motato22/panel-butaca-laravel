<?php

namespace App\Services;

use App\Models\Notificacion; // Ajusta con tu Model
use Carbon\Carbon;

class NotificacionService
{
    public function obtenerTodas()
    {
        return Notificacion::whereNull('deleted_at') // si usas borrado lógico con "deleted_at"
                           ->orderBy('fecha', 'desc')
                           ->get();
    }

    public function toggleActivacion($id)
    {
        $noti = Notificacion::findOrFail($id);
        $noti->activo = !$noti->activo;
        $noti->save();

        return (bool) $noti->activo;
    }

    public function crear(array $data)
    {
        // Manejo de fecha
        $data['fecha'] = Carbon::now();

        return Notificacion::create([
            'mensaje'   => $data['mensaje'],
            'titulo'    => $data['titulo'] ?? null,
            'fecha'     => $data['fecha'],
            'imagen'    => $data['imagen'] ?? null,
            'segmento'  => $data['segmento'] ?? null,
            'activo'    => 1,
        ]);
    }

    // ... etc, según tus necesidades
}
