<?php

namespace App\Http\Controllers;

use App\Models\Info;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    /**
     * Muestra el listado de informaciones.
     */
    public function index()
    {
        $infos = Info::all();
        $menu = 'informacion';
        return view('info.index', compact('infos', 'menu'));
    }

    /**
     * Muestra una información en formato “show”.
     */
    public function show(Info $info)
    {
        return view('info.show', compact('info'));
    }

    /**
     * Muestra el formulario para editar una información.
     */
    public function edit(Info $info)
    {
        $menu = 'Informacion';
        return view('info.edit', compact('menu', 'info'));
    }

    /**
     * Actualiza el contenido de la información.
     */
    public function update(Request $request, Info $info)
    {
        $request->validate([
            'texto' => 'required|string',
        ]);

        
        $info->update($request->only('texto'));

        return redirect()->route('info.index')
            ->with('success', 'Información actualizada correctamente.');
    }
}
