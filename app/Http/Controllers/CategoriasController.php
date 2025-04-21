<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Genero;

use Illuminate\Http\Request;

class CategoriasController extends Controller
{

    public function index()
    {


        $menu = "Categorias";
        $title = "Categorias";

        $categorias = Categoria::get();

        return view('categorias.index', compact('menu', 'title', 'categorias'));
    }

    public function create()
    {

        $menu = "Categorias";
        $title = "Categorias";

        return view('categorias.create', compact('menu', 'title'));
    }

    public function edit($id)
    {
        $menu = "Categorias"; // Define la variable $menu
        $title = "Editar Categoría";

        $categoria = Categoria::findOrFail($id);

        return view('categorias.create', compact('menu', 'title', 'categoria'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'background' => 'nullable|string',
            'thumbnailFile' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $categoria = new Categoria;
        $categoria->nombre = $request->nombre;
        $categoria->background = $request->background;

        // Guardar la imagen si se sube un archivo
        if ($request->hasFile('thumbnailFile')) {
            $file = $request->file('thumbnailFile');
            // Genera un nombre único (puedes usar time(), uuid, etc.)
            $filename = time() . '_' . $file->getClientOriginalName();
            // Mueve el archivo a public/uploads/categorias
            $file->move(public_path('uploads/categorias'), $filename);
            // Guarda la ruta relativa en la BD
            $categoria->thumbnail = 'uploads/categorias/' . $filename;
        }

        $categoria->save();

        // Guardar los géneros asociados a la categoría
        if ($request->has('generos')) {
            $generos = json_decode($request->generos, true);
            if (!empty($generos)) {
                foreach ($generos as $gen) {
                    Genero::create([
                        'categoria_id' => $categoria->id,
                        'nombre' => $gen['nombre'],
                        'color' => $gen['color']
                    ]);
                }
            }
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }


    public function update(Request $request, $id)
    {

        $categoria = Categoria::where('id', $id)->first();

        $request->validate([
            'nombre' => 'required|string|max:190',
            'background' => 'nullable|string|max:190',
            'thumbnailFile' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        $categoria->nombre = $request->nombre;
        $categoria->background = $request->background;

        // Verificar si se subió una nueva imagen
        if ($request->hasFile('thumbnailFile')) {
            $file = $request->file('thumbnailFile');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categorias'), $filename);
            $categoria->thumbnail = 'uploads/categorias/' . $filename;
        }

        $categoria->save();

        // Agregar nuevos géneros si se envían en la solicitud
        if ($request->has('generos')) {
            $generos = json_decode($request->generos, true); // Convertir JSON a array
            if (!empty($generos)) {
                foreach ($generos as $gen) {
                    Genero::create([
                        'categoria_id' => $categoria->id,
                        'nombre' => $gen['nombre'],
                        'color' => $gen['color']
                    ]);
                }
            }
        }

        return redirect()->route('categorias.index')->with('success', 'Categorias update Succesfully.');
    }

    public function delete($id)
    {
        // Busca la categoría por ID, o lanza un error 404 si no se encuentra
        $categoria = Categoria::findOrFail($id);

        // Elimina todos los géneros asociados a la categoría
        Genero::where('categoria_id', $categoria->id)->delete();

        // Elimina la categoría
        $categoria->delete();

        // Redirige con un mensaje de éxito
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada con éxito.');
    }

    public function getGeneros(\App\Models\Categoria $categoria)
    {
        // Retorna todos los géneros de esa categoría en formato JSON
        return response()->json($categoria->generos);
    }
}
