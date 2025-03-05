<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CuponService;
use App\Models\Notificacion;

class CuponController extends Controller
{
    protected $cuponService;

    public function __construct(CuponService $cuponService)
    {
        $this->cuponService = $cuponService;
    }

    /**
     * Muestra la lista de cupones
     */
    public function index()
    {
        // Obtenemos los cupones a través del service
        $cupones = $this->cuponService->obtenerTodos();

        // También obtenemos notificaciones de forma directa (o vía un NotificacionService si quieres)
        $notificaciones = Notificacion::whereNull('deleted_at')->get();

        // Calcular contadores (activos, total)
        $activos = $cupones->where('activo', 1)->count();
        $total   = $cupones->count();

        // Para tu menú o layout
        $menu = 'Cupon';

        // Retornamos la vista con todos estos datos
        return view('cupon.index', [
            'cupones'        => $cupones,
            'notificaciones' => $notificaciones,
            'meta'           => [
                'activos' => $activos,
                'total'   => $total
            ],
            'menu'           => $menu
        ]);
    }

    /**
     * Formulario para crear nuevo cupón
     */
    public function create()
    {
        $menu = 'Cupon';
        return view('cupon.create', compact('menu'));
    }

    /**
     * Guarda un nuevo cupón
     */
    public function store(Request $request)
    {
        // Validamos
        $data = $request->validate([
            'dominio'       => 'required|integer',
            'descripcion'   => 'required|string',
            'fecha_inicio'  => 'nullable|date',
            'notificacoin'  => 'nullable|integer',
            'image'         => 'nullable|image|max:2048',
        ]);

        // Manejo de la imagen (si la tabla tiene columna p.e. "image_path")
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/promociones', 'public');
            $data['image_path'] = $path;
        }

        // Creación vía service (marca activo=0 y eliminado=0 si así quieres)
        $data['activo']    = 0;
        $data['eliminado'] = 0;
        $this->cuponService->crear($data);

        return redirect()->route('cupon.index')
            ->with('success', 'Cupón creado correctamente.')
            ->with('menu', 'Cupon');
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $menu = 'Cupon';
        // Obtenemos el cupón por ID a través del service
        $cupon = $this->cuponService->obtenerPorId($id);

        return view('cupon.edit', compact('cupon', 'menu'));
    }

    /**
     * Actualiza un cupón existente
     */
    public function update(Request $request, $id)
    {
        // Validamos
        $data = $request->validate([
            'dominio'       => 'required|integer',
            'descripcion'   => 'required|string',
            'fecha_inicio'  => 'nullable|date',
            'notificacoin'  => 'nullable|integer',
            'image'         => 'nullable|image|max:2048',
        ]);

        // Manejo de la imagen (opcional)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/promociones', 'public');
            $data['image_path'] = $path;

            // (Opcional) Si quieres borrar imagen previa, hazlo en tu service (actualizar).
        }

        // Actualizamos vía service
        $this->cuponService->actualizar($id, $data);

        return redirect()->route('cupon.index')
            ->with('success', 'Cupón actualizado correctamente.')
            ->with('menu', 'Cupon');
    }

    /**
     * Elimina (borrado lógico) un cupón
     */
    public function destroy($id)
    {
        // Llamamos al service para marcar eliminado=1
        $this->cuponService->eliminar($id);

        return redirect()->route('cupon.index')
            ->with('success', 'Cupón eliminado correctamente.')
            ->with('menu', 'Cupon');
    }

    /**
     * Toggle para activar/desactivar
     */
    public function toggleActivacion($id)
    {
        // Llamamos al service
        $estadoActual = $this->cuponService->toggleActivacion($id);

        // Retornamos JSON (usado en AJAX, por ejemplo)
        return response()->json(['activo' => $estadoActual], 200);
    }
}
