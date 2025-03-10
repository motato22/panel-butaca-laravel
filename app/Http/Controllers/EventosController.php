<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Evento;
use App\Models\Recinto;
use App\Models\Genero;
use App\Models\GaleriaEvento;
use App\Models\Categoria;

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
            $query->where(function ($q) use ($searchTerm) {
                // buscamos por nombre
                $q->where('nombre', 'LIKE', "%$searchTerm%")

                    // por recinto
                    ->orWhereHas('recintoRelation', function ($sub) use ($searchTerm) {
                        $sub->where('nombre', 'LIKE', "%$searchTerm%");
                    })

                    // por genero sepa para que si paginamos pero asi quiere el cliente jaja
                    ->orWhereHas('generos', function ($sub) use ($searchTerm) {
                        $sub->where('nombre', 'LIKE', "%$searchTerm%");
                    });
            });
        }

        $menu = 'Eventos';
        $eventos = $query->with('recintoRelation')
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(10);

        // Si "recinto" es un int, convertimos; si no, lo dejamos
        $eventos->getCollection()->transform(function ($evento) {
            if (is_numeric($evento->recinto)) {
                $evento->recinto = Recinto::find($evento->recinto);
            } elseif (is_array($evento->recinto)) {
                $evento->recinto = (object) $evento->recinto;
            }
            return $evento;
        });

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

        $evento = new Evento;
        $recintos = Recinto::all();
        $categorias = Categoria::with('generos')->get();
        $menu = 'Eventos';

        // Si quieres mostrar géneros en el formulario de “crear”:
        // $categorias = Categoria::with('generos')->get(); // (solo si vas a agrupar por categorías)
        // $generos = Genero::all(); // o si prefieres cargar todos

        return view('eventos.nuevo', compact('menu', 'recintos', 'categorias', 'promociones', 'evento'));
    }

    /**
     * Guarda un evento en la base de datos.
     */
    public function store(Request $request)
    {
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
            'generos' => 'array',
            'generos.*' => 'exists:generos,id',
        ]);

        $evento = new Evento();
        $evento->fill($request->except(['foto', 'es_gratuito', 'recomendado', 'horarios', 'generos']));

        $evento->recinto = (int) $request->input('recinto');
        $evento->es_gratuito = $request->has('es_gratuito') ? 1 : 0;

        // Si es gratuito, forzamos precios a 0
        if ($evento->es_gratuito) {
            $evento->precio_bajo = '0';
            $evento->precio_alto = '0';
        }

        // Manejo de la imagen
        if ($request->hasFile('foto')) {
            $fileName = md5(uniqid()) . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/uploads/eventos', $fileName);
            $evento->foto = $fileName;
        } else {
            $evento->foto = null;
        }

        // Manejo de horarios (JSON decodificado)
        if ($request->filled('horarios')) {
            $horarios = json_decode($request->input('horarios'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $evento->horario = json_encode($horarios);
                $fechas = array_keys($horarios);
                sort($fechas);
                $evento->fecha_inicio = $fechas[0];
                $evento->fecha_fin = end($fechas);
            } else {
                return back()->withErrors(['horarios' => 'Error al procesar los horarios.']);
            }
        } else {
            $evento->horario = null;
            $evento->fecha_inicio = null;
            $evento->fecha_fin = null;
        }

        // Guardamos primero el evento
        $evento->save();

        // Sincronizar géneros en la pivote (genero_evento)
        $evento->generos()->sync($request->input('generos', []));

        return redirect()->route('eventos.index')->with('success', 'Evento creado correctamente.');
    }

    /**
     * Muestra el formulario de edición de un evento.
     */
    public function edit(Evento $evento)
    {
        // Decodificamos su horario para mostrarlo en el form
        $evento->horario = json_decode($evento->horario, true) ?? [];

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

        $menu = "Eventos";
        $title = "Editar Evento";

        // Si quieres mostrar todos los géneros disponibles:
        // $generos = Genero::all();

        $categorias = Categoria::with('generos')->get();

        return view('eventos.nuevo', compact('menu', 'title', 'evento', 'promociones', 'recintos', 'categorias'));
    }

    /**
     * Actualiza un evento en la base de datos (incluyendo géneros).
     */
    public function update(Request $request, Evento $evento)
    {
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
            'generos' => 'array',
            'generos.*' => 'exists:generos,id',
        ]);

        // Rellenamos excepto “foto” y “horarios” para tratarlos aparte
        $evento->fill($request->except(['foto', 'horarios', 'generos']));

        $evento->recinto = (int) $request->input('recinto');
        $evento->es_gratuito = $request->has('es_gratuito') ? 1 : 0;

        // Si es gratuito, forzamos precios a 0
        if ($evento->es_gratuito) {
            $evento->precio_bajo = '0';
            $evento->precio_alto = '0';
        }

        // Manejo de la imagen en actualización
        if ($request->hasFile('foto')) {
            // Borrar la imagen anterior si existe
            if ($evento->foto) {
                Storage::disk('public')->delete('uploads/eventos/' . $evento->foto);
            }

            $fileName = md5(uniqid()) . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->storeAs('public/uploads/eventos', $fileName);
            $evento->foto = $fileName;
        }

        // Manejo del horario (JSON)
        if ($request->filled('horarios')) {
            $horarios = json_decode($request->input('horarios'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $evento->horario = json_encode($horarios);
                $fechas = array_keys($horarios);
                sort($fechas);
                $evento->fecha_inicio = $fechas[0];
                $evento->fecha_fin = end($fechas);
            } else {
                return back()->withErrors(['horarios' => 'Error al procesar los horarios.']);
            }
        } else {
            $evento->horario = null;
            $evento->fecha_inicio = null;
            $evento->fecha_fin = null;
        }

        // Guardamos primero el evento
        $evento->save();

        // Sincronizar géneros en la pivote
        $evento->generos()->sync($request->input('generos', []));

        return redirect()->route('eventos.index')->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Elimina un evento de la base de datos.
     */
    public function destroy(Evento $evento)
    {
        // Eliminar imagen principal
        Storage::disk('public')->delete('uploads/eventos/' . $evento->foto);


        // Eliminar el evento (y se eliminarán las filas de pivote si la FK está en cascade)
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
                'image' => $image->store('uploads/eventos/galeria', 'public'),
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

    public function attachGenero(Request $request, Evento $evento)
    {
        $request->validate([
            'genero_id' => 'required|exists:generos,id'
        ]);

        $evento->generos()->attach($request->input('genero_id'));

        // Respuesta JSON si manejas todo por AJAX
        return response()->json([
            'success' => true,
            'message' => 'Género agregado',
        ]);
    }

    public function detachGenero(Evento $evento, \App\Models\Genero $genero)
    {
        $evento->generos()->detach($genero->id);

        return response()->json([
            'success' => true,
            'message' => 'Género eliminado',
        ]);
    }
}
