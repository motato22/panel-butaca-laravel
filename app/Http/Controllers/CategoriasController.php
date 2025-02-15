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
        $categoria = new Categoria;
        $categoria->nombre = $request->nombre;
        $categoria->background = $request->background;
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

        $categoria->nombre = $request->nombre;
        $categoria->background = $request->background;
        $categoria->save();

        return redirect()->route('categorias.index')->with('sucess', 'Categorias update Succesfully.');
    }

    public function delete($id)
    {
        // Busca la categoría por ID, o lanza un error 404 si no se encuentra
        $categoria = Categoria::findOrFail($id);

        // Elimina la categoría
        $categoria->delete();

        // Redirige con un mensaje de éxito
        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada con éxito.');
    }
}
