<?php

namespace App\Http\Controllers;

use App\Models\TipoZona;
use Illuminate\Http\Request;

class TipoZonaController extends Controller
{
    /**
     * Muestra la lista de tipos de zona.
     */
    public function index()
    {
        $zonas = TipoZona::all();
        $menu='tipoZonas';
        return view('tipo_zona.index', compact('menu','zonas'));
    }

    /**
     * Muestra el formulario para crear un nuevo tipo de zona.
     */
    public function create()
    {
        $menu='tipoZonas';
        return view('tipo_zona.create',compact('menu'));
    }

    /**
     * Procesa el formulario y guarda el nuevo tipo de zona.
     */
    public function store(Request $request)
    {
        // Validación (ajusta las reglas según tus necesidades)
        $request->validate([
            'tipo' => 'required|string|max:255',
        ]);

        TipoZona::create($request->all());

        return redirect()->route('tipo_zona.index');
    }

    /**
     * Muestra el formulario para editar un tipo de zona existente.
     */
    public function edit(TipoZona $tipoZona)
    {
        $menu='tipoZonas';
        return view('tipo_zona.edit', compact('menu','tipoZona'));
    }

    /**
     * Actualiza el tipo de zona con la información del formulario.
     */
    public function update(Request $request, TipoZona $tipoZona)
    {
        // Validación
        $request->validate([
            'tipo' => 'required|string|max:255',
        ]);

        $tipoZona->update($request->all());

        return redirect()->route('tipo_zona.index');
    }

    /**
     * Elimina el tipo de zona.
     * Antes de eliminar, se recorren las zonas asociadas y se pone su atributo "tipo" a null.
     */
    public function destroy(TipoZona $tipoZona)
    {
        foreach ($tipoZona->zonas as $zona) {
            $zona->tipo = null;
            $zona->save();
        }
        $tipoZona->delete();
        return redirect()->route('tipo_zona.index');
    }
}
