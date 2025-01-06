<?php

namespace App\Http\Controllers;

use \App\Models\Configuracion;

use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function politicaPrivacidad(Request $req)
    {
        $title = "Términos y condiciones";
        $menu = "Configuración";
        $item = Configuracion::where('tipo', 'terminos')->first();

        return view('configuraciones.terminos_condiciones', compact('item', 'menu', 'title'));
    }

    /**
     * Show the main view.
     *
     */
    public function avisoPrivacidad(Request $req)
    {
        $title = "Aviso de privacidad";
        $menu = "Configuración";
        $item = Configuracion::where('tipo', 'aviso')->first();

        return view('configuraciones.aviso_privacidad', compact('item', 'menu', 'title'));
    }

    /**
     * Show the main view.
     *
     */
    public function showSystemConfig(Request $req)
    {
        $title = "Configuración de sistema";
        $menu = "Configuración";
        $item = Configuracion::first();
        $contact = Configuracion::where('tipo', 'contacto')->first();
        $descuento = Configuracion::where('tipo', 'descuento')->first();

        if ( $item ) {
            $time = $item->ultima_actualizacion;
            $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time)). ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.';
        }

        return view('configuraciones.system', compact('item', 'contact', 'descuento', 'menu', 'title'));
    }

    /**
     * Save a new resource.
     *
     */
    public function saveTermsConditions(Request $req)
    {
        $item = $req->id ? Configuracion::find($req->id) : New Configuracion;
        
        $item->descripcion = $req->content;
        $item->tipo = 'terminos';
        
        $item->save();

        return response(['status' => 'success', 'msg' => 'Éxito guardando los términos y condiciones'], 200);
    }

    /**
     * Save a new resource.
     *
     */
    public function saveNoticePrivacy(Request $req)
    {
        $item = $req->id ? Configuracion::find($req->id) : New Configuracion;
        
        $item->descripcion = $req->content;
        $item->tipo = 'aviso';
        
        $item->save();

        return response(['status' => 'success', 'msg' => 'Éxito guardando el aviso de privacidad'], 200);
    }
}
