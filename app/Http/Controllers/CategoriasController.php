<?php

namespace App\Http\Controllers;

use App\Models\Categoria;

use Illuminate\Http\Request;

class CategoriasController extends Controller {

    public function index () {

        
        $menu = "Categorias";
        $title = "Categorias";

        $categorias = Categoria::get();

        return view('categorias.index', compact('menu','title', 'categorias'));
    }

    public function create () {

        $menu = "Categorias";
        $title = "Categorias";

        return view('categorias.form', compact('menu','title'));

    }

    public function edit ($id)
    {
        $menu = "Categorias";
        $title = "Editar Categoría";

        $item = Categoria::findOrFail($id);

        return view('categorias.form',compact('menu','title','item'));
    }

    public function add (Request $request) {

        $categoria = new Categoria;

        $categoria->nombre = $request->nombre;
        $categoria->background = $request->background;
        $categoria->save();

        return redirect()->route('categorias.index')->with('sucess','Categorias created Succesfully.');
 
    }

    public function update (Request $request, $id) {

        $categoria = Categoria::where('id', $id)->first();

        $categoria->nombre = $request->nombre;
        $categoria->background = $request->background;
        $categoria->save();

        return redirect()->route('categorias.index')->with('sucess','Categorias update Succesfully.');
 
    }

    public function delete (Request $request, $id) {

        $categoria = Categoria::where('id', $id)->first();

        $categoria->delete();

        return redirect()->route('categorias.index')->with('sucess','Categorias update Succesfully.');
 
    }
}