<?php

namespace App\Http\Controllers;

use \App\Models\User;
use \App\Models\SupportFaq;

use Illuminate\Http\Request;

class SupportFaqsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Soporte";
        $menu = "Configuración";
        $items = SupportFaq::orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('supports.table', compact('items'));
        }
        return view('supports.index', compact('items', 'menu' , 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de soporte";
        $menu = "Configuración";
        $item = null;
        if ( $id ) {
            $item = SupportFaq::find($id);
        }
        return view('supports.form', compact('item', 'menu', 'title'));
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = SupportFaq::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('configuracion/soporte')], 404); }

        $item->email    = $req->email;
        $item->subject  = $req->subject;
        $item->message  = $req->message;
        $item->reply    = $req->reply;

        $item->save();

        // Falta el código para enviar correo y responder la pregunta

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('configuracion/soporte'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'las preguntas' : 'la pregunta';
        $item = SupportFaq::whereIn('id', $req->ids)
        ->first();
        // ->delete();
        //->update(['status' => $req->status]);

        if ( $item ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('configuracion/soporte'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('configuracion/soporte')], 404);
        }
    }
}
