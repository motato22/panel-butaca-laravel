<?php

namespace App\Http\Controllers;

use Excel;

use \App\Models\User;
use \App\Models\Project;
use \App\Models\Property;

use \App\Exports\CustomersExport;

use Illuminate\Support\Str;

use Illuminate\Http\Request;

class CustomersController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $title = "Clientes";
       

        $projects   = Project::all();
        $properties = Property::all();
        $items = User::filter([ 'roles' => [2] ])->withTrashed()->get();

        if ( $req->ajax() ) {
            return view('users.customers.table', compact('items'));
        }
        return view('users.index', compact('items', 'projects', 'properties', 'menu', 'title'));
    }

    /**
     * Filter user franchise acording to the filters given by user.
     *
     */
    public function filter(Request $req)
    {
        $req->request->add([ 'user' => auth()->user(), 'roles' => [2] ]);

        $items = User::filter( $req->all() )->withTrashed()->get();

        return view('users.customers.table', compact(['items']));
    }
    
    /**
     * Show the form for creating/editing a user franchise.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de cliente";
        $menu = "Usuarios";
        $item = null;

        if ( $id ) {
            $item = User::where('id', $id)->where('role_id', 2)->first();
        }
        return view('users.customers.form', compact(['item', 'menu', 'title']));
    }

    /**
     * Save a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $exist = User::where('email', $req->email)->first();

        if ( $exist ) { return response(['msg' => 'Este correo no está disponible, utilice otro para continuar', 'status' => 'error'], 400); }

        $res = $this->saveCustomer($req);
        if ( $res['status'] != 'success' ) { return response($res, 500); }#Customer wasn't created on openpay
        
        // Se obtiene la clabe para pagos recurrentes (SPEI)
        $clabe = $res['data']->payment_sources->params['data'][0]['reference'];
        
        $img = $this->uploadFile($req->file('photo'), 'img/users/clientes', true);
        
        $customer = new User;

        $customer->password = bcrypt($req->password);// Contraseña que será modificada
        $customer->change_password = 1;
        $customer->fullname = $req->fullname; 
        $customer->email = $req->email;
        $customer->genre = $req->genre;
        $customer->photo = $img ?? 'img/users/default.jpg';
        $customer->player_id = $req->player_id ?? null;
        $customer->phone = $req->phone ?? null;
        $customer->country = $req->country ?? 'México';
        $customer->country_iso = $req->country_iso ?? 'MX';
        $customer->date_of_birth = $req->date_of_birth ?? null;
        $customer->receive_emails = 1;
        $customer->receive_notifications = 1;
        $customer->role_id = 2;//Role customer
        $customer->payment_token = $res['data']->id;
        $customer->clabe = $clabe;

        $customer->save();

        $token = $customer->createToken( Str::random(64) )->plainTextToken;

        return response([
            'msg'    => 'Usuario registrado correctamente', 
            'status' => 'success', 
            'url'    => url('usuarios/clientes'),
            'data'   => [ 
                'user'  => $customer->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at']),
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = User::where('id', $req->id)
        ->where('role_id', 2)
        ->first();

        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 404); }

        $exist = User::where('email', $req->email)->where('email', '!=', $item->email)->first();

        if ( $exist ) { return response(['msg' => 'Este correo no está disponible, utilice otro para continuar', 'status' => 'error'], 400); }

        $img = $this->uploadFile($req->file('photo'), 'img/users/clientes', true);

        $img ? $item->photo = $img : '';
        $item->fullname = $req->fullname;
        // $item->email = $req->email;
        $req->password ? $item->password = bcrypt($req->password) : '';
        $item->phone = $req->phone;
        $item->genre = $req->genre;
        $item->country = $req->country ?? 'México';
        $item->country_iso = $req->country_iso ?? 'MX';
        $item->date_of_birth = $req->date_of_birth;
        $item->receive_emails = $req->receive_emails ? 1 : 0;
        $item->receive_notifications = $req->receive_notifications ? 1 : 0;

        $item->save();

        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('usuarios/clientes'), 'status' => 'success'], 200);
    }

    /**
     * Set an user for use api rest.
     *
     * @return \Illuminate\Http\Response
     */
    public function setApiUser(Request $req)
    {
        $promotor = User::where('id', $req->user_id)->first();

        if (! $promotor ) { return response(['msg' => 'Promotor inválido', 'status' => 'error'], 400); }

        $fullname = 'Acceso a apirest de '.$promotor->fullname;
        $email = 'apirest_'.$promotor->email;

        $userApi = User::updateOrCreate(
            ['asociado_id' => $promotor->id, 'role_id' => 4],
            [
                'role_id'       => 4,
                'tipo_login_id' => 1,
                'fullname'      => $fullname, 
                'email'         => $email, 
                'asociado_id'   => $promotor->id, 
                // 'password'      => bcrypt($email),
                'password'      => $promotor->password,
                'foto'          => asset('img/users/default.jpg'),
                'pais'          => $promotor->pais, 
                'pais_iso'      => $promotor->pais_iso, 
            ]
        );

        // $userApi->save();
        // dd($userApi);

        $userApi->tokens()->delete();

        $token = $userApi->createToken( Str::random(64) )->plainTextToken;


        return response(['msg' => 'Apikey mostrada a continuación', 'status' => 'success', 'data' => [ 'userApi' => $userApi, 'token' => $token ] ], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $msg = count($req->ids) > 1 ? 'los registros' : 'el registro';
        $items = User::whereIn('id', $req->ids)
        ->delete();

        if ( $items ) {
            return response(['msg' => 'Éxito eliminando '.$msg, 'url' => url('usuarios/clientes'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status de '.$msg, 'status' => 'error', 'url' => url('usuarios/clientes')], 404);
        }
    }

    /**
     * Change the status of the specified resource.
     *
     */
    public function changeStatus(Request $req)
    {
        $users = User::whereIn('id', $req->ids)
        ->where('role_id', 2)
        ->restore();
        // ->update(['status' => $req->change_to]);

        if ( $users ) {
            return response(['url' => url('usuarios/clientes'), 'status' => 'success', 'msg' => 'Éxito cambiando el status del usuario'], 200);
        } else {
            return response(['msg' => 'Usuario no encontrado o inválido', 'status' => 'error'], 404);
        }
    }

    /**
     * Export the orders to excel according to the filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function export( Request $req )
    {
        $req->request->add([ 'user' => auth()->user(), 'roles' => [2] ]);
        $items = User::filter( $req->all() )->where('role_id', 2)->get();
        $rows = $titulos = array();

        foreach ( $items as $item ) {
            $rows [] = [
                'ID usuario'               => $item->id,
                'Nombre completo'          => $item->fullname,
                'Email'                    => $item->email,
                'Teléfono'                 => $item->phone ?? 'N/A',
                'Sexo'                     => $item->genre ?? 'N/A',
                'País'                     => $item->country ?? 'N/A',
                'Num. de propiedades'      => $item->properties->count(),
                'Fecha de nacimiento'      => $item->date_of_birth ? strftime('%d', strtotime($item->date_of_birth)).' de '.strftime('%B', strtotime($item->date_of_birth)). ' del '.strftime('%Y', strtotime($item->date_of_birth)) : 'N/A',
                'SPEI'                     => 'Clabe: '.$item->clabe,
                '¿Recibir correos?'        => $item->receive_emails == 1 ? 'Si' : 'No',
                '¿Recibir notificaciones?' => $item->receive_notifications == 1 ? 'Si' : 'No',
                // 'Status'                   => $item->deleted_at ? 'Baneado' : 'Activo',
                'Fecha de registro'        => strftime('%d', strtotime($item->created_at)).' de '.strftime('%B', strtotime($item->created_at)). ' del '.strftime('%Y', strtotime($item->created_at)). ' a las '.strftime('%H:%M', strtotime($item->created_at)). ' hrs.',
            ];
        }

        // More than 1 row
        if ( count($rows) ) {
            $titulos = array_keys($rows[0]);
        }
        return Excel::download(new CustomersExport($rows, $titulos), 'Listado de clientes '.date('d-m-Y').'.xlsx');
    }
}
