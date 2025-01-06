<?php

namespace App\Http\Categorias;

use App\Models\User;
use App\Models\NewData;
use App\Models\NewCategory;

use Illuminate\Http\Request;

class RwcintosController extends Controller {

    public function index () {

        
        $menu = "Recintos";
        $title = "Recintos";

        return view('recintos.index', compact('menu','title'));
    }

    public function create () {

        $menu = "Recintos";
        $title = "Recintos";

        return view('recintos.create', compact('menu','title'));

    }   
}