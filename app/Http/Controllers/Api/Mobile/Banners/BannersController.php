<?php

namespace App\Http\Controllers\Api\Mobile\Banners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BannersController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now();
        try {
            $banners = DB::table('banners')
                ->where(function ($query) use ($today) {
                    // Condición 1: Banners activos y sin fechas (fechaInicio y fechaFin son NULL)
                    $query->where('activo', 1)
                        ->whereNull('fecha_inicio')
                        ->whereNull('fecha_fin');
                })
                ->orWhere(function ($query) use ($today) {
                    // Condición 2: Banners con fechas dentro del rango actual (sin importar si están activos o no)
                    $query->where('fecha_inicio', '<=', $today)
                        ->where('fecha_fin', '>=', $today);
                })
                ->orderBy('descripcion')
                ->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($banners);
    }

    public function getImage(Request $request)
    {

        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://butaca.udg.mx/api/banner?spinner=0', [
               // 'form_params' => [
               //     'spinner'  => $request->spinner,
               // ]
            ]);
           // dd($response);
            return $response;// response()->json([
                //'message' => $response,
           // ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
