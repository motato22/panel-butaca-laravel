<?php

namespace App\Http\Controllers;

use \App\Models\Faq;

use Illuminate\Http\Request;

class FaqsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Preguntas frecuentes";
        $menu = "Configuración";
        $items = Faq::orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('faqs.table', compact('items'));
        }
        return view('faqs.index', compact('items', 'menu' , 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario preguntas frecuentes";
        $menu = "Configuración";
        $item = null;
        if ( $id ) {
            $item = Faq::find($id);
        }
        return view('faqs.form', compact('item', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = New Faq;

        $item->question = $req->question;
        $item->answer   = $req->answer;

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('configuracion/preguntas-frecuentes'), 'status' => 'success'], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Faq::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('configuracion/preguntas-frecuentes')], 404); }

        $item->question = $req->question;
        $item->answer   = $req->answer;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('configuracion/preguntas-frecuentes'), 'status' => 'success'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'las preguntas' : 'la pregunta';
        $item = Faq::whereIn('id', $req->ids)
        ->first();
        //->update(['status' => $req->status]);

        if ( $item ) {
            $item->delete();

            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('configuracion/preguntas-frecuentes'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('configuracion/preguntas-frecuentes')], 404);
        }
    }
}
