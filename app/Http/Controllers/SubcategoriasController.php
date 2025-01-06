<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Subcategoria;

use Illuminate\Http\Request;

class SubcategoriasController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = $menu = "Subcategorías";
        $items = Subcategoria::orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('subcategorias.table', compact('items'));
        }
        return view('subcategorias.index', compact('items', 'menu' , 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario";
        $menu = "Subcategorías";
        $categorias = Categoria::all();
        $item = null;
        if ( $id ) {
            $item = Subcategoria::find($id);
        }

        #solo edición
        // if (! $item ) {
        //     return view('errors.404');
        // }
        
        return view('subcategorias.form', compact('item', 'categorias', 'menu', 'title'));
    }

    /**
     * Filter user customer acording to the filters given by user.
     *
     */
    public function filter(Request $req)
    {
        $items = Subcategoria::filter( $req->all() )->get();

        if ( $req->only_data ) {
            return response(['msg' => 'Subcategorías enlistadas a continuación', 'status' => 'success', 'data' => $items, 'total' => count($items)], 200);
        }

        return view('subcategorias.table', compact(['items']));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $categoria = Categoria::find($req->categoria_id);
        if (! $categoria ) { return response(['msg' => 'Seleccione una categoría válida para continuar', 'status' => 'error'], 404); }
        
        $item = New Subcategoria;

        $img = $this->uploadFile($req->file('foto'), 'img/subcategorias', true);

        $item->categoria_id = $req->categoria_id;
        $item->nombre = $req->nombre;
        $img ? $item->foto = $img : '';
        $item->mostrar = $req->mostrar ? 'S' : 'N';

        $item->save();

        return response(['msg' => 'Registro guardado exitósamente correctamente', 'url' => url('subcategorias'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Subcategoria::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('subcategorias')], 404); }
        
        $categoria = Categoria::find($req->categoria_id);
        if (! $categoria ) { return response(['msg' => 'Seleccione una categoría válida para continuar', 'status' => 'error'], 404); }

        $img = $this->uploadFile($req->file('foto'), 'img/subcategorias', true);

        $item->categoria_id = $req->categoria_id;
        $item->nombre = $req->nombre;
        $img ? $item->foto = $img : '';
        $item->mostrar = $req->mostrar ? 'S' : 'N';

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('subcategorias'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = Subcategoria::whereIn('id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('subcategorias'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('subcategorias')], 404);
        }
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $req)
    {
        $item = Subcategoria::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('subcategorias')], 404); }

        $item->activo = $req->activo;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('subcategorias'), 'status' => 'success', 'item' => $item], 200);
    }
}
