<?php

namespace App\Http\Controllers;

use App\Models\SitioInteres;
use Illuminate\Http\Request;

class SitioInteresController extends Controller
{
    /**
     * Muestra la lista de sitios de interés.
     */
    public function index()
    {
      
        $sitios = SitioInteres::all();

        
        $menu = 'SitioInteres';

        
        return view('sitios.index', compact('sitios', 'menu'));
    }

    /**
     * Muestra el formulario para crear un nuevo sitio de interés.
     */
    public function create()
    {
        $menu = 'SitioInteres';

        return view('sitios.create', compact('menu'));
    }

    /**
     * Procesa la creación del nuevo sitio de interés.
     */
    public function store(Request $request)
    {
        // Validamos los datos
        $request->validate([
            'nombre'        => 'required|string|max:190',
            'url'           => 'required|url|max:190',
            'clasificacion' => 'nullable|string|max:190',
        ]);

        
        SitioInteres::create($request->only('nombre', 'url', 'clasificacion'));

        
        return redirect()
            ->route('sitios.index')
            ->with('success', 'Sitio de interés creado correctamente.');
    }

    /**
     * Elimina el sitio de interés.
     */
    public function destroy(SitioInteres $sitio)
    {
        $sitio->delete();

        return redirect()
            ->route('sitios.index')
            ->with('success', 'Sitio de interés eliminado correctamente.');
    }
}
