<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Award;

use Illuminate\Http\Request;

class AwardsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $menu = "Premios";
        $title = "Premios";
        $filters = [ 
            'user' => auth()->user(), 
            'limit' => 100, 
            'ordenar_fecha' => null,
        ];

        $items = Award::filter( $filters )->orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('awards.table', compact('items'));
        }
        return view('awards.index', compact('items', 'menu', 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de premios";
        $menu = "Premios";
        $item = null;
        $filters = [ 'user' => auth()->user(), 'roles' => [2] ];

        if ( $id ) {
            $item = Award::find($id);
        }
        return view('awards.form', compact('item', 'menu', 'title'));
    }

    /**
     * Show the orders acording to the filters given for user.
     *
     */
    public function filter( Request $req )
    {
        $extraFilters = [ 
            'user' => auth()->user(), 
            'ordenar_fecha' => null, 
        ];

        $req->request->add( $extraFilters );
        
        $items = Award::filter( $req->all() )->orderBy('id', 'desc')->get();

        return view('awards.table', compact('items'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $photo = $this->uploadFile($req->file('photo'), 'img/premios', true);

        $item = New Award;

        $item->name = $req->name;
        $item->link = $req->link;
        $photo ? $item->photo = $photo : '';

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('premios'), 'status' => 'success', 'data' => $item ], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Award::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('premios')], 404); }

        $photo = $this->uploadFile($req->file('photo'), 'img/premios', true);

        $item->name = $req->name;
        $item->link = $req->link;
        $photo ? $item->photo = $photo : '';

        $item->save();
        
        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('premios'), 'status' => 'success', 'data' => $item ], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $item = Award::whereIn('id', $req->ids)
        ->first();

        if ( $item ) {
            $this->deletePath( $item->photo );
    
            $item->delete();
            return response(['msg' => 'Éxito eliminando el proyecto', 'url' => url('premios'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status del proyecto ', 'status' => 'error', 'url' => url('premios')], 404);
        }
    }
}
