<?php

namespace App\Http\Controllers\Api;

use Hash;

use \App\Models\Faq;
use \App\Models\Pais;
use \App\Models\User;
use \App\Models\NewData;
use \App\Models\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Get the categories
     *
     * @return \Illuminate\Http\Response
     */
    public function getFaqs()
    {
        $data = Faq::all();

        if ( count( $data ) ) {
            return response(['msg' => 'Preguntas frecuentes enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay preguntas frecuentes por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Get the news data
     *
     * @return \Illuminate\Http\Response
     */
    public function getNews()
    {
        $data = NewData::all();

        if ( count( $data ) ) {
            return response(['msg' => 'Noticias enlistadas a continuación', 'status' => 'success', 'data' => $data], 200);
        }

        return response(['msg' => 'No hay noticias por mostrar', 'status' => 'error'], 200);
    }

    /**
     * Send an email with a new password
     *
     * @return view mail
     */
    public function getLegalInfo(Request $req)
    {
        $faqs = Faq::all();
        $terminos = Configuracion::where('tipo', 'terminos')->first();
        $aviso = Configuracion::where('tipo', 'aviso')->first();

        $data = ['faqs' => $faqs, 'terminos' => $terminos->makeHidden(['id']), 'aviso' => $aviso->makeHidden(['id'])];

        return response(['msg' => 'Información legal mostrada a continuación', 'status' => 'success', 'data' => $data ], 200);
    }

    /**
     * Shows terms and conditions page
     *
     * @return view mail
     */
    public function showTermsAndConditions(Request $req)
    {
        $title = 'Términos y condiciones';
        $info = Configuracion::where('tipo', 'terminos')->first();

        return view('layouts.legal_info', compact('info', 'title'));
    }

    /**
     * Shows notice privacy page
     *
     * @return view mail
     */
    public function showNoticePrivacy(Request $req)
    {
        $title = 'Aviso de privacidad';
        $info = Configuracion::where('tipo', 'aviso')->first();

        return view('layouts.legal_info', compact('info', 'title'));
    }

    /**
     * Guardar tarjeta
     *
     * @param  Request  $request
     * @return response json if credentials are correct and status is active (1)
     */
    public function processPayment(Request $req)
    {
        $user = User::where('id', $req->user_id)
        ->where('status', 1)
        ->where('role_id', 2)
        ->first();

        if (! $user ) { return response(['msg' => 'Usuario inválido', 'status' => 'error'], 404); }

        $res = $this->makePayment($req, $user, $req->token, 1000);

        if ( $res['status'] != 'success' ) { return response($res, 500); }#Customer wasn't created on openpay

        return response($res, 200);
    }

    /**
     * Check information of events related to subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function webhookMain(Request $req)
    {
        $this->saveLog($req);

        $body = @file_get_contents('php://input');
        $event = json_decode($body);

        #Try to get the content of the event
        $content = @$event->data->object;

        #Subscription payment succeeded
        // if ( $event->type == 'invoice.payment_succeeded' ) {
        //     if ( $content && $content->customer ) {
        //         $user = User::where('customer_id', $content->customer)->first();
        //         #Found user
        //         if ( $user ) {
        //             $period = @$content->lines->data[0]->period;

        //             if ( $period ) {
        //                 $end_date = strftime("%Y-%m-%d", $period->end);

        //                 $user->renovation_date = $end_date;
                        
        //                 $user->save();
        //             }
        //         }
        //     }
        // }

        // #Subscription payment failed
        // if ( $event->type == 'invoice.payment_failed' ) {
        //     if ( $content && $content->customer ) {
        //         #Let's remove subscription created recently, we need to put the subscription id, so we can remove it from stripe too!
        //         $user = User::where('customer_id', $content->customer)->first();
        //         #Found user
        //         if ( $user ) {
        //             $req->merge(['user_id' => $user->id]);

        //             $this->cancelSubscription($req);
        //         }
        //     }
        // }

        return response(['msg' => 'Webhook procesado correctamente', 'status' => 'success'], 200); 
    }

    /**
     * Webhook that saves spei recurrent data
     * 
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function webhookSpeiRecurrent(Request $req)
    {
        $this->saveLog($req);

        $body = @file_get_contents('php://input');
        $event = json_decode($body);

        // dd($event);
        $clabe = null;
        $object = @$event->data->object;
        $paymentMethod = @$object->charges->data[0]->payment_method;// SPEI
        $amount = @$event->data->object->amount;
        $type = $event->type;

        if( $type == 'order.paid' ) {
            if ( $object ) {
                if ( $paymentMethod ) {
                    if ( $paymentMethod->type = 'spei' ) {// Revisar si SPEI recurrent es diferente a SPEI
                        $clabe = $paymentMethod->clabe;

                        $user = User::where('clabe', $clabe)->first();

                        if ( $user ) {
                            $payment = $this->createNewCustomerPayment($user, $amount / 100, 3);
                        }
                    }
                }
            }
        };

        return response(['msg' => 'Webhook spei recurrente procesado correctamente', 'status' => 'success'], 200); 
    }
}
