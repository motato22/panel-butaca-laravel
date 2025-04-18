<?php

namespace App\Http\Controllers\Api\Mobile\Recinto;

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
            $hoy = Carbon::now()
                ->setTimezone('America/Mexico_City')
                ->format('Y-m-d');

            $recintos = Recinto::whereHas('eventos', function ($q) use ($hoy) {
                    $q->where('fecha_inicio', '>=', $hoy)
                      ->orWhere('fecha_fin', '>=', $hoy);
                })
                ->orderBy('nombre')
                ->get();

            return response()->json($recintos);
        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json([
                'exito'  => false,
                'msg'    => $th->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $recinto = DB::table('recinto')->where('id', $id)->get();
            return response()->json($recinto);
        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json([
                'exito'  => false,
                'msg'    => $th->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
