<?php

namespace App\Http\Controllers\Api;

use PDF;
use Hash;

use \App\Models\User;
use \App\Models\Project;
use \App\Models\Property;
use \App\Models\Notification;

use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Sign up a new customer (Login)
     *
     * @param  Request $req
     * @return $customer
     */
    public function signUpCustomer(Request $req)
    {
        // Unique email
        if ( count(User::user_by_email($req->email)) ) { 
            return response(['msg' => 'Este correo ya está registrado, porfavor, elija uno diferente', 'status' => 'error'], 200); 
        }

        // Create customer on stripe
        $res = $this->saveCustomer($req);
        if ( $res['status'] != 'success' ) { return response($res, 500); }

        // Se obtiene la clabe para pagos recurrentes (SPEI)
        $clabe = $res['data']->payment_sources->params['data'][0]['reference'];

        $temporalPassword = Str::random(64);

        $customer = new User;

        $customer->password = bcrypt($temporalPassword);// Contraseña que será modificada
        $customer->change_password = 1;
        $customer->fullname = $req->fullname; 
        $customer->email = $req->email;
        $customer->photo = url('img/users/default.jpg');
        $customer->player_id = $req->player_id ?? null;
        $customer->phone = $req->phone ?? null;
        $customer->country = $req->country ?? null;
        $customer->country_iso = $req->country_iso ?? null;
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
            'data'   => [ 
                'user'  => $customer->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at']),
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Customer login
     *
     * @param  Request  $request
     * @return response json if credentials are correct and status is active (1)
     */
    public function signInCustomer(Request $req)
    {
        $user = User::where('email', $req->email)
        ->whereIn('role_id', [1,2])
        ->with(['role'])
        ->first();
        if ( $user ) {
            // Native login
            if ( Hash::check( $req->password, $user->password ) ) {
                if ( $req->player_id ) { 
                    $user->player_id = $req->player_id;

                    $user->save();
                }

                $token = $user->createToken( Str::random(64) )->plainTextToken;

                return response([
                    'msg'    => 'Inicio de sesión correcto', 
                    'status' => 'success', 
                    'data'   => [ 
                        'user'  => $user->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at']),
                        'token' => $token
                    ]
                ], 200);
            }
            return response(['msg' => 'Credenciales erróneas', 'status' => 'error'], 200);
        }
        return response(['msg' => 'Correo inválido', 'status' => 'error'], 200);
    }

    /**
     * Logout customer
     *
     * @param  Request  $request
     * @return response json
     */
    public function logoutCustomer(Request $req)
    {
        auth()->user()->tokens()->delete();

        return response(['msg' => 'Usuario deslogueado correctamente', 'status' => 'success'], 200);
    }

    /**
     * Actualiza el usuario
     *
     * @param  Request  $request
     * @return response json if credentials are correct and status is active (1)
     */
    public function updateUser(Request $req)
    {
        $item = User::where('id', $req->user()->id)
        ->first();

        if (! $item ) { return response([ 'msg' => "Este correo no pertenece a ninguna cuenta asociada", 'status' => 'error'], 200); }

        $img = $this->uploadFile($req->file('photo'), 'img/users', true);
        // Foto tomada nativamente desde el dispositivo
        $img ? $item->photo = url( $img ) : '';        
        $req->password ? $item->password = bcrypt($req->password) : '';
        $req->password ? $item->change_password = 0 : '';
        $req->phone ? $item->phone = $req->phone : '';
        $req->fullname ? $item->fullname = $req->fullname : '';
        $req->genre ? $item->genre = $req->genre : '';
        $req->country ? $item->country = $req->country : '';
        $req->country_iso ? $item->country_iso = $req->country_iso : '';
        $req->date_of_birth ? $item->date_of_birth = $req->date_of_birth : '';
        $req->player_id ? $item->player_id = $req->player_id : '';
        $req->receive_emails != null ? $item->receive_emails = $req->receive_emails : '';
        $req->receive_notifications != null ? $item->receive_notifications = $req->receive_notifications : '';

        $item->save();
        
        return response(['msg' => 'Usuario actualizado correctamente', 'status' => 'success', 'data' => $item], 200);
    }

    /**
     * Send an email with a new password
     *
     * @return view mail
     */
    public function recoverPassword(Request $req)
    {
        $item = User::where('email', $req->email)
        ->whereIn('role_id', [2])
        ->first();
        
        if (! $item ) { return response([ 'msg' => "Este correo no pertenece a ninguna cuenta asociada", 'status' => 'error'], 200); }
        
        $newPass = Str::random(6);

        $item->password = bcrypt( $newPass );
        $item->change_password = 1;

        
        $item->save();
        
        $params = array();

        $params['view'] = 'mails.reset_password';
        $params['subject'] = 'Cambio de contraseña';
        $params['user'] = $item;
        $params['email'] = $item->email;
        $params['password'] = $newPass;

        $sended = $this->f_mail( $params );

        if ( $sended['status'] == 'success' ) {
            return response(['msg' => 'Correo enviado exitósamente', 'status' => 'success', 'data' => $newPass], 200);
        } else{
            return response(['msg' => 'Ocurrió un error tratando de enviar el correo, trate nuevamente', 'status' => 'error'], 200);
        }
    }

    /**
     * Get info about an user
     *
     * @param  Request  $request
     * @return response json
     */
    public function myProfile(Request $req)
    {
        $projectIds = Property::where('user_id', $req->user()->id)->groupBy('project_id')->pluck('project_id');
        $projects   = Project::whereIn('id', $projectIds)->with(['photos', 'blogs.photos'])->get();
        // dd($projects);
        // dd($req->user());
        $user = User::where('id', $req->user()->id)
        ->with([
            // 'projects',
            'role',
            'properties.installments.status',
            'properties.payments.status',
            'properties.payments.type',
            'payments',
            'installments',
        ])->first();

        if (! $user ) { return response(['msg' => 'Usuario inválido', 'status' => 'error'], 200); }

        $user->projects = $projects;

        return response([
            'msg'    => 'Perfil de usuario encontrado', 
            'status' => 'success', 
            'data'   => $user->setHidden(['role_id', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'])
        ], 200);
    }

    /**
     * Get notifications from user
     *
     * @param  Request  $request
     * @return response json
     */
    public function getNotifications(Request $req)
    {
        $data = Notification::where('user_id', $req->user()->id)->orderBy('id', 'desc')->get();

        if ( count( $data ) ) {
            return response(['msg' => 'Notificaciones enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay notificaciones por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Update status of a notification
     *
     * @param  Request  $request
     * @return response json
     */
    public function updateNotification(Request $req)
    {
        if ( is_array($req->notification_id) ) {
            $notifications = Notification::where('user_id', $req->user()->id)->whereIn('id', $req->notification_id)->update(['seen' => 1]);
        } else {
            $notifications = Notification::where('user_id', $req->user()->id)->where('id', $req->notification_id)->update(['seen' => 1]);
        }

        if ( $notifications ){
            return response(['msg' => 'Notificación actualizada correctamente', 'status' => 'success', 'data' => $notifications], 200);
        } else {
            return response(['msg' => 'No hay notificaciones por actualizar', 'status' => 'error'], 200);
        }
    }
    

    /**
     * Get account status for a property
     *
     * @param  Request  $request
     * @return response json
     */
    public function accountStatus(Request $req)
    {
        $timer = microtime();
        $timer = str_replace([' ','.'], '', $timer);
        $mainPath = 'pdf/'.$timer.'.pdf';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $mainPath = str_replace("/", "\\", $mainPath);
        } 
        $fullPath = $this->createPath($mainPath);
        $property = Property::where('user_id', $req->user()->id)->where('id', $req->property_id)->with(['owner', 'payments.status', 'installments.status'])->first();
        // dd($property->installments->whereIn('installment_status_id', [1,2])->count());
        if( !$property ) { return response(['msg' => 'Seleccione una propiedad válida para continuar', 'status' => 'error'], 404); }

        $counter = $property->pay_in_advance ? 2 : 1;
        $pdf = PDF::loadView('properties.pdf', ['property' => $property, 'counter' => $counter])
        ->setPaper('letter')->setWarnings(false)->save($fullPath);

        return response(['msg' => 'Estado de cuenta generado exitósamente', 'status' => 'success', 'url' => asset($mainPath)], 200);
    }
}
