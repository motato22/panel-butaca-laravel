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

        // Validaciones (ajusta a tus reglas)
        $validated = $request->validate([
            'zona_id' => 'nullable|exists:zona_recinto,id',
            'nombre' => 'required|string|max:190',
            'foto' => 'nullable|image|max:2048',
            // ...
            'galeria.*' => 'nullable|image|max:2048',
        ]);

        $recinto = new Recinto();
        $recinto->fill($validated);

        // Guardar foto principal en "uploads/recintos"
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/uploads/recintos', $fileName);
            $recinto->foto = $fileName;
        }

        $recinto->save();

        // Guardar galería de imágenes en la **misma** carpeta
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/uploads/recintos', $fileName);

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
        $this->authorize('update', $recinto);

        // Validaciones
        $validated = $request->validate([
            'zona_id' => 'nullable|exists:zona_recinto,id',
            'nombre' => 'required|string|max:190',
            'foto' => 'nullable|image|max:2048',
            // ...
            'galeria.*' => 'nullable|image|max:2048',
        ]);

        $recinto->fill($validated);

        // Actualizar foto principal
        if ($request->hasFile('foto')) {
            if ($recinto->foto) {
                Storage::disk('public')->delete('uploads/recintos/' . $recinto->foto);
            }
            $file = $request->file('foto');
            $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/uploads/recintos', $fileName);
            $recinto->foto = $fileName;
        }

        $recinto->save();

       
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/uploads/recintos', $fileName);

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

      
        if ($recinto->foto) {
            Storage::disk('public')->delete('uploads/recintos/' . $recinto->foto);
        }

        
        foreach ($recinto->galeria as $imagen) {
            Storage::disk('public')->delete('uploads/recintos/' . $imagen->image);
            $imagen->delete();
        }

        $recinto->users()->detach();

        $recinto->delete();

        return redirect()->route('recintos.index')->with('success', 'Recinto eliminado exitosamente.');
    }

    /**
     * Muestra el formulario para agregar usuarios a un recinto.
     */

    public function addUsers(Request $request, Recinto $recinto)
    {
        
        if (!Auth::user()->hasRole('ROLE_ADMIN')) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        
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
        $this->authorize('update', $recinto); 

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
        // Verificación de permisos
        if (!Auth::user()->hasRole('ROLE_ADMIN') && !$recinto->users->contains(Auth::user())) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        $miGaleria = $recinto->galeria; 

        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/uploads/recintos', $fileName);

                GaleriaRecinto::create([
                    'image' => $fileName,
                    'recinto_id' => $recinto->id,
                ]);
            }

            return redirect()->route('recintos.addImages', $recinto->id)
                ->with('success', 'Imágenes agregadas correctamente.');
        }

        $menu = 'Recintos';
        return view('recintos.addImages', compact('menu', 'recinto', 'miGaleria'));
    }

    /**
     * Agrega imágenes POST para la galería (si tienes un formulario separado).
     */
    public function storeImages(Request $request, Recinto $recinto)
    {
        $request->validate([
            'galeria' => 'required|array|min:1',
            'galeria.*' => 'image|max:2048',
        ]);

        foreach ($request->file('galeria') as $image) {
            $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/uploads/recintos', $fileName);

            GaleriaRecinto::create([
                'image' => $fileName,
                'recinto_id' => $recinto->id,
            ]);
        }

        return redirect()->route('recintos.addImages', $recinto->id)
            ->with('success', 'Imágenes agregadas correctamente.');
    }

    /**
     * Elimina una imagen de la galería del recinto.
     */
    public function deleteImage(Recinto $recinto, GaleriaRecinto $imagen)
    {
        if (!Auth::user()->hasRole('ROLE_ADMIN') && !$recinto->users->contains(Auth::user())) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        Storage::disk('public')->delete('uploads/recintos/' . $imagen->image);

        $imagen->delete();

        return redirect()->route('recintos.addImages', $recinto->id)
            ->with('success', 'Imagen eliminada correctamente.');
    }
}
