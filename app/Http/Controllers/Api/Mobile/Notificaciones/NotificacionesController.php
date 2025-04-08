<?php

namespace App\Http\Controllers\API\Mobile\Notificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use Illuminate\Notifications\Notification;

class NotificacionesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $id = $request->input('idUser');
            $dominio = $request->input('dominio');

            // Obtener el segmento del usuario
            $usuario = DB::table('usuarios')->where('id', $id)->first();
            $segmento = $usuario->segmento ?? '';

            $usuario_notificacion = DB::table('notificaciones')
            ->where('activo', 1)
            ->whereIn('dominio', [0, $dominio])
            ->whereIn('segmento', ['Todos', $segmento == 'General' ? 'Publico' : $segmento])
            ->orderBy('id','desc')->get();
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($usuario_notificacion);
    }

}

// + (OK) Las imagens en vista completa
// + () Segmentacion notifiaciones
// + () Publico seria todos menos comunidad-X
// + (OK) Ver correctamente textos en T&C, Ayuda y AdP
