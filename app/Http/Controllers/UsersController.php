<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $menu = "Users";
        $title = "Users";

        // Estadísticas
        $usuariosActivos = User::where('activo', true)->count();
        $usuariosInactivos = User::where('activo', false)->count();
        $usuariosTotales = User::count();

        // Listas de usuarios
        $usuariosAdmin = User::select('id','nombre', 'correo', 'role', 'segmento', 'created_at')
            ->where('role', 'ROLE_ADMIN')
            ->orderBy('nombre', 'asc') 
            ->get();
        $usuariosApp = User::select('id','nombre', 'correo', 'segmento', 'created_at')
            ->where('role', 'ROLE_USER')
            ->orderBy('nombre', 'asc') 
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
    // Validación de datos
    $validated = $request->validate([
        'nombre' => 'required|string|max:120',
        'username' => 'required|string|max:60|unique:usuarios,username',
        'correo' => 'required|email|unique:usuarios,correo',
        'plainPassword' => 'nullable|string|max:60',
        'role' => 'required|string',
        'segmento' => 'nullable|string|max:80',
    ]);

    // Crear usuario con los datos validados
    $user = new User([
        'nombre' => $validated['nombre'],
        'username' => $validated['username'],
        'correo' => $validated['correo'],
        'role' => $validated['role'], // Cambiado para coincidir con los nombres validados
        'segmento' => $validated['segmento'],
    ]);

    // Asignar contraseña segura
    $user->password = bcrypt($validated['plainPassword'] ?? Str::random(10));

    // Guardar el usuario en la base de datos
    $user->save();

    // Redirigir con mensaje de éxito
    return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
}



    public function edit ($id)
    {
        $menu = "User";
        $title = "Edit User";

        $user = User::findOrFail($id);

        return view('users.edit',compact('menu','title','user'));
    }

    public function update (Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validate = $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:users,correo,' . $id,
            'activo' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('sucess','User update Succesfully.');
    }

    public function toggleActivation($id)
    {
        $user =User::findOrFail($id);
        $user->activo = !$user->activo;
        $user->save();

        return redirect()->route('users.index')->with('success','User Activation Status Update.');
    }

    public function delete ($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success','User Delete Succesfully.');
    }

    public function sendNotification($type, $title, $content, $date, $time, $data, $users_id)
    {
        $validate = $request->validate ([
            'id' => 'required|exists:users.id',
            'mensaje' => 'required|string',
            'titulo' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $user = User::findOrFail($validated['id']);

         // Aquí puedes implementar la lógica para enviar la notificación
        // Ejemplo: Notification::send($user, new CustomNotification($validated));

        return redirect()->route('users.index')->with('success','Notification sent Successfully.');
    }

    public function sendBulkNotification(Request $request)
    {
        $validate = $request->validate ([
            'tipo' => 'required|in:todos,seleccionados',
            'usuarios_seleccionados' => 'nullable|array',
            'usuarios_seleccionados.*' => 'exists:users,id',
            'mensaje' => 'requiered|string',
            'titulo' => 'nulable|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $users = $validated['tipo'] === 'todos'
        ? User::all()
        : User::whereIn('id', $validated['usuarios_seleccionados'])->get();

        // Aquí puedes implementar la lógica para enviar la notificación
        // Ejemplo: Notification::send($users, new CustomNotification($validated));

        return redirect()->route('users.index')->with('success','Bulks Notification sent Successfully.');
    }
}
