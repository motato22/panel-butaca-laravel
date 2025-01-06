<?php

namespace App\Http\Controllers;

use App\Traits\ConektaMethods;
use App\Traits\GeneralFunctions;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

setlocale(LC_ALL,'es_ES', 'esp_esp');

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ConektaMethods, GeneralFunctions;
    // Revisar como usar corectamente el controlador padre
    // function __construct() {
    //     date_default_timezone_set('America/Mexico_City');
    // }
}
