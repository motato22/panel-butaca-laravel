<?php

namespace App\Http\Controllers;

use Excel;

use \App\Models\User;
use \App\Models\Project;
use \App\Models\Payment;
use \App\Models\Property;
use \App\Models\Installment;
use \App\Models\Notification;
use \App\Models\PaymentStatus;

use \App\Exports\CustomersExport;

use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $menu = "Pagos";
        $title = "Pagos";
        $filters = [
            'user' => auth()->user(), 
            'limit' => 100, 
        ];

        $projects = Project::all();
        $status   = PaymentStatus::all();
        $owners   = User::where('role_id', 2)->get();
        $items    = Payment::filter( $filters )->orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('payments.table', compact('items'));
        }
        return view('payments.index', compact('items', 'projects', 'owners', 'status', 'menu', 'title'));
    }

    /**
     * Generate the installment for a property and a user
     *
     */
    public function formCreateInstallmentsPlan($id)
    {
        $title = "Crear plan de pagos";
        $menu = "Propiedades";
        $item = null;
        $filters = [ 'user' => auth()->user(), 'roles' => [2] ];
        $users = User::filter( $filters )->get();

        if ( $id ) {
            $item = Property::find($id);
            if (! $item ) {
                return view('errors.404');
            }
        }
        return view('properties.create_installment_plan', compact('item', 'users', 'menu', 'title'));
    }

    /**
     * Generate the installment for a property and a user
     *
     */
    public function createInstallmentsPlan(Request $req)
    {
        $property = Property::find($req->id);
        $user     = User::where('id', $req->owner_id)->where('role_id', 2)->first();

        if (! $property ) { return response(['msg' => 'Propiedad inválida', 'status' => 'error'], 404); }
        if (! $user ) { return response(['msg' => 'Seleccione un cliente para asignar el plan de pagos', 'status' => 'error'], 404); }
        if (! count($req->installmentsArray) ) { return response(['msg' => 'Plan de pagos inválido', 'status' => 'error'], 404); }
        if ( $property->owner ) { return response(['msg' => 'Esta propiedad ya cuenta con un cliente asignado', 'status' => 'error'], 404); }
        if ( count($property->installments) ) { return response(['msg' => 'Esta propiedad ya cuenta con un plan de pagos mensuales asignados', 'status' => 'error'], 404); }

        foreach( $req->installmentsArray as $installmentData ) {
            $installment = New Installment;

            $installment->user_id = $user->id;
            $installment->property_id = $property->id;
            $installment->installment_status_id = 2;
            $installment->amount = $installmentData['amount'];
            $installment->date = $installmentData['date'];

            $installment->save();
        }

        $property->user_id = $user->id;
        $property->pay_in_advance = $req->pay_in_advance;

        $property->save();

        return response(['msg' => 'Plan de pagos generados exitósamente', 'status' => 'success', 'url' => url('propiedades'), 'data' => $property->load(['installments'])], 200);
    }

    /**
     * Generate the installment for a property and a user
     *
     */
    public function updatePaymentDay(Request $req)
    {
        $property = Property::find($req->id);

        if (! $property ) { return response(['msg' => 'Propiedad inválida o no encontrada', 'status' => 'error'], 404); }
        if (! count($req->new_dates) ) { return response(['msg' => 'Proporcione un listado de pagos válidos para continuar', 'status' => 'error'], 400); }
        if (! count($property->installments) ) { return response(['msg' => 'Esta propiedad no cuenta con un plan de pagos por modificar', 'status' => 'error'], 400); }

        foreach( $req->new_dates as $installmentData ) {
            $installment = Installment::where('property_id', $property->id)->where('id', $installmentData['id'])->first();

            $installment->installment_status_id = 2;
            $installment->date = $installmentData['date'];

            $installment->save();
        }

        return response(['msg' => 'Plan de pagos actualizados exitósamente', 'status' => 'success', 'url' => url('propiedades'), 'data' => $property->load(['installments'])], 200);
    }

    /**
     * Show the payments acording to the filters given for user.
     *
     */
    public function filter( Request $req )
    {
        $extraFilters = [ 
            'user' => auth()->user(), 
        ];

        $req->request->add( $extraFilters );
        
        $items = Payment::filter( $req->all() )->orderBy('id', 'desc')->get();

        return view('payments.table', compact('items'));
    }

    /**
     * Show the info of an order.
     *
     */
    public function show($id, Request $req)
    {
        if (! $req->ajax() ) {
            return view('errors.404');
        }

        $item = Payment::where('id', $id)->with(['status'])->first();
        if (! $item ) { 
            return response(['msg' => 'Registro no encontrado', 'status' => 'error'], 404); 
        }

        $properties = Property::filter(['owner_id' => $item->owner->id ])->withTrashed()->get();

        $data = [
            'payment' => $item,
            'properties' => $properties
        ];

        return response(['msg' => 'Información de pago mostrado a continuación', 'status' => 'success', 'data' => $data], 200); 
    }

    /**
     * Generate the installment for a property and a user
     *
     */
    public function changeStatus(Request $req)
    {
        $item = Payment::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el pago a procesar', 'status' => 'error', 'url' => url('pagos')], 404); }

        $property = Property::find($req->property_id);// Este dato se necesita para cuando son pagos son pagos por SPEI
        
        $user = $item->owner;

        if ( $req->change_to == 0 ) {// Pago fue rechazado, procede a eliminarse
            $msg = "Pago rechazado.";
            $content = "El pago de la propiedad ".$item->property->name." ha sido rechazado, por favor, suba un nuevo comprobante.";
            // $item->delete();
        } else {// El pago se procesa exitósamente
            if (! $property ) { return response(['msg' => 'Elija una propiedad para continuar', 'status' => 'error', 'url' => url('pagos')], 404); }
            $msg = "¡Pago aprobado!.";
            $content = "El pago de la propiedad ".$property->name." ha sido aprobado exitósamente.";

            $item->property_id = $property->id;
            $item->payment_status_id = 1;// Pagado
            
            $item->save();

            if ( $item->property ) {
                $this->calculateInstallments($item);
            }

        }
        $this->saveNotification($user, $msg, $content);
        return response(['msg' => 'Pago validado exitósamente', 'url' => url('pagos'), 'status' => 'success' ], 200);
    }

    /**
     * Export the orders to excel according to the filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function export( Request $req )
    {
        $extraFilters = [ 'user' => auth()->user() ];
        $req->request->add( $extraFilters );
        
        $items = Payment::filter( $req->all() )->orderBy('id', 'desc')->get();
        $rows = $titulos = array();

        foreach ( $items as $item ) {
            $rows [] = [
                'ID pago'                  => $item->id,
                'Monto'                    => '$'.$item->amount,
                'Pagado por'               => $item->owner ? $item->owner->fullname : 'N/A',
                'Pago para propiedad'      => $item->property ? $item->property->name : 'N/A',
                'Tipo de pago'             => $item->type ? $item->type->name : 'N/A',
                'Status'                   => $item->status ? $item->status->name : 'N/A',
                'Fecha de pago'            => strftime('%d', strtotime($item->created_at)).' de '.strftime('%B', strtotime($item->created_at)). ' del '.strftime('%Y', strtotime($item->created_at)). ' a las '.strftime('%H:%M', strtotime($item->created_at)). ' hrs.',
            ];
        }

        // More than 1 row
        if ( count($rows) ) {
            $titulos = array_keys($rows[0]);
        }
        return Excel::download(new CustomersExport($rows, $titulos), 'Listado de pagos '.date('d-m-Y').'.xlsx');
    }
}
