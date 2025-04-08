<?php

namespace App\Http\Controllers\API\Mobile\Cupones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CuponesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $dominio = $request->input('dominio');
            $segmento = $request->input('segmento');
            // $cupones = DB::table('cupon')->leftJoin('galeria_cupon', 'cupon.id', '=', 'galeria_cupon.cupon_id')
            // ->where('cupon.activo', '=', 1)
            // ->select('cupon.*', 'galeria_cupon.image')
            // ->orderBy('cupon.id')->get();
            $cupones = DB::table('notificaciones')
            ->where('activo', 1)
            ->whereIn('dominio', [$dominio])
            ->whereIN('segmento', ['Comunidad-Todos' , $segmento])
            // ->select('cupon.*', 'galeria_cupon.image')
            ->orderBy('id','desc')->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($cupones);
    }
}
