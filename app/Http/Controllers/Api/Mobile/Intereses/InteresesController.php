<?php

namespace App\Http\Controllers\API\Mobile\Intereses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Intereses;
class InteresesController extends Controller
{
    public function getIntereses(Request $request)
    {
        try {
            $user_id = $request->get('user_id');
            $inter = $request->get('intereses');
                    for($i = 0; $i < count($inter); $i++) {

                        DB::table('usuario_genero')->insertOrIgnore([
                            'usuario_id' => $user_id,
                            'genero_id' => $inter[$i]
                        ]); 
                    } 
           return response()->json([
                'message' => 'ok',
            ]);   
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function getInteresesUser(Request $request, $user_id)
    {
        try {
            $usuario = DB::table('usuarios')->where('id', $user_id)->first(['intereses']);
            if ($usuario && !empty($usuario->intereses)) {
                return response()->json([
                    'message' => 'ok',
                    'intereses' => $usuario->intereses
                ]);
            } else {
                return response()->json([
                    'message' => 'No se encontraron intereses para el usuario especificado',
                    'intereses' => []
                ], 200);
            } 
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setInteresesUser(Request $request)
    {
        try {
            $user_id = $request->get('user_id');
            $intereses = $request->get('intereses');
            $intereses_str = (is_array($intereses) && !empty($intereses)) ? implode(',', $intereses) : '';
            $usuario = DB::table('usuarios')->where('id', $user_id)->first();
            if ($usuario) {
                // Actualizar los intereses existentes
                DB::table('usuarios')->where('id', $user_id)->update([
                    'intereses' => $intereses_str
                ]);
            } else {
                // Insertar un nuevo registro con los intereses
                DB::table('usuarios')->insert([
                    'id' => $user_id,
                    'intereses' => $intereses_str
                ]);
            }
            return response()->json([
                'message' => 'Intereses guardados exitosamente',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setSegmentoUser(Request $request)
    {
        try {
            $user_id = $request->get('user_id');
            $intereses = $request->get('segmento');
            $usuario = DB::table('usuarios')->where('id', $user_id)->first();
            if ($usuario) {
                // Actualizar los intereses existentes
                DB::table('usuarios')->where('id', $user_id)->update([
                    'segmento' => $intereses
                ]);
            } else {
                // Insertar un nuevo registro con los intereses
                DB::table('usuarios')->insert([
                    'id' => $user_id,
                    'segmento' => $intereses
                ]);
            }
            return response()->json([
                'message' => 'Segmento guardado exitosamente',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
   
}
