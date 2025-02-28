<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\TipoZona;
use Illuminate\Http\Request;

class ZonaRecintoController extends Controller
{
    /**
     * Muestra el listado de Zonas.
     * GET: /panel/zonas
     */
    public function index()
    {

        $zonas = Zona::with('tipoZona')->get();
        // $zonas = Zona::all();
        // Retornamos la vista "zonas.index" con la variable $zonas
        // dd($zonas->toArray());
        $menu = 'Zonas';
        return view('zonas.index', compact('menu', 'zonas'));
    }

    /**
     * Muestra el formulario para crear una nueva Zona.
     * GET: /panel/zonas/create
     */
    public function create()
    {
        $tipos = TipoZona::all();
        $menu = 'Crear Zona';
        return view('zonas.create', compact('menu', 'tipos'));
    }

    /**
     * Guarda una nueva Zona en la base de datos.
     * POST: /panel/zonas
     */
    public function store(Request $request)
    {
        // Validamos los datos que llegan del formulario
        $request->validate([
            'zona' => 'required|string|max:190',
            'tipo' => 'nullable|exists:tipo_zona,id',
            // Ajusta el nombre de la tabla/columna si tu "tipo" es FK
        ]);

        // Creamos la instancia
        $zona = new Zona();
        $zona->zona = $request->input('zona');
        $zona->tipo = $request->input('tipo');
        // O si es una relación belongsTo, ajusta la FK según tu BD
        // $zona->tipo_id = $request->input('tipo');

        $zona->save();

        return redirect()->route('zonas.index')
            ->with('success', 'Zona creada correctamente');
    }

    /**
     * Muestra el formulario para editar una Zona.
     * GET: /panel/zonas/{id}/edit
     */
    public function edit(Zona $zona)
    {
      
        $tipos = TipoZona::all();
        $menu = 'Editar Zona';
       
        return view('zonas.edit', compact('menu','zona', 'tipos'));
    }

    /**
     * Actualiza una Zona en la base de datos.
     * PUT/PATCH: /panel/zonas/{id}
     */
    public function update(Request $request, Zona $zona)
    {
        // Validamos
        $request->validate([
            'zona' => 'required|string|max:190',
            'tipo' => 'nullable|exists:tipo_zona,id',
        ]);

        $zona->zona = $request->input('zona');
        $zona->tipo = $request->input('tipo');
        // O si la columna en DB se llama "tipo_id", usa $zona->tipo_id = ...

        $zona->save();

        return redirect()->route('zonas.index')
            ->with('success', 'Zona actualizada correctamente');
    }

    /**
     * Elimina una Zona de la base de datos.
     * DELETE: /panel/zonas/{id}
     */
    public function destroy(Zona $zona)
    {
        // Si quieres desvincular recintos primero (como en tu Symfony):
        // foreach ($zona->recintos as $recinto) {
        //     $recinto->zona_id = null;
        //     $recinto->save();
        // }

        $zona->delete();

        return redirect()->route('zonas.index')
            ->with('success', 'Zona eliminada correctamente');
    }
}
