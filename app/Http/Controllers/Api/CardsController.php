<?php

namespace App\Http\Controllers\Api;

use \App\Models\User;
use \App\Models\Card;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CardsController extends Controller
{
    /**
     * Obtiene las tarjetas vinculadas a un usuario
     *
     * @return \Illuminate\Http\Response
     */
    public function getCards(Request $req)
    {
        $user = User::find($req->user_id);

        if (! $user ) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }

        $data = Tarjeta::where('user_id', $user->id)->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Tarjetas enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay tarjetas por mostrar', 'data' => $data, 'status' => 'error'], 200);
    }

    /**
     * Save a card 
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        // dd($req->user());
        $res = $this->saveCard($req, $req->user());
        if ( $res['status'] != 'success' ) { return response($res, 500); }#Card wasn't created on openpay
        // dd($res['data']);
        $card = New Card;

        $card->user_id   = $req->user()->id;
        $card->token     = $res['data']->id;
        $card->brand     = $res['data']->brand;
        $card->last_four = $res['data']->last4;

        $card->save();

        return response(['msg' => 'Tarjeta guardada exitósamente', 'data' => $card, 'status' => 'success'], 200);
    }

    /**
     * Elimina una tarjeta vinculada a un usuario
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $tarjeta = Tarjeta::where('id', $req->tarjeta_id)->where('user_id', $req->user()->id)->first();

        if (! $tarjeta ) { return response(['msg' => 'ID de tarjeta inválido', 'status' => 'error'], 200); }
        
        $res = $this->deleteCard($req, $tarjeta);
        if ( $res['status'] != 'success' ) { return response($res, 500); }

        $tarjeta->delete();

        return response(['msg' => 'Tarjeta eliminada exitósamente', 'status' => 'success'], 200);
    }
}
