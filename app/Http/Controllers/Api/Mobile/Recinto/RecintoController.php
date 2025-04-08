<?php

namespace App\Http\Controllers\API\Mobile\Recinto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use \App\Models\Recinto;

class RecintoController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Obtener la fecha actual
            $hoy = Carbon::now();
            $fechaMexico = Carbon::parse($hoy)->setTimezone('America/Mexico_City');

            // Formatear solo la fecha en formato YYYY-MM-DD
            $hoy = $fechaMexico->format('Y-m-d');
            // Consulta con join y filtros
            $recintos = Recinto::whereHas('eventos', function ($query) use ($hoy) {
                $query->where('fecha_inicio', '>=', $hoy)
                      ->orWhere('fecha_fin', '>=', $hoy);
            })
            ->orderBy('nombre') // Ordena por nombre del recinto
            ->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($recintos);
    }

    public function show(Request $request,$id)
    {
        try {
            $recinto = DB::table('recinto')->where('id','=',$id)->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($recinto);
    }
}
