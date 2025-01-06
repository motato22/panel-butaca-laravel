<?php

namespace App\Console\Commands;

use App\Models\Card;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Installment;

use App\Traits\ConektaMethods;
use App\Traits\GeneralFunctions;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecurrentPayments extends Command
{
    use ConektaMethods, GeneralFunctions;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:recurrent-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charge recurrent payments from customers';

    /**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('America/Mexico_city');
        \Conekta\Conekta::setApiKey(env("CONEKTA_API_PRIVATE"));
        \Conekta\Conekta::setApiVersion("2.0.0");
	}

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $countSuccess = $countFail = 0;
        $today = Carbon::now();
        // dd($today->isoFormat('Y-m-d H:i:s'));
        // dd( $today->toDateTimeString() );
        $properties = Property::whereHas('installments', function($query) {
            $query->where('installment_status_id', '!=', 1);
        })
        ->whereHas('card')
        ->get();

        foreach ( $properties as $property ) {
            $makePayment = false;
            // Ya ordenado por fecha de forma ascendente desde el modelo, sólo se necesita el primer registro
            $pendingInstallments = $property->installments->where('installment_status_id', '!=', 1)->first();
            if ( $pendingInstallments ) {
                $installmentDate = Carbon::createFromFormat('Y-m-d', $pendingInstallments->date);
                /**
                 * La fecha debe ser mayor o igual a hoy
                 * Si es mayor, revisar que el día de pago sea el mismo que hoy, es decir
                 * Si se pagó por adelantado 2 meses, no puede haber un pago programado para ese mismo mes, debe ser uno diferente
                 * Si es mayor, hay que revisar el monto del adeudo que se tiene
                 */
                if ( $installmentDate->isSameAs('Y-m-d', $today) ) {// Hoy es día de cobro
                    $makePayment = true;
                } else if ($installmentDate->isSameAs('d', $today)) {// Es el mismo día, pero debe ser mayor a n días para que no lo tome por adelantado
                    // Debe existir al menos un pago realizado, caso contrario, significa que sus pagos están programados para una fecha mayor que aún no pasa
                    if ( $installmentDate->greaterThan($today) && $property->payments->count() > 0) {
                        $makePayment = true;
                    }
                }
                // Procede a pagar en caso de que las condiciones se cumplan
                if ( $makePayment == true ) {
                    // Código para cobrar acá
                    $total = intval( ( $pendingInstallments->amount - $pendingInstallments->amount_paid ) * 100 );
                    $res = $this->makePayment( null, $property->owner, $property->card, $total );
                    // dd($res);
                    if ( $res['status'] != 'success' ) {// Pago por conekta falló
                        $countFail ++;
                    } else {
                        $payment = New Payment;
                        
                        $payment->property_id = $property->id;
                        $payment->payment_type_id = 2;
                        $payment->payment_status_id = 1;// Pagado completamente!
                        $payment->amount = round($total / 100, 2);//Total pagado por cliente en pesos
                        $payment->payment_date = $today->toDateTimeString();
                        $payment->user_id = $property->owner->id;
    
                        $payment->save();
                        
                        $countSuccess ++;
                        
                        $this->calculateInstallments($payment);    
                    }
                }
            }
            // dd($pendingInstallments);
            // dd($property);
        }
        // dd($properties);
        $msgFinish = 'Comando check:recurrent-payment ejecutado a las '.$today->toDateTimeString().', realizando exitósamente '.$countSuccess. ' y fallando en '.$countFail;
        Log::info($msgFinish);

        return Command::SUCCESS;
    }
}
