<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Evento;
use App\Models\Recinto;
use App\Models\Genero;
use App\Models\GaleriaEvento;
use App\Http\Requests\EventoRequest;

class EventosController extends Controller
{
    /**
     * Muestra la lista de eventos con paginación y búsqueda.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->query('search', '');
        $orderColumn = $request->query('order_column', 'id');
        $orderDirection = $request->query('order_direction', 'desc');
        $eventos = Evento::with('recinto')->get();

        $query = Evento::query();

        if ($searchTerm) {
            $query->where('nombre', 'LIKE', "%$searchTerm%");
        }

        $menu = 'Eventos';
        $eventos = $query->orderBy($orderColumn, $orderDirection)->paginate(10);

        return view('eventos.index', compact('menu', 'eventos', 'searchTerm'));
    }

    /**
     * Muestra el formulario de creación de un evento.
     */
    public function create()
    {

        $promociones = [
            '60%',
            '50%',
            '2x1',
            '40%',
            '3x2',
            '30%',
            '25%',
            '20%',
            '15%',
            '10%',
            '5%',
            'Sin promoción'
        ];

        $recintos = Recinto::all();

        $menu = 'Eventos';
        return view('eventos.nuevo', compact('menu', 'recintos', 'promociones'));
    }

    /**
     * Guarda un evento en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:190',
            'recinto' => 'required|exists:recinto,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'horario' => 'nullable|string',
            'precio_bajo' => 'required|string|max:190',
            'precio_alto' => 'required|string|max:190',
            'descripcion' => 'required|string',
            'facebook' => 'nullable|string|max:190',
            'instagram' => 'nullable|string|max:190',
            'web' => 'nullable|string|max:190',
            'twitter' => 'nullable|string|max:190',
            'youtube' => 'nullable|string|max:190',
            'snapchat' => 'nullable|string|max:190',
            'texto_promocional' => 'nullable|string|max:24',
            'video' => 'nullable|string|max:190',
            'url_compra' => 'nullable|string|max:190',
            'es_gratuito' => 'nullable|boolean',
            'recomendado' => 'nullable|boolean',
            'galeria.*' => 'nullable|image|max:2048',
        ]);

        $evento = new Evento();
        $evento->fill($request->except(['foto', 'es_gratuito', 'recomendado']));

        $evento->es_gratuito = $request->has('es_gratuito') ? 1 : 0;

        // Si es gratuito, aseguramos que precio_bajo y precio_alto sean 0
        if ($request->has('es_gratuito')) {
            $evento->es_gratuito = true;
            $evento->precio_bajo = '0';
            $evento->precio_alto = '0';
        } else {
            $evento->es_gratuito = false;
        }

        // Manejo de la imagen
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $image) {
                $fileName = md5(uniqid()) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('recintos/galeria', $fileName, 'public');

                GaleriaEvento::create([
                    'image' => $fileName,
                    'recinto_id' => $evento->id,
                ]);
            }
        }

        // Guardamos el evento en la base de datos
        $evento->save();

        return redirect()->route('eventos.index')->with('success', 'Evento creado correctamente.');
    }

    /**
     * Muestra el formulario de edición de un evento.
     */
    public function edit(Evento $evento)
    {
        return view('eventos.editar', compact('evento'));
    }

    /**
     * Actualiza un evento en la base de datos.
     */
    public function update(Request $request, Evento $evento)
    {
        $request->validate([
            'nombre' => 'required|string|max:190',
            'recinto' => 'required|exists:recintos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'es_gratuito' => 'nullable|boolean',
        ]);

        $evento->fill($request->except('es_gratuito'));
        $evento->es_gratuito = $request->has('es_gratuito') ? 1 : 0;

        if ($request->hasFile('foto')) {
            $fileName = md5(uniqid()) . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/eventos', $fileName);
            $evento->foto = $fileName;
        }

        $evento->save();

        return redirect()->route('eventos.index')->with('success', 'Evento actualizado correctamente.');
    }


    /**
     * Elimina un evento de la base de datos.
     */
    public function destroy(Evento $evento)
    {
        Storage::disk('public')->delete($evento->foto);
        $evento->galeria()->delete();
        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento eliminado correctamente.');
    }

    /**
     * Agrega imágenes a la galería del evento.
     */
    public function addImages(Request $request, Evento $evento)
    {
        $request->validate([
            'galeria.*' => 'required|image|max:2048',
        ]);

        foreach ($request->file('galeria') as $image) {
            GaleriaEvento::create([
                'image' => $image->store('eventos/galeria', 'public'),
                'evento_id' => $evento->id,
            ]);
        }

        return redirect()->route('eventos.addImages', $evento->id)->with('success', 'Imágenes agregadas correctamente.');
    }

    /**
     * Elimina una imagen de la galería del evento.
     */
    public function deleteImage(GaleriaEvento $imagen)
    {
        Storage::disk('public')->delete($imagen->image);
        $imagen->delete();

        return redirect()->route('eventos.addImages', $imagen->evento_id)->with('success', 'Imagen eliminada correctamente.');
    }
}
