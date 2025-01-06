<?php

namespace App\Http\Controllers\Api;

use \App\Models\User;
use \App\Models\SupportFaq;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportFaqsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $filters = ['owner_id' => $req->user()->id];

        $data = SupportFaq::filter( $filters )->orderBy('id', 'desc')->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Preguntas de soporte técnico enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay preguntas de soporte técnico por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $item = New SupportFaq;

        $item->user_id = $req->user()->id;
        $item->subject = $req->subject;
        $item->message = $req->message;
        $item->email   = $req->email ?? $req->user()->email;

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'status' => 'success', 'data' => $item], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = SupportFaq::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 200); }

        $item->subject = $req->subject;
        $item->message = $req->message;
        // $item->email   = $req->email ?? $req->user()->email;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'status' => 'success', 'data' => $item], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'las preguntas' : 'la pregunta';
        $item = SupportFaq::where('id', $req->id)
        ->first();
        // ->delete();

        if ( $item ) {
            $item->delete();
            return response(['msg' => 'Éxito eliminando '.$msg, 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error'], 404);
        }
    }
}
