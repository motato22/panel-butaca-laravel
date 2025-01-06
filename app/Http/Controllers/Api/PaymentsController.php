<?php

namespace App\Http\Controllers\Api;

use \App\Models\Card;
use \App\Models\User;
use \App\Models\Project;
use \App\Models\Payment;
use \App\Models\Property;
use \App\Models\PaymentType;
use \App\Models\Installment;
use \App\Models\PaymentStatus;

use App\Http\Controllers\Controller;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Get filters for payments view on app (only admin)
     *
     * @param  Request  $request
     */
    public function getFilters(Request $req)
    {
        $filters = [
            'user' => $req->user(), 
            'limit' => 100, 
        ];

        $earningProject = [];
        $items = Payment::filter( $filters )->orderBy('id', 'desc')->get();
        $projects = Project::all();

        foreach ( $projects as $project ) {
            $totalPayment = Payment::where('payment_status_id', 1)->whereHas('property', function($query) use($project) {
                $query->where('project_id', $project->id);
            })->sum('amount');

            $totalAdvance = Property::whereHas('installments')->where('project_id', $project->id)->sum('pay_in_advance');

            $earningProject[ $project->id ] = [
                'id' => $project->id,
                'name' => $project->name,
                'total' => $totalPayment + $totalAdvance,
            ];
        }

        $data['filters'] = [
            'projects' => $projects,
            'status' => PaymentStatus::all(),
            'owners' => User::where('role_id', 2)->get()
        ];

        $totalEarningsPayment = Payment::where('payment_status_id', 1)->sum('amount');
        $totalEarningsAdvance = Property::whereHas('installments')->sum('pay_in_advance');

        $data['info'] = [
            'total_earnings'  => ( $totalEarningsPayment + $totalEarningsAdvance ),
            'properties_sold' => Property::whereHas('payments', function($query) {
                $query->where('payment_status_id', 1);
            })->count(),
            'total_by_properties' => $earningProject,
        ];

        return($data);

        return response(['msg' => 'Filtros obtenidos exitósamente', 'status' => 'success', 'data' => $data], 200);
    }

    /**
     * Get all payments (only admin)
     *
     * @param  Request  $request
     */
    public function getPayments(Request $req)
    {
        $extraFilters = [ 
            'user' => $req->user(),
            'limit' => $req->limit ?? 100
         ];

        $req->request->add( $extraFilters );
        
        $items = Payment::filter( $req->all() )->orderBy('id', 'desc')->get();

        return response(['msg' => 'Pagos obtenidos exitósamente', 'status' => 'success', 'data' => $items->load(['type', 'status', 'property', 'owner'])], 200);
    }
    /**
     * Pay order products
     *
     * @param  Request  $request
     */
    public function processOrder(Request $req)
    {
        $paymentType = PaymentType::find($req->payment_type_id);

        if (! $paymentType ) { return response(['msg' => 'Especifique un tipo de pago válido', 'status' => 'error'], 200); }

        $property = Property::find($req->property_id);

        if (! $property ) { return response(['msg' => 'Especifique una propiedad para el pago', 'status' => 'error'], 200); }

        #Empieza el proceso para el pago

        $payment = New Payment;

        if ( $paymentType->name == 'Tarjeta' ) {

            $card = Card::where('id', $req->card_id)->where('user_id', $req->user()->id)->first();

            if (! $card ) { return response(['msg' => 'ID de tarjeta inválida', 'status' => 'error'], 200); }

            $res = $this->makePayment($req, $req->user(), $card, $req->amount, 'MXN');

            #Pago por conekta falló
            if ( $res['status'] != 'success' ) { return response($res, 200); }

            // Aplicaremos la lógica en un cronjob para asignar esta tarjeta a pagos recurrentes siempre y cuando tenga pagos pendientes por hacer
            if ( $req->recurrent == 1 ) {
                
                $property->card_id = $card->id;
                
                $property->save();
            }

            #Se guarda la información del pago de conekta en una variable
            $response = $res['data'];

            #Se guarda el id de la orden de conekta
            $payment->property_id = $property->id;
            // $payment->payment_type_id = 2;
            $payment->payment_status_id = 1;
            $payment->amount = round($req->amount / 100, 2);//Total pagado por cliente en CENTAVOS
            $payment->payment_date = date("Y-m-d H:i:s");

        } elseif ( $paymentType->name == 'SPEI' ) {

            $res = $this->payConektaSpei( $req->user(), $req->amount, $req );
            dd($res);
            #Pago por conekta falló
            if ( $res['status'] != 'success' ) { return response($res, 200); }

            #Se guarda la información del pago de conekta en una variable
            $response = $res['data'];

            $payment->pagado = 0;
            $payment->total = $response->amount;//Total pagado por cliente en CENTAVOS
            $payment->token_pedido = $response->id;
            $payment->status_pedido_id = 6;#Procesando pago de spei

            #Se guarda la información del pago de conekta en una variable
            $spei_order = $res['data'];

            $spei_data = New SpeiData;

            $spei_data->banco = $spei_order->charges[0]->payment_method->receiving_account_bank;
            $spei_data->clabe = $spei_order->charges[0]->payment_method->receiving_account_number;
            $spei_data->total = $spei_order->amount;//En centavos
            $spei_data->fecha_expiracion = $spei_order->charges[0]->payment_method->expires_at;
            // $spei_data->due_date_string = strftime('%d', strtotime($spei_order->due_date)).' de '.strftime('%B', strtotime($spei_order->due_date)). ' del '.strftime('%Y', strtotime($spei_order->due_date));

        } elseif ( $paymentType->name == 'Efectivo' ) {
            $photo = $this->uploadFile($req->file('photo'), 'img/pagos', true);

            if (! $photo ) { return response(['msg' => 'Archivo de comprobante inválido', 'status' => 'error'], 200); }

            $payment->property_id = $property->id;
            // $payment->payment_type_id = 1;
            $payment->payment_status_id = 3;
            $payment->photo = $photo;
            $payment->amount = round($req->amount / 100, 2);//Total pagado por cliente en CENTAVOS
            // $payment->amount = $req->amount;//Total pagado por cliente en CENTAVOS
            $payment->payment_date = $req->payment_date;
        }

        $payment->user_id = $req->user()->id;
        $payment->payment_type_id = $paymentType->id;

        $payment->save();

        if ( $paymentType->name == 'Tarjeta' ) {
            $this->calculateInstallments($payment);    
        }
        else if ( $paymentType->nombre == 'SPEI' ) {#Se terminan de guardar los datos de spei

            // $spei_data->pedido_id = $payment->id;

            // $spei_data->save();

        }
        $msg = "Pago registrado exitósamente";
        $desc = "Ha registrado un pago para la propiedad ".$property->name." por la cantidad de $".number_format(round($req->amount / 100, 2), 2)." MXN.";
        $this->sendNotification(2, $msg, $desc, null, null, ['origin' => 'System'], [$req->user()->id]);
        $this->saveNotification($req->user(), $msg, $desc);
        return response(['msg' => $msg, 'status' => 'success', 'data' => $payment->load(['type', 'status'])], 200);
    }
}
