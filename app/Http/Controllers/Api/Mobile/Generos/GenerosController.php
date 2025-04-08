<?php

namespace App\Http\Controllers\API\Mobile\Generos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GenerosController extends Controller
{
    public function index(Request $request)
    {
        try {
            $generos = DB::table('generos')->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json(['message' => $generos]);
    }
}
