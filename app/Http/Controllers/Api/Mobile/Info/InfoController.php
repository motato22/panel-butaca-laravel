<?php

namespace App\Http\Controllers\Api\Mobile\Info;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class InfoController extends Controller
{
    public function sitiosIntereses(Request $request)
    {
        try {
            $sitio_interes = DB::table('sitio_interes')->orderBy('nombre')->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($sitio_interes);
    }

    public function show(Request $request,$slug)
    {
        try {
            $info = DB::table('info')->where('slug','=',$slug)->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($info);
    }
}
