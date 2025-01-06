<?php

namespace App\Http\Controllers;

use Socialite;

use \App\Models\Blog;
use \App\Models\User;
use \App\Models\Project;
use \App\Models\Payment;
use \App\Models\Property;
use \App\Models\Transaccion;

// use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Validate the user login.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        if ( auth()->attempt(['correo' => $req->email, 'password' => $req->password])  ||  auth()->attempt(['username' => $req->email, 'password' => $req->password] )) {
            if (auth()->user() && auth()->user()->role == 'ROLE_ADMIN') {
                return redirect()->to('dashboard');
            /*} elseif (auth()->user() && auth()->user()->role == 'ROLE_APP' && auth()->user()->verificado == 1 && auth()->user()->tipo_login_id == 1) {
                return redirect()->to('mi-perfil');
            } elseif (auth()->user() && auth()->user()->role == 'ROLE_ALTERNOS') {
                return redirect()->to('mi-perfil');*/
            } else {
                session(['msg' => 'Usuario inválido']);
                auth()->logout();
            }
            
        } else {

            $user = User::where('correo', $req['email'])->withTrashed()->first();

            if (! $user ) {

                session([ 'msg' => 'Usuario inválido']);
                session()->forget('email');

            } else {

                if ( $user->deleted_at ) {

                    session([ 'msg' => 'Cuenta baneada']);
                    session(['email' => $req['email']]);

                } else {

                    session([ 'msg' => 'Contraseña incorrecta']);
                    session(['email' => $req['email']]);
                    
                }
            }
            auth()->logout();
        }

        return redirect()->to('/')->withErrors([]);
    }

    /**
     * redirect to the dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function loadDashboard()
{
    $title = $menu = 'Inicio';
    $data = $this->getDashboarData();
    // Elimina la asignación de la variable $proximos
    // $proximos = Payment::orderBy('payment_date', 'desc')->limit('50')->get();

    $filters = [
        'solo_vigentes'    => true, 
        'visible_en_app'   => 'S', 
        'limit'            => 50,
        'ordenar_fecha'    => true,
    ];
    
    // Elimina 'proximos' de los datos enviados a la vista
    return view('layouts.dashboard', ['data' => json_decode($data), 'graphic_data' => [], 'title' => $title, 'menu' => $menu]);
}


    /**
     * Shows the sign up form
     *
     * @return \Illuminate\Http\Response
     */
    public function sign_up()
    {
        return view('layouts.sign_up');
    }

    /**
     * Shows the sign up form
     *
     * @return \Illuminate\Http\Response
     */
    public function resetView()
    {
        return view('layouts.reset');
    }

    /**
     * Shows the sign up form
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $req)
    {
        $item = User::where('email', $req->email)
        ->whereIn('role_id', [2,3])
        ->where('tipo_login_id', 1)// Native
        ->first();

        if ( $item ) {
            $newPass = Str::random(6);

            $item->password = bcrypt( $newPass );
            $item->save();

            $params = array();

            $params['view'] = 'mails.reset_password';
            $params['subject'] = 'Cambio de contraseña';
            $params['user'] = $item;
            $params['email'] = $item->email;
            $params['password'] = $newPass;

            auth()->logout();
            
            $this->f_mail( $params );
        }

        return response(['status' => 'success', 'msg' => 'Correo enviado', 'url' => url('/')], 200);
    }

    /**
     * Get the dashboard data.
     *
     */
    public function getDashboarData() 
    {
        $data = new \stdClass();
        
       
        return json_encode($data);
    }

    /**
     * Get top 10 users.
     */
    public function getTopUsers()
    {
        return [];

        $items = User::whereHas('recorrido_en_curso')->orderBy('fullname', 'ASC')->get();

        return $items;
    }

    /**
     * Get weekly sales.
     */
    public function getWeeklySales($tipo = null) 
    {
        return [];

        $day_name = array();
        $array_week_day = array();
        $array_sales_day = array();
        $current_week = array();
        $array_days = array('','Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
        $data_sales = Evento::getLastWeekSales();

        for ( $i=0; $i <= 6; $i++ ) {
            $current_date = date_create(date("Y-m-d H:i:s"));
            $current_date = date_sub($current_date, date_interval_create_from_date_string($i.' days'));
            array_push($current_week, $current_date->format('Y-m-d'));
        }

        foreach ($current_week as $day) {
            array_push($day_name, $array_days[date('N', strtotime($day))]);
        }
        
        foreach ($data_sales as $value) {
            array_push($array_week_day, date_create($value->fecha_visita)->format('Y-m-d'));
            array_push($array_sales_day, $value->total_visitas);
        }

        $final_array = $current_week;

        foreach ($final_array as $key => $value) { $final_array[$key] = 0; }

        foreach ($array_week_day as $key => $val) {
            $found = array_search($val, $current_week);
            is_int($found) ? $final_array[$found] = $array_sales_day[$key] : '';
        }

        $object = new \stdClass();
        $object->week_days = array_reverse($day_name);
        $object->total_visitas = array_reverse($final_array);

        return json_encode($object);
    }

    /**
     * Destroy's the current session.
     *
     */
    public function logout() 
    {
        auth()->logout();
        return redirect('/');
    }
}
