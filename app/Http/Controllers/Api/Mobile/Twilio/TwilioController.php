<?php

namespace App\Http\Controllers\API\Mobile\Twilio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TwilioController extends Controller
{
    public function checUserSms(Request $request){
        try{
            $usuario = $request->usuario;
            $nip = $request->nip;
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://mwp2.siiau.udg.mx:8080/WSEscolarCentroWeb-war/webresources/WSEscolarCentroWeb/validaUsuario',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "codigo": "'.$usuario.'",
                "nip": "'.$nip.'"
                }',
              CURLOPT_HTTPHEADER => array(
                'Username: APPBUTACA',
                'Password: PS23BRIDSERV',
                'Content-type: application/json'
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            //return $this->genericRespose(json_decode($response), "", 200);
            return json_decode($response);
        }catch (\Throwable $th) {
            throw $th;
        }
    }
    public function sendSms(Request $request)
    {
        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'https://butaca.udg.mx/api/send/sms/', [
                'form_params' => [
                    'id_usuario'  => $request->id_usuario,
                    'telefono'=>  $request->telefono,
                ]
            ]);
            return response()->json([
                'message' => 'true',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
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
            $response = $client->request('PUT', 'https://butaca.udg.mx/api/resend/code/'.$user_id, [
                'form_params' => [
                    'telefono'  => $request->telefono,
                ]
            ]);
            return response()->json([
                'success' => 'true',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
