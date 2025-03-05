<?php

namespace App\Services;

use App\Models\Cupon;
use Carbon\Carbon;

class CuponService
{
    /**
     * Obtiene todos los cupones (no eliminados).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerTodos()
    {
        return Cupon::where('eliminado', 0)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
    }

    /**
     * Cambia el estado activo/inactivo de un cupón.
     * 
     * @param int $id
     * @return bool Devuelve el estado actual (true=activo, false=inactivo)
     */
    public function toggleActivacion($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->activo = !$cupon->activo; // Cambia de 1->0 o 0->1
        $cupon->save();

        return (bool) $cupon->activo; // Retorna el nuevo estado
    }

    /**
     * Marca un cupón como eliminado (borrado lógico).
     *
     * @param int $id
     * @return void
     */
    public function eliminar($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->eliminado = 1;
        $cupon->save();
    }

    /**
     * Crea un nuevo cupón.
     * @param array $data
     * @return Cupon
     */
    public function crear(array $data)
    {
        // Aquí puedes manejar la fecha si la necesitas, p.e:
        // $data['fecha_inicio'] = Carbon::parse($data['fecha_inicio'])->format('Y-m-d');

        return Cupon::create([
            'dominio'       => $data['dominio'],
            'descripcion'   => $data['descripcion'],
            'activo'        => $data['activo'] ?? 0,
            'fecha_inicio'  => $data['fecha_inicio'] ?? null,
            'eliminado'     => 0,
        ]);
    }

    /**
     * Actualiza los datos de un cupón existente.
     *
     * @param int $id
     * @param array $data
     * @return Cupon
     */
    public function actualizar($id, array $data)
    {
        $cupon = Cupon::findOrFail($id);

        $cupon->dominio       = $data['dominio'];
        $cupon->descripcion   = $data['descripcion'];
        $cupon->fecha_inicio  = $data['fecha_inicio'] ?? $cupon->fecha_inicio;
        // $cupon->activo     = $data['activo'] ?? $cupon->activo; (depende de tu formulario)

        $cupon->save();

        return $cupon;
    }

    public function obtenerPorId($id)
    {
        
        return Cupon::where('eliminado', 0)->findOrFail($id);
    }
}
