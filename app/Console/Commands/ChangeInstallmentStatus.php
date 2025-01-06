<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\Installment;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChangeInstallmentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:installment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('America/Mexico_city');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now();
        $yesterday = Carbon::now()->subDays(1);
        $installments = Installment::where('date', $yesterday->toDateString())
        ->whereIn('installment_status_id', [2])// Normal
        ->update(['installment_status_id' => 4]);// Se cambia el registro a vencido

        $msgFinish = 'Comando check:installment-status ejecutado a las '.$today->toDateTimeString().', detectando '.$installments. ' pagos vencidos.';
        Log::info($msgFinish);
        return Command::SUCCESS;
    }
}
