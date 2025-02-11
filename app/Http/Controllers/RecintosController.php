<?php

namespace App\Http\Controllers;

use App\Models\Recinto;
use App\Models\Zona;
use Illuminate\Http\Request;

class RecintosController extends Controller
{
    public function index()
    {
        $menu = "Recintos";
        $title = "Recintos";
        $recintos = Recinto::with('zona')->paginate(10);

        return view('recintos.index', compact('menu', 'title', 'recintos'));
    }

    public function create()
    {
        $menu = "Recintos";
        $title = "Crear Recinto";

        $zonas = Zona::all();

        return view('recintos.create', compact('menu', 'title', 'zonas'));
    }

    public function store(Request $request)
    {
        // Asegurar que 'promocion' tenga un valor (0 o 1)
        $request->merge([
            'promocion' => $request->has('promocion') ? 1 : 0,
        ]);

        // Validar los datos
        $validated = $request->validate([
            'zona_id' => 'nullable|exists:zona_recinto,id',
            'nombre' => 'required|string|max:190',
            'foto' => 'nullable|image|max:2048',
            'contacto' => 'nullable|string|max:190',
            'web' => 'nullable|sometimes|url',
            'horario_inicio' => 'nullable|string|max:190',
            'horario_fin' => 'nullable|string|max:190',
            'capacidad' => 'nullable|string|max:190',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'twitter' => 'nullable|url',
            'youtube' => 'nullable|url',
            'amenidades' => 'nullable|string|max:190',
            'descripcion' => 'nullable|string',
            'video' => 'nullable|url',
            'promocion' => 'nullable|boolean',
            'lat' => 'nullable|string|max:190',
            'lng' => 'nullable|string|max:190',
            'direccion' => 'nullable|string|max:190',
            'telefono' => 'nullable|string|max:190',
        ]);

        // 🚀 Depuración: Verificar los datos antes de guardar
        // dd($validated);

        // Guardar la imagen si fue subida
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('recintos', 'public');
        }

        // Insertar en la base de datos
        Recinto::create($validated);

        return redirect()->route('recintos.index')->with('success', 'Recinto creado exitosamente.');
    }

    public function edit($id)
    {
        $menu = "Recintos";
        $title = "Editar Recinto";
        $recinto = Recinto::findOrFail($id);

        return view('recintos.create', compact('menu', 'title', 'recinto'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'zona_id' => 'nullable|exists:zona_recinto,id',
            'nombre' => 'required|string|max:190',
            'foto' => 'nullable|image|max:2048',
            'contacto' => 'nullable|string|max:190',
            'web' => 'nullable|url',
            'horario_inicio' => 'nullable|string|max:190',
            'horario_fin' => 'nullable|string|max:190',
            'capacidad' => 'nullable|string|max:190',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'twitter' => 'nullable|url',
            'youtube' => 'nullable|url',
            'amenidades' => 'nullable|string|max:190',
            'descripcion' => 'nullable|string',
            'video' => 'nullable|url',
            'promocion' => 'nullable|boolean',
            'lat' => 'nullable|string|max:190',
            'lng' => 'nullable|string|max:190',
            'direccion' => 'nullable|string|max:190',
            'telefono' => 'nullable|string|max:190',
        ]);

        $recinto = Recinto::findOrFail($id);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('recintos', 'public');
        }

        $recinto->update($validated);

        return redirect()->route('recintos.index')->with('success', 'Recinto actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $recinto = Recinto::findOrFail($id);
        $recinto->delete();

        return redirect()->route('recintos.index')->with('success', 'Recinto eliminado exitosamente.');
    }
}
