<?php

namespace App\Http\Controllers\Api;

use \App\Models\Award;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AwardsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $data = Award::orderBy('id', 'desc')->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Premios enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay Premios por mostrar', 'status' => 'error'], 200);
    }
}
