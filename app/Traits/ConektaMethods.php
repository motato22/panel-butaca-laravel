<?php

namespace App\Traits;

use Mail;
use Image;

use \App\Models\Log;
use \App\Models\User;
use \App\Models\Card;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait ConektaMethods
{
    function __construct() {
        \Conekta\Conekta::setApiKey(env("CONEKTA_API_PRIVATE"));
        \Conekta\Conekta::setApiVersion("2.0.0");
    }

	/**
     * Register a customer on stripe
     *
     * @return \Illuminate\Http\Response
     */
    public function saveCustomer(Request $req)
    {
        try {
            $customer = \Conekta\Customer::create([
                'email' => $req->email,
                'name'  => $req->fullname,
                'payment_sources' => [// All users may have spei recurrent and unique data
                    [
                        'type' => "spei_recurrent"
                    ]
                ]
            ]);

            return ['msg' => 'Cliente registado correctamente en conekta', 'status' => 'success', 'data' => $customer];
        } catch (\Conekta\ProcessingError $e) {
            return ['msg' => 'Error al procesar el cliente: '.$e->getMessage(), 'status' => 'error'];
        } catch (\Conekta\ParameterValidationError $e) {
            return ['msg' => 'Error al procesar el cliente, parámetro erróneo: '.$e->getMessage(), 'status' => 'error'];
        }
    }

    /**
     * Register a payment on conekta
     *
     * @return \Illuminate\Http\Response
     * @param Request $req
     * @param User $user (nullable)
     * @param String $cardtoken: El token de la tarjeta de stripe.js (tok_1JY1yhFCiAPeuM1vJY60BoVg)
     */
    public function makePayment(Request $req = null, User $user, Card $card, $payment, $currency = 'MXN')
    {
        try {
            $customer = \Conekta\Customer::find($user->payment_token);

            if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos en línea', 'status' => 'error']; }
            
            $order = \Conekta\Order::create([
                'currency' => $currency,
                'customer_info' => [
                    'customer_id' => $user->payment_token,
                    // 'antifraud_info' => [
                    //     'paid_transactions' => 4
                    // ]
                ],
                'line_items' => [
                    [
                        'name' => 'Pago de propiedad',
                        'unit_price' => $payment,
                        'quantity' => 1,
                        // 'antifraud_info' => [
                        //     'trip_id'        => '12345',
                        //     'driver_id'      => 'driv_1231',
                        //     'ticket_class'   => 'economic',
                        //     'pickup_latlon'  => '23.4323456,-123.1234567',
                        //     'dropoff_latlon' => '23.4323456,-123.1234567'
                        // ]
                    ]
                ],
                'charges' => [
                    [
                        'payment_method' => [
                            'type' => 'card',
                            // 'token_id' => 'tok_test_visa_4242'
                            "payment_source_id" => $card->token
                        ] //payment_method - use customer's default - a card
                    ]
                ]
            ]);
            // $order = $this->stripe->charges->create([
            //     // "customer"    => $user->payment_token,
            //     "amount"      => $totalCost,
            //     'currency'    => $currency,
            //     "source"      => $cardToken,
            //     "description" => "Charge for ".$user->name,
            // ]);

            return ['msg' => 'Cargo procesado correctamente', 'status' => 'success', 'data' => $order];
        } catch (\Conekta\ProcessingError $e) {
            return ['msg' => 'Error al procesar el cargo: '.$e->getMessage(), 'status' => 'error'];
        } catch (\Conekta\ParameterValidationError $e) {
            return ['msg' => 'Error al procesar el cargo, parámetro erróneo: '.$e->getMessage(), 'status' => 'error'];
        }  catch (\Exception $e) {
            return ['msg' => 'Algo salió mal: '.$e->getMessage(), 'status' => 'error'];
        }
        // } catch (\Conekta\ProcessingError $error) {
        //     return ['msg' => $error->getMesage(), 'status' => 'error'];
        // } catch (\Conekta\ParameterValidationError $error) {
        //     return ['msg' => 'Error en los parámetros recibidos, revisar los logs', 'status' => 'error'];
        // } catch (\Conekta\Handler $error){
        //     return ['msg' => $error->getMesage(), 'status' => 'error'];
        // } catch (\Exception $e) {
        //     error_log('Error al registrar el usuario en conekta: '. $e->getMessage(), 0);
        //     return ['msg' => 'Error al registrar el usuario, trate usando otros datos', 'status' => 'error'];
        // }
    }

    /**
     * Register a card for a customer on conekta
     *
     * @return \Illuminate\Http\Response
     * @param Request $req
     * @param User $user (nullable)
     * @param String $cardtoken: El token de la tarjeta de stripe.js (tok_1JY1yhFCiAPeuM1vJY60BoVg)
     */
    public function saveCard(Request $req, User $user)
    {
        try {
            $customer = \Conekta\Customer::find($user->payment_token);

            if (! $customer ) { return ['msg' => 'El cliente no cuenta con acceso a pagos.', 'status' => 'error']; }
            
            $source = $customer->createPaymentSource([
                'token_id' => $req->token,
                // 'token_id' => 'tok_test_visa_4242',
                'type'     => 'card'
            ]);
            
            return ['msg' => 'Tarjeta registada correctamente en conekta', 'status' => 'success', 'data' => $source];
        } catch (\Conekta\ProcessingError $e) {
            return ['msg' => 'Error al procesar la tarjeta: '.$e->getMessage(), 'status' => 'error'];
        } catch (\Conekta\ParameterValidationError $e) {
            return ['msg' => 'Error al procesar la tarjeta, parámetro erróneo: '.$e->getMessage(), 'status' => 'error'];
        } catch (\Conekta\Handler $error){
            return ['msg' => $error->getMessage(), 'status' => 'error'];
        } catch (\Exception $e) {
            return ['msg' => 'Error al registrar la tarjeta, trate usando otros datos:'.$e->getMessage(), 'status' => 'error'];
        }
    }

    /**
     * Delete a card for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteCard(Request $req, Card $card)
    {
        $item = null;

        $user = User::find($card->user_id);
        if (! $user ) { return ['msg' => 'ID de usuario no encontrado', 'status' => 'error']; }

        $customer = $customer = \Conekta\Customer::find($user->payment_token);

        if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos en línea', 'status' => 'error']; }

        try {
            foreach( $customer->payment_sources as $source ) {
                if ( $source->id == $card->token ) {
                    $item = $source;

                    $source->delete();
                }
            }
            
            return ['msg' => 'Tarjeta eliminada correctamente', 'status' => 'success', 'data' => $item];
        } catch (\Conekta\ProcessingError $error) {
            return ['msg' => $error->getMesage(), 'status' => 'error'];
        } catch (\Conekta\ParameterValidationError $error) {
            return ['msg' => 'Error en los parámetros recibidos, revisar los logs', 'status' => 'error'];
        } catch (\Conekta\Handler $error){
            return ['msg' => $error->getMesage(), 'status' => 'error'];
        } catch (\Exception $e) {
            // error_log('Error al eliminar la tarjeta en conekta: '. $e->getMessage(), 0);
            return ['msg' => 'Error al eliminar la tarjeta, trate usando otros datos: '.$e->getMessage(), 'status' => 'error'];
        }
    }

    /**
     * Create a charge for a customer on openpay
     *
     * @return \Illuminate\Http\Response
     */
    public function payConektaSpei(User $user, $payment, Request $req = null)
    {
        $items = [];

        $customer = \Conekta\Customer::find($user->payment_token);

        if (! $customer ) { return ['msg' => 'El cliente no se encuentra registrado para pagos en líneo', 'status' => 'error']; }

        $thirty_days_from_now = (new \DateTime())->add(new \DateInterval('P30D'))->getTimestamp(); 

        try{
            $orderData = [
                'line_items' => [
                    [
                        'name' => 'Pago de propiedad',
                        'unit_price' => $payment,
                        'quantity' => 1,
                        // 'antifraud_info' => [
                        //     'trip_id'        => '12345',
                        //     'driver_id'      => 'driv_1231',
                        //     'ticket_class'   => 'economic',
                        //     'pickup_latlon'  => '23.4323456,-123.1234567',
                        //     'dropoff_latlon' => '23.4323456,-123.1234567'
                        // ]
                    ]
                ],
                "currency" => "MXN",
                "customer_info" => [
                    "customer_id" => $user->payment_token
                ],
                "charges" => [
                    [
                        "payment_method" => [
                            "type" => "spei",
                            // "type" => "spei_recurrent",
                            "expires_at" => $thirty_days_from_now
                        ]
                    ]
                ]
            ];

            $order = \Conekta\Order::create($orderData);
            
            return ['msg' => 'Pedido por SPEI generado correctamente', 'status' => 'success', 'data' => $order];

        } catch ( \Conekta\ProcessingError $error ) {
            return ['msg' => $error->getMessage(), 'status' => 'error'];
        } catch ( \Conekta\ParameterValidationError $error ) {
            return ['msg' => $error->message, 'status' => 'error'];
        } catch ( \Conekta\Handler $error ) {
            return ['msg' => $error->getMessage(), 'status' => 'error'];
        } catch ( \Exception $e ) {
            error_log('Error al crear la órden de pago: '. $e->getMessage(), 0);
            return ['msg' => 'Error al crear la órden de pago de SPEI, verifique su método de pago', 'status' => 'error'];
        }
    }
}
