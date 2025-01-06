<?php

namespace App\Traits;

use Mail;
use Image;

use \App\Models\Log;
use \App\Models\User;
use \App\Models\Payment;
use \App\Models\Project;
use \App\Models\Property;
use \App\Models\Installment;
use \App\Models\Notification;

// use \App\Mail\SendGeneralMail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as Logs;
use Illuminate\Support\Facades\File;

trait GeneralFunctions
{
    /**
     * Verify if a file is valid, then upload it to a given path.
     *
     * @return $name
     */
    public function uploadFile($file, $path, $rename = false, $resize = false)
    {
        $extensions = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png", "4"=>"gif");
        $name = '';

        if ( $file ) {
            $file_ext = strtolower($file->getClientOriginalExtension());
            if (array_search($file_ext, $extensions)) {
                if (!File::exists( $path )) {
                    File::makeDirectory(public_path().'/'.$path, 0755, true, true);
                }

                $timer = microtime();
                $timer = str_replace([' ','.'], '', $timer);

                $name = $rename ? $path.'/'.$timer.'.'.$file_ext : $path.'/'.$file->getClientOriginalName();

                if ( $resize ) {
                    $content = Image::make( $file )
                    ->resize( $resize['width'], $resize['height'] )
                    ->save( $name );
                } else {
                    $file->move( $path, $name );
                }
                
                return $name;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Delete a path/file from server
     *
     */
    public function deletePath($path)
    {
        try {
            $fullPath = public_path( $path );

            if ( file_exists($fullPath) ) {// El archivo existe en el servidor
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    unlink( str_replace("/", "\\", $fullPath) );// Código para windows
                    return true;
                } else {
                    unlink( $fullPath );// Código para linux
                    return true;
                }
            }
            // File::delete( public_path( str_replace("/", "\\", $item->photo) ) );
            // return File::delete(asset($path));
            //code...
        } catch (\Exception $ex) {
            logger('Error al eliminar un archivo o ruta: '.$ex->getMessage());
            // dd('Exception',$ex);
            return false;
        // } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Delete a path/file from server
     *
     */
    public function createPath($path)
    {
        try {
            $fullPath = public_path( $path );

            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                return str_replace("/", "\\", $fullPath);// Código para windows
            } else {
                return $fullPath; //Código para linux
            }
        } catch (\Exception $ex) {
            Logs::error('Error al eliminar un archivo o ruta: '.$ex->getMessage());
            // dd('Exception',$ex);
            return false;
        // } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Get a random string
     *
     * @return strtoupper($codigo)
     */
    public function getNotifcationsUSers(Request $req)
    {
        $verifyPlayerID = true;
        $customers = [];

        $customers = User::filter([ 'user' => auth()->user(), 'roles' => [2,3] ])->whereNotNull('player_id')->get();
        // if ( $req->filter == 'all' ) { $customers = User::filter(auth()->user())->get(); }
        // elseif ( $req->filter == 'top_users' ) { $customers = User::getTopTen([2], null, $verifyPlayerID); }
        // elseif ( $req->filter == 'enabled_users' ) { $customers = User::filter_rows(auth()->user(), [2], '1', null, $verifyPlayerID); }
        // elseif ( $req->filter == 'disabled_users' ) { $customers = User::filter_rows(auth()->user(), [2], '0', null, $verifyPlayerID); }

        return ['data' => $customers, 'msg' => 'Usuarios enlistados a continución', 'status' => 'success'];
    }

    /**
     * Send a notification to a single user or a group of users.
     *
     * @return $name
     */
    public function sendNotification($type, $title, $content, $date, $time, $data, $users_id)
    {
        $app_icon = env('ONESIGNAL_APP_ICON');
        $app_key = env('ONESIGNAL_REST_API_KEY');
        $player_ids = array();
        $header = array(
            "en" => $title
        );

        $msg = array(
            "en" => $content
        );
        
        $fields = array(
            'app_id' => env('ONESIGNAL_APP_ID'),
            'data' => $data,
            'headings' => $header,
            'contents' => $msg,
            'large_icon' => $app_icon
        );

        if ( $type == 1 ) {//General notification
            $fields['included_segments'] = array('All');
        } 

        else if ( $type == 2 ) {//Individual notification
            foreach( $users_id as $id ) {
                $user = User::find( $id );
                $player_ids [] = $user->player_id;
            }
            $fields['include_player_ids'] = $player_ids;
        }

        if ( $date && $time ) {
            $time_zone = $date.' '.$time;
            $time_zone = $this->summer ? $time_zone.' '.'UTC-0500' : $time_zone.' '.'UTC-0600';
            $fields['send_after'] = $time_zone;
        }

        $fields = json_encode( $fields );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   "Authorization: Basic $app_key"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec( $ch );
        curl_close( $ch );
        
        $res = json_decode($response);

        return $res;
    }

    /*
    * Return boolean, true if mail was sent, false if mail fails
    *
    */
    public function f_mail($params)
    {
        try {
            // $data = ['message' => 'This is a test!'];
            // Mail::to('anton_con@hotmail.com')->send(new SendGeneralMail($data));
            $params['view'] = $params['view'] ?? 'mails.general';
            Mail::send($params['view'], ['content' => $params], function ($message) use($params)
            {
                $message->to($params['email']);
                $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
                $message->subject(env('APP_NAME').' | '.$params['subject']);
            });
            return ['status' => 'success', 'msg' => 'Correo enviado exitósamente'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msg' => 'Algo salió mal: '.$e->getMessage()];
        }
    }

    /**
     * Verify if request is made 1 day before the service time
     *
     */
    public function check_time(Request $req, Order $order)
    {
        $service_date = strtotime( $order->start_datetime );
        $now = strtotime( $this->actual_datetime );
        $dif = $service_date - $now;

        #1 day diff at least?
        if ( $dif < 86400 ) { return false; }

        return true;
    }

    /**
     * Save the logs to check them in db.
     *
     */
    public function saveLog($req)
    {
        $item = New Log;

        $item->content = $req instanceof Request ? json_encode($req->all()) : $req;
        $item->type = ( $req instanceof Request ? ( $req->type ? $req->type : 'Unknown or custom' ) : 'Unknown or custom' );
        // $item->origin = 'Webhook';

        $item->save();
    }

    /**
     * Save the notification on db.
     *
     */
    public function saveNotification($user, $msg, $content)
    {
        if ( $user->receive_notifications != 1 ) {
            return false;
        }
        $notification = New Notification;

        $notification->user_id = $user->id;
        $notification->title = $msg;
        $notification->content = $content;

        $notification->save();

        return $notification;
    }

    /**
     * Save a payment for customer.
     *
     */
    public function createNewCustomerPayment(User $user, $amount, $typeId)
    {
        $payment = New Payment;
        
        $payment->user_id = $user->id;
        $payment->payment_type_id = $typeId;
        $payment->payment_status_id = 1;
        $payment->amount = $amount;
        $payment->payment_date = date("Y-m-d H:i:s");
        
        $payment->save();
        
        if ( $user->receive_notifications == 1 ) {// Se envía notificación de su pago recibido exitósamente
            $msg = "Pago registrado exitósamente";
            $desc = "Ha registrado un pago por la cantidad de $".number_format(round($amount / 100, 2), 2)." MXN.";
            $this->sendNotification(2, $msg, $desc, null, null, ['origin' => 'System'], [$user->id]);
            $this->saveNotification($user, $msg, $desc);
        }
        return $payment;
    }

    /**
     * Calculates the installments paid with a single payment.
     *
     */
    public function calculateInstallments(Payment $payment)
    {
        $next = true;
        $pago = $payment->amount;
        // dd('Monto disponible: '.$pago);
        $installments = Installment::where('installment_status_id', '!=', 1)
        ->where('property_id', $payment->property_id)
        ->where('user_id', $payment->user_id)
        ->get();

        foreach( $installments as $installment ) {
            if ( $next && $pago > 0 ) {
                $totalDeuda = ($installment->amount - $installment->amount_paid);// The total to paid
                // if ($totalDeuda < 0) { $totalDeuda = $totalDeuda * -1; }
                $pago = $pago - $totalDeuda;
                // dd('pago: '.$pago, 'Total deuda: '.$totalDeuda);
                if( $pago > 0 ) {// Todavía puede seguir cobrándose
                    $installment->installment_status_id = 1;// Pagado!
                    $installment->amount_paid = $installment->amount;// Pagado al 100%
                    // $next = true;
                } else if ( $pago < 0 ) {// Quedará parcialmente pagado
                    $installment->installment_status_id = 3;// Parcialmente pagado
                    $installment->amount_paid = $installment->amount + ($pago);
                    // dd($installment->amount_paid);
                    $next = false;
                    // $installment->amount_paid = ;
                } else if ( $pago == 0 ) {// Quedó justamente pagado, ya no puede aplicarse a más pagos
                    $installment->installment_status_id = 1;// Pagado!
                    $installment->amount_paid = $installment->amount;// Pagado al 100%

                    $next = false;
                }
               
                $installment->save();
            } else {
                return true;
            }
        }
    }
}
