<?php

namespace App\Http\Controllers;

use DB;

use \App\Models\User;

use Illuminate\Http\Request;

class NotificacionesPushController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = $menu = 'Notificaciones push';
        $start_date = date('Y-m-d H:m:s');
        $verifyPlayerID = true;
        $customers = User::filter([ 'user' => auth()->user(), 'roles' => [2,3] ])->whereNotNull('player_id')->get();
        // $customers = User::filter_rows(auth()->user(), [2], null, null, $verifyPlayerID);

        return view('notificaciones_push.index', compact('menu', 'title', 'customers', 'start_date'));
    }

    /**
     * Filter the users to send a notification
     *
     * @return \Illuminate\Http\Response
     */
    public function filterUsers(Request $req)
    {
        $res = $this->getNotifcationsUSers($req);

        $customers = $res['data'];

        return response(['data' => $customers, 'msg' => 'Usuarios enlistados a continuación', 'status' => 'success'], 200);
    }

    /**
    * Get the notifications parameters, so, we can decide if send an individual or a general notification. 
    * @return $this->sendNotification
    */
    public function sendPush(Request $req) 
    {
        $users_id = array();
        $type = $req->type;
        $app_id = env('ONE_SIGNAL_APP_ID');
        $app_key = env('ONE_SIGNAL_API_KEY');
        $app_icon = null;
        $title = $req->title;
        $content = $req->content;
        $date = $req->date;
        $time = $req->time;
        $data = array("origin" => "api_system");

        if ( $type == 1 ) {#General
            $res = $this->getNotifcationsUSers($req);

            $customers = $res['data'];

            foreach( $customers as $customer ) { $users_id[] = $customer->id; }
        } else {#Individual
            $users_id = $req->users_id;
        }

        $response = $this->sendNotification($type, $app_id, $app_key, $app_icon, $title, $content, $date, $time, $data, $users_id);

        if ( property_exists($response, 'errors') ) {
            $msg = 'Notificación no enviada, revise que los parámetros estén escritos correctamente o que la aplicación cuente con usuarios vigentes';
            #Código para ver el error de la notificación push, descomentar si es necesario.
            #dd($res);
            return response(['msg' => $msg, 'status' => 'warning'], 400);
        } else {
            return ['msg' => 'Notificación enviada exitósamente', 'status' => 'success'];
        }
        // $str_errors = '';
        // if ( array_search('error', $response) ) {
        //     return response(['msg' => $response['msg'], 'status' => 'warning'], 400);
        // } else {
        //     return response(['msg' => $response['msg'], 'status' => 'success'], 200);
        // }
    }
}
