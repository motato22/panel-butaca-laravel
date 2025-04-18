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
            $hoy = Carbon::now('America/Mexico_City')->format('Y-m-d');

            $recintos = Recinto::whereHas('eventos', function ($query) use ($hoy) {
                    $query->where('fecha_inicio', '>=', $hoy)
                          ->orWhere('fecha_fin', '>=', $hoy);
                })
                ->orderBy('nombre')
                ->get()
                ->map(function ($recinto) {
                    $recinto->foto_url = Storage::disk('public')
                        ->url('uploads/recintos/' . $recinto->foto);
                    return $recinto;
                });
        } catch (\Throwable $th) {
            return response()->json([
                'exito'   => false,
                'msg'     => $th->getMessage(),
                'status'  => 500
            ], 500);
        }

        return response()->json($recintos);
    }

    public function show(Request $request, $id)
    {
        try {
            $recinto = Recinto::findOrFail($id);
            $recinto->foto_url = Storage::disk('public')
                ->url('uploads/recintos/' . $recinto->foto);
        } catch (\Throwable $th) {
            return response()->json([
                'exito'   => false,
                'msg'     => $th->getMessage(),
                'status'  => 500
            ], 500);
        }

        return response()->json($recinto);
    }
}