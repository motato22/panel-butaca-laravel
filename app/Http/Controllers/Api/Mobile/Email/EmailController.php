<?php

namespace App\Http\Controllers\Api\Mobile\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    // public function checUserSms(Request $request){
    //     try{
    //         $usuario = $request->usuario;
    //         $nip = $request->nip;
    //         $curl = curl_init();
    //         curl_setopt_array($curl, array(
    //           CURLOPT_URL => 'http://mwp2.siiau.udg.mx:8080/WSEscolarCentroWeb-war/webresources/WSEscolarCentroWeb/validaUsuario',
    //           CURLOPT_RETURNTRANSFER => true,
    //           CURLOPT_ENCODING => '',
    //           CURLOPT_MAXREDIRS => 10,
    //           CURLOPT_TIMEOUT => 0,
    //           CURLOPT_FOLLOWLOCATION => true,
    //           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //           CURLOPT_CUSTOMREQUEST => 'POST',
    //           CURLOPT_POSTFIELDS =>'{
    //             "codigo": "'.$usuario.'",
    //             "nip": "'.$nip.'"
    //             }',
    //           CURLOPT_HTTPHEADER => array(
    //             'Username: APPBUTACA',
    //             'Password: PS23BRIDSERV',
    //             'Content-type: application/json'
    //           ),
    //         ));
    //         $response = curl_exec($curl);
    //         curl_close($curl);
    //         //return $this->genericRespose(json_decode($response), "", 200);
    //         return json_decode($response);
    //     }catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }
    // public function sendSms(Request $request)
    // {
    //     try {

    //         $client = new \GuzzleHttp\Client();
    //         $response = $client->request('POST', 'https://butaca.udg.mx/api/send/sms/', [
    //             'form_params' => [
    //                 'id_usuario'  => $request->id_usuario,
    //                 'telefono'=>  $request->telefono,
    //             ]
    //         ]);
    //         return response()->json([
    //             'message' => 'true',
    //         ]);
    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }
    public function validateCode(Request $request, $id_usuario)
    {

        $existencia = DB::table('sms_twilio')->where( 'codigo_sms', request()->get('sms_code'))
        ->where('id_usuario', $id_usuario)->exists();
        try {

            if ($existencia != true) {
                return response()->json([
                    'Validate' => 'false',
                ]);
            }else {

                $user = User::find($id_usuario);
                $user->cuenta_verificada = 1;
                $user->save();

                return response()->json([
                    'Validate' => 'true',
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function resendCode(Request $request,$user_id)
    {
        try {

            $client = new \GuzzleHttp\Client();
            $to = $request->correo;
            $response = $client->request('PUT', 'https://app.butaca.udg.mx/api/resend/email/code/'.$user_id, [
                'form_params' => [
                    'correo'  => $to,
                ]
            ]);
            // Decodifica la respuesta JSON a un array asociativo
            $apiResponseBody = json_decode((string) $response->getBody(), true);
            // Accede al mensaje dentro de la respuesta
            $apiMessage = isset($apiResponseBody['message']) ? $apiResponseBody['message'] : 'No message in response';

            Mail::send([], [], function ($message) use ($apiMessage, $to) {
                $message->from('utaca-no-reply@correo.udg.mx', 'Butaca No Reply')
                        ->to($to) // Usar la variable $to aquí
                        ->subject('Butaca UDG: Codigo de verificacion')
                        ->setBody($apiMessage, 'text/html'); // Establecer el cuerpo del mensaje como HTML
            });

            return response()->json([
                'success' => 'true',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
