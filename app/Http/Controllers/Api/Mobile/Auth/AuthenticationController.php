<?php

namespace App\Http\Controllers\Api\Mobile\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \stdClass;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthenticationController extends Controller
{
    private $maxLifetime = 3600;
    public function authenticate3(Request $request)
    {
        $credentials = $request->only('username', 'password');
        //valid credential
        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required'
        ]);
        $user  = new \stdClass();

        $user_profile = DB::select('select * from usuarios where username = :username', ['username' => $credentials['username']]);

        $user->nombre = $user_profile[0]->nombre;
        $user->username = $user_profile[0]->username;
        $user->correo = $user_profile[0]->correo;
        $user->provider_id = $user_profile[0]->provider_id;
        $user->provider_uid = $user_profile[0]->provider_uid;
        $user->role = $user_profile[0]->role;
        $user->activo = $user_profile[0]->activo;
        $user->fecha_nacimiento = $user_profile[0]->fecha_nacimiento;
        $user->genero = $user_profile[0]->genero;
        $user->telefono = $user_profile[0]->telefono;
        $user->cuenta_verificada = $user_profile[0]->cuenta_verificada;
        $user->foto = $user_profile[0]->foto;
        $user->foto_url = $user_profile[0]->foto_url;
        $user->fcm_token = $user_profile[0]->fcm_token;
        $user->no_push = $user_profile[0]->no_push;
        $user->estrato_id = $user_profile[0]->estrato_id;
        $user->codigo_ude_g = $user_profile[0]->codigo_ude_g;
        $user->nip = $user_profile[0]->nip;
        $user->intereses = $user_profile[0]->intereses;
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token', 'user'));
    }


    public function authenticate(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            // Validación de credenciales
            $validator = Validator::make($credentials, [
                'username' => 'required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            $user_profile = DB::select('select * from usuarios where username = :username', ['username' => $credentials['username']]);

            if (empty($user_profile)) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user = (object) [
                'id' => $user_profile[0]->id,
                'nombre' => $user_profile[0]->nombre,
                'username' => $user_profile[0]->username,
                'correo' => $user_profile[0]->correo,
                'provider_id' => $user_profile[0]->provider_id,
                'provider_uid' => $user_profile[0]->provider_uid,
                'role' => $user_profile[0]->role,
                'activo' => $user_profile[0]->activo,
                'fecha_nacimiento' => $user_profile[0]->fecha_nacimiento,
                'genero' => $user_profile[0]->genero,
                'telefono' => $user_profile[0]->telefono,
                'cuenta_verificada' => $user_profile[0]->cuenta_verificada,
                'foto' => $user_profile[0]->foto,
                'foto_url' => $user_profile[0]->foto_url,
                'fcm_token' => $user_profile[0]->fcm_token,
                'no_push' => $user_profile[0]->no_push,
                'estrato_id' => $user_profile[0]->estrato_id,
                'codigo_ude_g' => $user_profile[0]->codigo_ude_g,
                'intereses' => $user_profile[0]->intereses,
            ];

            $this->validateLogin($request);

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return response()->json([
                'token' => $request->user()->createToken('butacaudg')->plainTextToken,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in authenticate: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    public function validateLogin(Request $request)
    {
        return $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

    public function registerApi(Request $request)
    {
        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://app.butaca.udg.mx/api/auth/user/create', [
                'form_params' => [
                    'nombre'  => $request->nombre,
                    'email' =>  $request->email,
                    'username' =>  $request->username,
                    'password' =>  $request->password,
                    'genero' =>  $request->genero,
                    'telefono' =>  $request->telefono
                ]
            ]);
            return response()->json([
                'success' => 'true',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function register(Request $request)
    {
        $credentials = $request->only(
            'nombre',
            'correo',
            'username',
            'password',
            'telefono'
        );

        $existencia = DB::table('usuarios')->where('username', $request->username)
            ->where('telefono', $request->telefono)
            ->where('correo', $request->correo)->exists();

        //valid credential
        $validator = Validator::make($credentials, [
            'nombre' => 'required',
            'correo' => 'required|unique:usuarios',
            'username' => 'required',
            'password' => 'required',
            'telefono' => 'required',
            'genero' => 'required',
        ]);

        try {
            if ($existencia != true) {

                $obj  = new \stdClass();
                $user = new User();
                $user->nombre = $request->nombre;
                $user->correo = $request->correo;
                $user->estado = $request->estado;
                $user->ciudad = $request->ciudad;
                $user->username = $request->username;
                $user->password = Hash::make($request->password);
                $user->telefono = $request->telefono;
                $user->role = 'ROLE_USER';
                $user->activo = 1;
                $user->cuenta_verificada = $request->cuenta_verificada;
                $user->codigo_ude_g = $request->codigo;
                $user->genero = $request->genero;
                $user->provider_id = $request->provider_id;
                $user->provider_uid = $request->provider_uid;
                //$user->nip = $request->nip; 
                $user->save();
                $obj = $user;
                return $obj;
            } else {
                return response()->json(['message' => 'username o telefono ya existe']);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return response()->json(['message' => 'Email Duplicate Entry']);
            }
        }
    }


    public function me()
    {
        auth()->user()->tokens()->delete();
        return response()->json(auth()->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    public function refresh(Request $request)
    {
        //dd($request);
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['token' => $user->createToken($user->name)->plainTextToken]);
    }


    protected function perfil(Request $request, $id)
    {
        $get_perfil  = new \stdClass();
        $user = DB::select('select * from usuarios where id = :id', ['id' => $id]);


        $intereses = DB::table('usuario_genero')->where('usuario_id', '=', $id)->get();
        // $datos = json_decode($intereses);

        // foreach ($datos as $key => $dato) {
        //     $eventos = DB::table('generos')->where('id','=',$dato->genero_id)->get();
        //     $dato->eventos = $eventos;
        // }

        $get_perfil->nombre = $user[0]->nombre;
        $get_perfil->username = $user[0]->username;
        $get_perfil->correo = $user[0]->correo;
        $get_perfil->provider_id = $user[0]->provider_id;
        $get_perfil->provider_uid = $user[0]->provider_uid;
        $get_perfil->role = $user[0]->role;
        $get_perfil->activo = $user[0]->activo;
        $get_perfil->fecha_nacimiento = $user[0]->fecha_nacimiento;
        $get_perfil->genero = $user[0]->genero;
        $get_perfil->telefono = $user[0]->telefono;
        $get_perfil->cuenta_verificada = $user[0]->cuenta_verificada;
        $get_perfil->foto = $user[0]->foto;
        $get_perfil->foto_url = $user[0]->foto_url;
        $get_perfil->fcm_token = $user[0]->fcm_token;
        $get_perfil->no_push = $user[0]->no_push;
        $get_perfil->estrato_id = $user[0]->estrato_id;
        $get_perfil->codigo_ude_g = $user[0]->codigo_ude_g;
        //$get_perfil->nip = $user[0]->nip;
        $get_perfil->intereses = $user[0]->intereses;
        $get_perfil->segmento = $user[0]->segmento;

        return response()->json([
            'user' => $get_perfil,
        ]);
    }

    public function editPerfil(Request $request, $id)
    {
        //dd($request);
        try {
            $edit = User::find($id);
            if (isset($request->password)) {
                $edit->password = Hash::make($request->password);
                $edit->cuenta_verificada = 0;
            }
            $edit->nombre = $request->nombre ?? $edit->nombre;
            // $edit->username = $request->username ?? $edit->username;
            $edit->correo = $request->correo ?? $edit->correo;
            $edit->provider_id = $request->provider_id ?? $edit->provider_id;
            $edit->provider_uid = $request->provider_uid ?? $edit->provider_uid;
            $edit->fecha_nacimiento = $request->fecha_nacimiento ?? $edit->fecha_nacimiento;
            $edit->genero = $request->genero ?? $edit->genero;
            $edit->telefono = $request->telefono ?? $edit->telefono;
            $edit->foto = $request->foto ?? $edit->foto;
            $edit->foto_url = $request->foto_url ?? $edit->foto_url;
            $edit->fcm_token = $request->fcm_token ?? $edit->fcm_token;
            $edit->no_push = $request->no_push ?? $edit->no_push;
            $edit->estrato_id = $request->estrato_id ?? $edit->estrato_id;
            $edit->codigo_ude_g = $request->codigo_ude_g ?? $edit->codigo_ude_g;
            //$edit->nip = $request->nip ?? $edit->nip;
            $edit->save();
            return response()->json(['message' => $edit]);
        } catch (\Throwable $th) {
            return response()->json([$th]);
        }
    }

    public function updateFotoPerfil(Request $request, $id)
    {
        try {
            $request->validate([
                'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($request->file('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();
                $destino = 'imagenes_subidas/';
                $path = $imagen->storeAs($destino, $nombreImagen, 'public');
                $pathImagen = '/storage/imagenes_subidas/' . $nombreImagen;
                $edit = User::find($id);
                $edit->foto = $pathImagen ?? $edit->foto;
                $edit->foto_url = $pathImagen ?? $edit->foto_url;
                $edit->save();
                return response()->json(['message' => $edit]);
            }
            return response()->json(['message' => 'Error al subir la imagen']);
        } catch (\Throwable $th) {
            return response()->json([$th]);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            // Validar que se envíe el identificador (username o correo)
            $request->validate([
                'identifier' => 'required'
            ]);

            $identifier = $request->identifier;

            // Determinar si el identificador es un correo electrónico o un username
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                // Es un correo, buscar por columna 'correo'
                $user = DB::table('usuarios')->where('correo', $identifier)->first();
            } else {
                // No es un correo, se asume que es un username
                $user = DB::table('usuarios')->where('username', $identifier)->first();
            }

            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            // Generar una nueva contraseña aleatoria (por ejemplo, de 12 caracteres)
            $newPassword = \Illuminate\Support\Str::random(12);

            // Actualizar la contraseña del usuario en la base de datos utilizando un hash
            DB::table('usuarios')->where('id', $user->id)->update([
                'password' => Hash::make($newPassword)
            ]);

            // Construir el contenido HTML del correo con la nueva contraseña
            $htmlContent = "
                <p>Estimado Usuario,</p>
                <p>Has solicitado reiniciar su contraseña de acceso a Butaca UDG.</p>
                <p>Se le ha asignado una nueva contraseña: <strong>{$newPassword}</strong></p>
                <p>Acceda a la app con esa contraseña y le sugerimos cambiarla desde Menu/Perfil una vez que ingrese.</p>
                <p>Atentamente,<br>Butaca UDG.</p>
            ";

            $to = $user->correo;

            // Enviar el correo utilizando Mail::send
            Mail::send([], [], function ($message) use ($htmlContent, $to) {
                $message->from('sendmailqa@bridgestudio.mx', 'Butaca No Reply')
                        ->to($to)
                        ->subject('Butaca UDG: Reinicio de Contraseña')
                        ->html($htmlContent);
            });
            

            return response()->json([
                'message' => 'Mensaje enviado a ' . $to,
            ]);
        } catch (\Throwable $th) {
            \Log::error('Error en resetPassword: ' . $th->getMessage());
            return response()->json([
                'message' => 'Error interno en el servidor'
            ], 500);
        }
    }


    public function existsEmail(Request $request)
    {
        $credentials = $request->correo;
        // dd($credentials);
        $get_perfil  = new \stdClass();
        try {
            $info = DB::table('usuarios')->where('correo', '=', $credentials)->get();
            $get_perfil->id = $info[0]->id;
            $get_perfil->nombre = $info[0]->nombre;
            $get_perfil->username = $info[0]->username;
            $get_perfil->correo = $info[0]->correo;
            $get_perfil->provider_id = $info[0]->provider_id;
            $get_perfil->provider_uid = $info[0]->provider_uid;
            $get_perfil->role = $info[0]->role;
            $get_perfil->activo = $info[0]->activo;
            $get_perfil->fecha_nacimiento = $info[0]->fecha_nacimiento;
            $get_perfil->genero = $info[0]->genero;
            $get_perfil->telefono = $info[0]->telefono;
            $get_perfil->cuenta_verificada = $info[0]->cuenta_verificada;
            $get_perfil->foto = $info[0]->foto;
            $get_perfil->foto_url = $info[0]->foto_url;
            $get_perfil->fcm_token = $info[0]->fcm_token;
            $get_perfil->no_push = $info[0]->no_push;
            $get_perfil->estrato_id = $info[0]->estrato_id;
            $get_perfil->codigo_ude_g = $info[0]->codigo_ude_g;
            //$get_perfil->nip = $info[0]->nip;
        } catch (\Throwable $th) {
            return \Response::json(['exists' => 'false']);
        }
        return response()->json([
            'user' => $get_perfil,
        ]);
    }

    public function existsUsername(Request $request)
    {
        $credentials = $request->username;
        // dd($credentials);
        $get_perfil  = new \stdClass();
        try {
            $info = DB::table('usuarios')->where('username', '=', $credentials)->get();
            $get_perfil->id = $info[0]->id;
            $get_perfil->nombre = $info[0]->nombre;
            $get_perfil->username = $info[0]->username;
            $get_perfil->correo = $info[0]->correo;
            $get_perfil->provider_id = $info[0]->provider_id;
            $get_perfil->provider_uid = $info[0]->provider_uid;
            $get_perfil->role = $info[0]->role;
            $get_perfil->activo = $info[0]->activo;
            $get_perfil->fecha_nacimiento = $info[0]->fecha_nacimiento;
            $get_perfil->genero = $info[0]->genero;
            $get_perfil->telefono = $info[0]->telefono;
            $get_perfil->cuenta_verificada = $info[0]->cuenta_verificada;
            $get_perfil->foto = $info[0]->foto;
            $get_perfil->foto_url = $info[0]->foto_url;
            $get_perfil->fcm_token = $info[0]->fcm_token;
            $get_perfil->no_push = $info[0]->no_push;
            $get_perfil->estrato_id = $info[0]->estrato_id;
            $get_perfil->codigo_ude_g = $info[0]->codigo_ude_g;
            //$get_perfil->nip = $info[0]->nip;
        } catch (\Throwable $th) {
            return \Response::json(['exists' => 'false']);
        }
        return response()->json([
            'user' => $get_perfil,
        ]);
    }


    public function checkUdegUser(Request $request)
    {
        $credentials = $request->only('codigo', 'nip');
        //dd($credentials);
        $get_perfil  = new \stdClass();
        try {
            $info = DB::table('usuarios')->where('codigo_ude_g', '=', $request->codigo)
                ->where('nip', '=', $request->nip)->get();
            $usuario = $request->codigo;
            $nip = $request->nip;
            if (count($info) < 1) {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://ms.mw.siiau.udg.mx/WSEscolarCentroWeb-war/webresources/WSEscolarCentroWeb/validaUsuario',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
                        "codigo": "' . $usuario . '",
                        "nip": "' . $nip . '"
                        }',
                    CURLOPT_HTTPHEADER => array(
                        'Username: APPBUTACA',
                        'Password: WSBUTACA23',
                        'Content-type: application/json'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $info = json_decode($response);
                if (isset($info->respuesta)) {
                    return \Response::json(['exito' => 'false', 'msg' => 'El usuario no existe', 'status' => '500'], 500);
                }
                $get_perfil->id = null;
                $get_perfil->nombre = $info->nombre;
                $get_perfil->username = null;
                $get_perfil->correo = null;
                $get_perfil->provider_id = null;
                $get_perfil->provider_uid = null;
                $get_perfil->role = null;
                $get_perfil->activo = null;
                $get_perfil->fecha_nacimiento = null;
                $get_perfil->genero = null;
                $get_perfil->telefono = null;
                $get_perfil->cuenta_verificada = null;
                $get_perfil->foto = null;
                $get_perfil->foto_url = null;
                $get_perfil->fcm_token = null;
                $get_perfil->no_push = null;
                $get_perfil->estrato_id = null;
                $get_perfil->codigo_ude_g = $usuario;
                //$get_perfil->nip = $nip;
            } else {
                $get_perfil->id = $info[0]->id;
                $get_perfil->nombre = $info[0]->nombre;
                $get_perfil->username = $info[0]->username;
                $get_perfil->correo = $info[0]->correo;
                $get_perfil->provider_id = $info[0]->provider_id;
                $get_perfil->provider_uid = $info[0]->provider_uid;
                $get_perfil->role = $info[0]->role;
                $get_perfil->activo = $info[0]->activo;
                $get_perfil->fecha_nacimiento = $info[0]->fecha_nacimiento;
                $get_perfil->genero = $info[0]->genero;
                $get_perfil->telefono = $info[0]->telefono;
                $get_perfil->cuenta_verificada = $info[0]->cuenta_verificada;
                $get_perfil->foto = $info[0]->foto;
                $get_perfil->foto_url = $info[0]->foto_url;
                $get_perfil->fcm_token = $info[0]->fcm_token;
                $get_perfil->no_push = $info[0]->no_push;
                $get_perfil->estrato_id = $info[0]->estrato_id;
                $get_perfil->codigo_ude_g = $info[0]->codigo_ude_g;
                //$get_perfil->nip = $info[0]->nip;
            }
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => 'El usuario no existe', 'status' => '500'], 500);
        }
        return response()->json([
            'user' => $get_perfil,
        ]);
    }

    public function checkUdegUserExternal(Request $request)
    {
        $usuario = $request->codigo;
        $nip = $request->nip;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ms.mw.siiau.udg.mx/WSEscolarCentroWeb-war/webresources/WSEscolarCentroWeb/validaUsuario',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                        "codigo": "' . $usuario . '",
                        "nip": "' . $nip . '"
                        }',
            CURLOPT_HTTPHEADER => array(
                'Username: APPBUTACA',
                'Password: WSBUTACA23',
                'Content-type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $info = json_decode($response);
        return response()->json($info);
    }

    public function checkStatusUdegUser(Request $request)
    {
        $encryptedId = $request->codigo;
        $usuario = $this->decodeUniqueId($encryptedId, $this->maxLifetime);

        if ($usuario === null) {
            return \Response::json(['exito' => 'false', 'msg' => 'Error de datos', 'status' => '403'], 500);
        }
        // $usuario = $request->codigo;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ms.mw.siiau.udg.mx/WSEscolarCentroWeb-war/webresources/WSEscolarCentroWeb/tipoUsuario',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "codigo": "' . $usuario . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'Username: APPBUTACA',
                'Password: WSBUTACA23',
                'Content-type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $info = json_decode($response);
        if (isset($info->respuesta)) {
            return \Response::json(['exito' => 'false', 'msg' => 'El usuario no existe', 'status' => '500'], 500);
        } else {
            return response()->json($info);
        }
    }

    public function registrarFCMToken(Request $request, $token)
    {

        //dd($request);
        $user_id = $request->user_id;

        try {
            $user = User::find($user_id);
            $user->fcm_token = $token;
            $user->update();
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json([
            'message' => $user,
        ]);
    }

    public function test(Request $request)
    {

        dd($request);
    }

    // Función para decodificar el ID único
    private function decodeUniqueId($uniqid, $maxLifetime)
    {
        $decoded = $this->doubleDecodeBase64($uniqid);
        if ($decoded === null) {
            return null;
        }
        $parts = explode('-', $decoded);
        if (count($parts) !== 2) {
            return null;
        }
        $alumnoId = $parts[0];
        $timestamp = (int)$parts[1];
        // Verifica si el tiempo ha expirado
        if (time() > $timestamp + $maxLifetime) {
            return null;
        }
        return $alumnoId;
    }

    // Función para decodificar doble Base64
    private function doubleDecodeBase64($input)
    {
        try {
            $decoded = base64_decode($input, true);
            if ($decoded === false) {
                return null;
            }
            $decoded = base64_decode($decoded, true);
            if ($decoded === false) {
                return null;
            }
            return $decoded;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
