<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NewData;
use App\Models\NewCategory;

use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        
        return view('users.index');
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de noticia";
        $menu = "Prensa";
        $item = null;
        $categories = NewCategory::all();

        if ( $id ) {
            $item = NewData::find($id);
        }
        return view('news.form', compact('item', 'categories', 'menu', 'title'));
    }

    /**
     * Show the orders acording to the filters given for user.
     *
     */
    public function filter( Request $req )
    {
        $items = NewData::filter( $req->all() )->orderBy('id', 'desc')->get();

        $view = 'news.table';
        
        return view($view, compact('items'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $photo = $this->uploadFile($req->file('photo'), 'img/prensa', true);

        $item = New NewData;

        $item->link = $req->link;
        $photo ? $item->photo = $photo : '';

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('prensa'), 'status' => 'success', 'data' => $item ], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = NewData::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('prensa')], 404); }

        $photo = $this->uploadFile($req->file('photo'), 'img/prensa', true);

        $item->link = $req->link;
        $photo ? $item->photo = $photo : '';

        $item->save();
        
        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('prensa'), 'status' => 'success', 'data' => $item ], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $item = NewData::whereIn('id', $req->ids)
        ->first();

        if ( $item ) {
            $this->deletePath($item->photo);
            $item->delete();
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('prensa'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('prensa')], 404);
        }
    }
}
