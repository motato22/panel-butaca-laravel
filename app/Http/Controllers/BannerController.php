<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BannerController extends Controller
{
    /**
     * Muestra la lista de Banners.
     */
    public function index()
    {
        // Obtenemos todos los banners
        $banners = Banner::orderByDesc('id')->get();

        // Generamos datos estadísticos (opcional, similar a "meta" en tu Twig)
        $total    = $banners->count();
        $activos  = $banners->where('activo', 1)->count();
        $inactivos = $total - $activos;

        // $menu es la variable para marcar en tu layout el menú activo
        $menu = 'Banner';

        // Retornamos la vista de index con la data
        return view('banners.index', compact('banners', 'total', 'activos', 'inactivos', 'menu'));
    }

    /**
     * Muestra el formulario para crear un nuevo Banner.
     */
    public function create()
    {
        $menu = 'Banner';
        return view('banners.create', compact('menu'));
    }

    /**
     * Procesa el guardado de un nuevo Banner.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion'       => 'nullable|string|max:190',
            'texto'             => 'nullable|string|max:190',
            'ubicacion'         => 'required|integer',
            'ubicacion_imagen'  => 'required|integer',
            'activo'            => 'required|boolean',
            'url'               => 'nullable|url|max:500',
            'fecha_inicio'      => 'nullable|date',
            'fecha_fin'         => 'nullable|date',
            'imagen_file'       => 'nullable|image|max:2048',
        ]);

        $banner = new Banner();
        $banner->descripcion       = $request->input('descripcion');
        $banner->texto             = $request->input('texto');
        $banner->ubicacion         = $request->input('ubicacion');
        $banner->ubicacion_imagen  = $request->input('ubicacion_imagen');
        $banner->activo            = $request->boolean('activo');
        $banner->url               = $request->input('url');
        $banner->fecha_inicio      = $request->input('fecha_inicio');
        $banner->fecha_fin         = $request->input('fecha_fin');
        $banner->fecha_creacion = Carbon::now('America/Mexico_City');

        // Si viene imagen, la guardamos en "storage/app/public/uploads/banners"
        // y en la BD solo guardamos el nombre (por ejemplo, "foto.jpg")
        if ($request->hasFile('imagen_file')) {
            // Nombre de archivo único
            $filename = $request->file('imagen_file')->hashName();
            // Sube el archivo a la carpeta "uploads/banners" dentro de "public"
            $request->file('imagen_file')->storeAs('uploads/banners', $filename, 'public');
            // En la columna "imagen" solo guardamos el nombre
            $banner->imagen = $filename;
        }

        $banner->save();

        return redirect()
            ->route('banners.index')
            ->with('success', 'Banner creado correctamente.');
    }

    /**
     * Muestra el formulario de edición de un Banner.
     */
    public function edit(Banner $banner)
    {
        $menu = 'Banner';
        return view('banners.edit', compact('banner', 'menu'));
    }

    /**
     * Procesa la actualización de un Banner.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'descripcion'       => 'nullable|string|max:190',
            'texto'             => 'nullable|string|max:190',
            'ubicacion'         => 'required|integer',
            'ubicacion_imagen'  => 'required|integer',
            'activo'            => 'required|boolean',
            'url'               => 'nullable|url|max:500',
            'fecha_inicio'      => 'nullable|date',
            'fecha_fin'         => 'nullable|date',
            'imagen_file'       => 'nullable|image|max:2048',
        ]);

        $banner->descripcion       = $request->input('descripcion');
        $banner->texto             = $request->input('texto');
        $banner->ubicacion         = $request->input('ubicacion');
        $banner->ubicacion_imagen  = $request->input('ubicacion_imagen');
        $banner->activo            = $request->boolean('activo');
        $banner->url               = $request->input('url');
        $banner->fecha_inicio      = $request->input('fecha_inicio');
        $banner->fecha_fin         = $request->input('fecha_fin');

        if ($request->hasFile('imagen_file')) {
            // Opcional: borrar la anterior
            // Storage::disk('public')->delete('uploads/banners/'.$banner->imagen);

            $filename = $request->file('imagen_file')->hashName();
            $request->file('imagen_file')->storeAs('uploads/banners', $filename, 'public');
            $banner->imagen = $filename;
        }

        $banner->save();

        return redirect()
            ->route('banners.index')
            ->with('success', 'Banner actualizado correctamente.');
    }

    /**
     * Elimina un Banner de la BD.
     */
    public function destroy(Banner $banner)
    {
        // Opcional: Borrar la imagen del disco
        // Storage::disk('public')->delete(str_replace('storage/', '', $banner->imagen));

        $banner->delete();

        return redirect()
            ->route('banners.index')
            ->with('success', 'Banner eliminado correctamente.');
    }
}
