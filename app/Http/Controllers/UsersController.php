<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $menu = "Users";
        $title = "Users";

        // Parámetros de búsqueda y ordenación
        $search = $request->input('search');
        $orderBy = $request->input('order_by', 'nombre');

        // Estadísticas
        $usuariosActivos = User::where('activo', true)->count();
        $usuariosInactivos = User::where('activo', false)->count();
        $usuariosTotales = User::count();

        // Listas de usuarios
        $usuariosAdmin = User::where('role', 'ROLE_ADMIN')
            ->when($search, function ($query, $search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('nombre', 'like', "%{$search}%")
                        ->orWhere('correo', 'like', "%{$search}%");
                });
            })
            ->orderBy($orderBy, 'asc') // Ordenar según el campo seleccionado
            ->get();

        $usuariosApp = User::where('role', 'ROLE_USER')
            ->when($search, function ($query, $search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('nombre', 'like', "%{$search}%")
                        ->orWhere('correo', 'like', "%{$search}%");
                });
            })
            ->orderBy($orderBy, 'asc') // Ordenar según el campo seleccionado
            ->paginate(10);

        return view('users.index', compact('menu', 'title', 'usuariosActivos', 'usuariosInactivos', 'usuariosTotales', 'usuariosAdmin', 'usuariosApp'));
    }

    public function create()
    {
        $menu = "User";
        $title = "Create User";

        return view('users.create', compact('menu', 'title'));
    }

    public function add(Request $request)
    {
        
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150',
            'username'     => 'required|string|max:190|unique:usuarios,username',
            'correo'       => 'required|email|unique:usuarios,correo',
            'plainPassword'=> 'nullable|string|max:190',
            'role'         => 'required|string|in:ROLE_ADMIN,ROLE_USER,ROLE_RECINTO',
            'segmento'     => 'nullable|string|max:80',
        ], [
            'correo.unique'   => 'Este correo ya está registrado.',
            'username.unique' => 'Este username ya existe, elige otro.',
        ]);
      

        $user = new User([
            'nombre' => $validated['nombre'],
            'username' => $validated['username'],
            'correo' => $validated['correo'],
            'role' => $validated['role'],
            'segmento' => $validated['segmento'] ?? 'General',
        ]);

        $user->password = bcrypt($validated['plainPassword'] ?? Str::random(10));
        
        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($id)
    {
        $menu = "User";
        $title = "Edit User";

        $user = User::findOrFail($id);

        return view('users.edit', compact('menu', 'title', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'correo' => 'required|email|unique:usuarios,correo,' . $id,
            'activo' => 'nullable|boolean',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function toggleActivation($id)
    {
        $user = User::findOrFail($id);
        $user->activo = !$user->activo;
        $user->save();

        return redirect()->route('users.index')->with('success', 'Estado de activación actualizado exitosamente.');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}

    // public function sendNotification(Request $request)
    // {
    //     $validated = $request->validate([
    //         'id' => 'required|exists:users,id',
    //         'mensaje' => 'required|string',
    //         'titulo' => 'nullable|string',
    //         'imagen' => 'nullable|image|max:2048',
    //     ]);

    //     $user = User::findOrFail($validated['id']);

    //     // Lógica para enviar la notificación
    //     Notification::send($user, new CustomNotification($validated));

    //     return redirect()->route('users.index')->with('success', 'Notificación enviada exitosamente.');
    // }

    // public function sendBulkNotification(Request $request)
    // {
    //     $validated = $request->validate([
    //         'tipo' => 'required|in:todos,seleccionados',
    //         'usuarios_seleccionados' => 'nullable|array',
    //         'usuarios_seleccionados.*' => 'exists:users,id',
    //         'mensaje' => 'required|string',
    //         'titulo' => 'nullable|string',
    //         'imagen' => 'nullable|image|max:2048',
    //     ]);

    //     $users = $validated['tipo'] === 'todos'
    //         ? User::all()
    //         : User::whereIn('id', $validated['usuarios_seleccionados'])->get();

    //     // Lógica para enviar notificaciones en lote
    //     Notification::send($users, new CustomNotification($validated));

    //     return redirect()->route('users.index')->with('success', 'Notificaciones enviadas exitosamente.');
    // }
