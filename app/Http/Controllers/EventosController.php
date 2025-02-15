<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Evento;
use App\Models\Recinto;
use App\Models\Genero;
use App\Models\GaleriaEvento;

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

        $query = Evento::query();

        if ($searchTerm) {
            $query->where('nombre', 'LIKE', "%$searchTerm%");
        }

        $menu = 'Eventos';
        $eventos = Evento::with('recinto')->orderBy($orderColumn, $orderDirection)->paginate(10);

        $eventos->getCollection()->transform(function ($evento) {
            if (is_numeric($evento->recinto)) {
                $evento->recinto = Recinto::find($evento->recinto); // Busca el recinto en la BD
            } elseif (is_array($evento->recinto)) {
                $evento->recinto = (object) $evento->recinto; // Convierte a objeto
            }
            return $evento;
        });

        // dd($eventos->toArray());
        // dd(Evento::with('recinto')->orderBy('id', 'desc')->first()->toArray());

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
        // Depuración: Ver qué datos se reciben
        // dd($request->all());

        $request->validate([
            'nombre' => 'required|string|max:190',
            'recinto' => 'required|exists:recinto,id',
            'tipo_horario' => 'required|in:temporada,funciones,unico_dia',
            'horarios' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (is_null(json_decode($value, true))) {
                        $fail('El campo horarios debe ser un JSON válido.');
                    }
                }
            ],
            'precio_bajo' => $request->has('es_gratuito') ? 'nullable' : 'required|string|max:190',
            'precio_alto' => $request->has('es_gratuito') ? 'nullable' : 'required|string|max:190',
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
            'foto' => 'nullable|image|max:2048',
        ]);

        $evento = new Evento();
        $evento->fill($request->except(['foto', 'es_gratuito', 'recomendado', 'horario']));

        $evento->recinto = (int) $request->input('recinto');

        $evento->es_gratuito = $request->has('es_gratuito') ? 1 : 0;

        // Si es gratuito, precio es 0
        if ($request->has('es_gratuito')) {
            $evento->precio_bajo = '0';
            $evento->precio_alto = '0';
        }

        // Manejo de la imagen
        if ($request->hasFile('foto')) {
            $fileName = md5(uniqid()) . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/eventos', $fileName);
            $evento->foto = $fileName;
        } else {
            $evento->foto = null;
        }

        // Manejo de horario
        if ($request->filled('horarios')) {
            $horarios = json_decode($request->input('horarios'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $evento->horario = json_encode($horarios);

                // Obtener la fecha más temprana (inicio) y la más tardía (fin)
                $fechas = array_keys($horarios);
                sort($fechas);

                $evento->fecha_inicio = $fechas[0]; // Primera fecha en la lista
                $evento->fecha_fin = end($fechas); // Última fecha en la lista
            } else {
                return back()->withErrors(['horarios' => 'Error al procesar los horarios.']);
            }
        } else {
            $evento->horario = null;
            $evento->fecha_inicio = null;
            $evento->fecha_fin = null;
        }

        // dd($evento);

        // Guardamos el evento en la base de datos
        $evento->save();

        return redirect()->route('eventos.index')->with('success', 'Evento creado correctamente.');
    }

    /**
     * Muestra el formulario de edición de un evento.
     */
    public function edit(Evento $evento)
    {
        $evento->horario = json_decode($evento->horario, true) ?? [];
        return view('eventos.editar', compact('evento'));
    }

    /**
     * Actualiza un evento en la base de datos.
     */
    public function update(Request $request, Evento $evento)
    {
        // dd($request->all(), $request->hasFile('foto'));

        $request->validate([
            'nombre' => 'required|string|max:190',
            'recinto' => 'required|exists:recinto,id',
            'tipo_horario' => 'required|in:temporada,funciones,unico_dia',

            'horarios' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (is_null(json_decode($value, true))) {
                        $fail('El campo horarios debe ser un JSON válido.');
                    }
                }
            ],

            'precio_bajo' => $request->has('es_gratuito') ? 'nullable' : 'required|string|max:190',
            'precio_alto' => $request->has('es_gratuito') ? 'nullable' : 'required|string|max:190',

            'descripcion' => 'required|string',
            'es_gratuito' => 'nullable|boolean',
        ]);

        $evento->fill($request->except(['horarios']));
        $evento->fill($request->except('es_gratuito', 'horario'));

        $evento->recinto = (int) $request->input('recinto');

        $evento->es_gratuito = $request->has('es_gratuito') ? 1 : 0;

        // Manejo de la imagen en actualización
        if ($request->hasFile('foto')) {
            // Eliminar la imagen anterior
            Storage::disk('public')->delete('eventos/' . $evento->foto);

            $fileName = md5(uniqid()) . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/eventos', $fileName);
            $evento->foto = $fileName;
        }

        // Manejo del horario
        if ($request->filled('horarios')) {
            $horarios = json_decode($request->input('horarios'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $evento->horario = json_encode($horarios);

                // Obtener la fecha más temprana y la más tardía
                $fechas = array_keys($horarios);
                sort($fechas);

                $evento->fecha_inicio = $fechas[0]; // Fecha más antigua
                $evento->fecha_fin = end($fechas); // Fecha más reciente
            } else {
                return back()->withErrors(['horarios' => 'Error al procesar los horarios.']);
            }
        } else {
            $evento->horario = null;
            $evento->fecha_inicio = null;
            $evento->fecha_fin = null;
        }

        // dd($evento);

        $evento->save();

        return redirect()->route('eventos.index')->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Elimina un evento de la base de datos.
     */
    public function destroy(Evento $evento)
    {
        Storage::disk('public')->delete('eventos/' . $evento->foto);
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
