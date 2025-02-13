<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recinto;
use App\Models\GaleriaRecinto;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecintosController extends Controller
{
    /**
     * Muestra la lista de recintos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $recintos = [];

        // // Depuración: Verificar el rol del usuario actual
        // $user = Auth::user();
        // dd([
        //     'user_role' => $user->role, // Esto mostrará el valor del campo `role` del usuario
        //     'is_admin' => $user->hasRole('ROLE_ADMIN'), // Esto mostrará si el usuario tiene el rol 'ROLE_ADMIN'
        //     'is_recinto' => $user->hasRole('ROLE_RECINTO'), // Esto mostrará si el usuario tiene el rol 'ROLE_RECINTO'
        // ]);

        if (Auth::user()->hasRole('ROLE_ADMIN')) {
            $recintos = Recinto::paginate(10); // Pagina los resultados (10 por página)
        } elseif (Auth::user()->hasRole('ROLE_RECINTO')) {
            $recintos = Auth::user()->recintos()->paginate(10); // Pagina los recintos del usuario
        }


        $menu = 'Recintos';
        return view('recintos.index', compact('menu', 'recintos'));
    }

    /**
     * Muestra el formulario para crear un nuevo recinto.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', Recinto::class); // Verifica permisos
        $zonas = Zona::all(); // Obtener todas las zonas para el select

        $menu = 'Recintos';
        return view('recintos.create', compact('menu', 'zonas'));
    }

    /**
     * Guarda un nuevo recinto en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Recinto::class); // Verifica permisos

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
            'promocion' => 'required|boolean',
            'lat' => 'nullable|string|max:190',
            'lng' => 'nullable|string|max:190',
            'direccion' => 'nullable|string|max:190',
            'telefono' => 'nullable|string|max:190',
            'galeria.*' => 'nullable|image|max:2048',
        ]);

        $recinto = new Recinto();
        $recinto->fill($validated);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('recintos', $fileName, 'public');
            $recinto->foto = $fileName;
        }

        $recinto->save();

        // Guardar galería de imágenes
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('recintos/galeria', $fileName, 'public');

                GaleriaRecinto::create([
                    'image' => $fileName,
                    'recinto_id' => $recinto->id,
                ]);
            }
        }

        return redirect()->route('recintos.index')->with('success', 'Recinto creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un recinto.
     *
     * @param  \App\Models\Recinto  $recinto
     * @return \Illuminate\View\View
     */
    public function edit(Recinto $recinto)
    {
        $this->authorize('update', $recinto); // Verifica permisos
        $zonas = Zona::all(); // Obtener todas las zonas para el select

        $menu = 'Recintos';
        return view('recintos.create', compact('menu', 'recinto', 'zonas'));
    }

    /**
     * Actualiza un recinto en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Recinto  $recinto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Recinto $recinto)
    {

        $this->authorize('update', $recinto); // Verifica permisos

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
            'promocion' => 'required|boolean',
            'lat' => 'nullable|string|max:190',
            'lng' => 'nullable|string|max:190',
            'direccion' => 'nullable|string|max:190',
            'telefono' => 'nullable|string|max:190',
            'galeria.*' => 'nullable|image|max:2048', // Validación para la galería
        ]);

        $recinto->fill($validated);

        if ($request->hasFile('foto')) {
            // Eliminar la foto anterior si existe
            if ($recinto->foto) {
                Storage::disk('public')->delete('recintos/' . $recinto->foto);
            }

            $file = $request->file('foto');
            $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('recintos', $fileName, 'public');
            $recinto->foto = $fileName;
        }

        $recinto->save();

        // Guardar galería de imágenes
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('recintos/galeria', $fileName, 'public');

                GaleriaRecinto::create([
                    'image' => $fileName,
                    'recinto_id' => $recinto->id,
                ]);
            }
        }

        return redirect()->route('recintos.index')->with('success', 'Recinto actualizado exitosamente.');
    }

    /**
     * Elimina un recinto de la base de datos.
     *
     * @param  \App\Models\Recinto  $recinto
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Recinto $recinto)
    {
        $this->authorize('delete', $recinto);

        // Eliminar imagen principal
        if ($recinto->foto) {
            Storage::disk('public')->delete('recintos/' . $recinto->foto);
        }

        // Eliminar imágenes de galería
        foreach ($recinto->galeria as $imagen) {
            Storage::disk('public')->delete('recintos/galeria/' . $imagen->image);
            $imagen->delete();
        }

        // Eliminar relación con usuarios (si aplica)
        $recinto->users()->detach();

        // Eliminar recinto
        $recinto->delete();

        return redirect()->route('recintos.index')->with('success', 'Recinto eliminado exitosamente.');
    }


    /**
     * Muestra el formulario para agregar usuarios a un recinto.
     */

    public function addUsers(Request $request, Recinto $recinto)
    {
        // Verifica permisos (equivalente a `denyAccessUnlessGranted` en Symfony)
        if (!Auth::user()->hasRole('ROLE_ADMIN')) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        // Obtener usuarios con el rol 'ROLE_RECINTO' que no están en este recinto
        $users = User::where('role', 'ROLE_RECINTO')
            ->whereNotIn('id', $recinto->users()->pluck('id'))
            ->get();

        // Si se recibe un usuario en el formulario, agregarlo al recinto
        if ($request->has('user')) {
            $user = User::find($request->input('user'));

            if ($user) {
                $recinto->users()->attach($user->id); // Agregar a la relación pivote
            }

            return redirect()->route('recintos.addUsers', ['recinto' => $recinto->id]);
        }

        $menu = 'Recintos';
        return view('recintos.addUsers', compact('menu', 'recinto', 'users'));
    }

    /**
     * Procesa la solicitud de agregar un usuario al recinto.
     */
    public function storeUser(Request $request, Recinto $recinto)
    {
        $this->authorize('update', $recinto); // Verifica permisos

        $request->validate([
            'user' => 'required|exists:usuarios,id',
        ]);

        // Agregar usuario al recinto (tabla pivote usuario_recinto)
        $recinto->users()->attach($request->input('user'));

        return redirect()->route('recintos.addUsers', $recinto->id)->with('success', 'Usuario agregado exitosamente.');
    }

    /**
     * Elimina un usuario asociado a un recinto.
     *
     * @param  \App\Models\Recinto  $recinto
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeUser(Recinto $recinto, User $usuario)
    {
        $this->authorize('update', $recinto); // Verifica permisos

        // Eliminar la relación en la tabla pivote
        $recinto->users()->detach($usuario->id);

        return redirect()->route('recintos.addUsers', $recinto->id)
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    /**
     * Muestra el formulario para agregar imágenes a la galería de un recinto.
     *
     * @param  \App\Models\Recinto  $recinto
     * @return \Illuminate\View\View
     */
    public function addImages(Request $request, Recinto $recinto)
    {
        // Verificar permisos: solo el usuario asociado o un admin pueden acceder
        if (!Auth::user()->hasRole('ROLE_ADMIN') && !$recinto->users->contains(Auth::user())) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        // Obtener galería del recinto
        $miGaleria = GaleriaRecinto::where('recinto', $recinto->id)->get();

        // Subir imágenes si se enviaron archivos
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('recintos/galeria', $fileName, 'public');

                GaleriaRecinto::create([
                    'image' => $fileName,
                    'recinto' => $recinto->id,
                ]);
            }

            return redirect()->route('recintos.addImages', $recinto->id)->with('success', 'Imágenes agregadas correctamente.');
        }

        $menu = 'Recintos';
        return view('recintos.addImages', compact('menu', 'recinto', 'miGaleria'));
    }


    public function storeImages(Request $request, Recinto $recinto)
    {
        $request->validate([
            'galeria' => 'required|array|min:1',
            'galeria.*' => 'image|max:2048',
        ], [
            'galeria.required' => 'Debes subir al menos una imagen.',
            'galeria.min' => 'Debes subir al menos una imagen.',
            'galeria.*.image' => 'Cada archivo debe ser una imagen válida.',
            'galeria.*.max' => 'Cada imagen debe pesar menos de 2MB.',
        ]);

        foreach ($request->file('galeria') as $image) {
            $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('recintos/galeria', $fileName, 'public');

            GaleriaRecinto::insert([
                'image' => $fileName,
                'recinto' => $recinto->id
            ]);
        }

        return redirect()->route('recintos.addImages', $recinto->id)->with('success', 'Imágenes agregadas correctamente.');
    }


    /**
     * Elimina una imagen de la galería de un recinto.
     *
     * @param  \App\Models\GaleriaRecinto  $imagen
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage(Recinto $recinto, GaleriaRecinto $imagen)
    {
        // Verificar permisos
        if (!Auth::user()->hasRole('ROLE_ADMIN') && !$recinto->users->contains(Auth::user())) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        // Eliminar archivo físico
        Storage::disk('public')->delete('recintos/galeria/' . $imagen->image);

        // Eliminar la imagen de la base de datos
        $imagen->delete();

        return redirect()->route('recintos.addImages', $recinto->id)->with('success', 'Imagen eliminada correctamente.');
    }
}
