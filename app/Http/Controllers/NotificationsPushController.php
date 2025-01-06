<?php

namespace App\Http\Controllers;

use DB;

use \App\Models\User;

use Illuminate\Http\Request;

class NotificationsPushController extends Controller
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
}
