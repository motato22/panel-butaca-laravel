<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::whereNull('deleted_at')->get();
        return view('notificaciones.index', compact('notificaciones'));
    }

    public function create()
    {
        return view('notificaciones.create');
    }

    public function store(Request $request)
    {
        // Validar
        $data = $request->validate([
            'titulo'   => 'required|string|max:500',
            'mensaje'  => 'required|string|max:500',
            'fecha'    => 'required|date',
            'imagen'   => 'nullable|image|max:2048',
            'segmento' => 'nullable|string',
            'dominio'  => 'nullable|integer',
            'activo'   => 'boolean'
        ]);

        // Si viene un archivo
        if ($request->hasFile('imagen')) {
            // 1) Generas el nombre aleatorio
            $filename = $request->file('imagen')->hashName();
            // $filename = "9232c0a66c2d64e8f5e7816520290ab.png", por ejemplo

            // 2) Guardas el archivo en 'uploads/notificaciones' (disco 'public')
            $request->file('imagen')->storeAs('uploads/notificaciones', $filename, 'public');

            // 3) En la BD solo guardas el nombre
            $data['imagen'] = $filename;
        }

        Notificacion::create($data);

        return redirect()->route('notificaciones.index')
            ->with('success', 'Notificación creada correctamente.');
    }

    public function edit($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        return view('notificaciones.edit', compact('notificacion'));
    }

    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $data = $request->validate([
            'titulo'    => 'required|string|max:500',
            'mensaje'   => 'required|string|max:500',
            'fecha'     => 'required|date',
            'imagen'    => 'nullable|image|max:2048',
            'segmento'  => 'nullable|string',
            'dominio'   => 'nullable|integer',
            'activo'    => 'boolean'
        ]);
        // imagen
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('uploads/notificaciones', 'public');
            $data['imagen'] = $path;
            // Borrar la anterior si quieres
        }

        $notificacion->update($data);

        return redirect()->route('notificaciones.index')
            ->with('success', 'Notificación actualizada.');
    }

    public function destroy($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        // Borrado suave
        $notificacion->delete();

        return redirect()->route('notificaciones.index')
            ->with('success', 'Notificación eliminada.');
    }
    public function toggleActivacion($id)
    {
        $noti = Notificacion::findOrFail($id);
        $noti->activo = !$noti->activo;
        $noti->save();


        return response()->json(['activo' => (bool) $noti->activo], 200);
    }
}
