<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    public function enviar(Request $request)
    {
        $mensaje = $request->input('mensaje');

        // Lógica para enviar notificaciones
        // Ejemplo:
        // Notification::send($users, new CustomNotification($mensaje));

        return response()->json(['success' => true]);
    }
}
