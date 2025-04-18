<?php

namespace App\Http\Controllers\Api\Mobile\Evento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \stdClass;
use App\Models\Categorias;
use App\Models\Recinto;
use App\Models\Generos;
use App\Models\Eventos;
use Carbon\Carbon;
class EventoController extends Controller
{
    public function paraTi(Request $request,$user_id)
    {
        
        try {
        
            $objparaTi = new \stdClass();
            $fechaHoy = Carbon::now()->format('Y-m-d');
            $paraTiArray = DB::table('usuario_evento')
            ->join('evento', 'usuario_evento.evento_id', '=', 'evento.id')
            ->join('genero_evento', 'evento.id', '=', 'genero_evento.evento_id')
            ->join('generos', 'genero_evento.genero_id', '=', 'generos.id')
            ->join('categorias', 'categorias.id', '=', 'generos.categoria_id')
            ->join('recinto', 'recinto.id', '=', 'evento.recinto')
            ->select('usuario_evento.*', 'evento.*',
            'categorias.nombre as categoria_id', 'categorias.id as categoria', 'categorias.nombre as categoria_nombre',
             'generos.id as genero_id','generos.nombre as genero_nombre', 'recinto.nombre as recinto_nombre',
             'recinto.direccion as recinto_direccion','recinto.lat as lat', 'recinto.lng as lng')
            ->where('usuario_id',$user_id)
            ->where('evento.fecha_inicio', '>=', $fechaHoy)
            ->get();
            $datos = json_decode($paraTiArray);
            $eventos = DB::table('evento')
            ->orderBy('nombre')
            ->join('recinto', 'recinto.id', '=', 'evento.recinto')
            ->join('genero_evento', 'evento.id', '=', 'genero_evento.evento_id')
            ->join('generos', 'genero_evento.genero_id', '=', 'generos.id')
            ->join('categorias', 'categorias.id', '=', 'generos.categoria_id')
            ->select('evento.*',
                'recinto.nombre as recinto_nombre', 'generos.id as genero_id',
                'generos.nombre as generos_nombre', 'categorias.nombre as categoria_id', 'categorias.id as categoria', 'categorias.nombre as categoria_nombre',
                'recinto.direccion as recinto_direccion','recinto.lat as lat', 'recinto.lng as lng');
         
            $eventos = $eventos->where('evento.fecha_inicio', '>=', $fechaHoy)->get();
            $converterEventos = json_decode($eventos);
           
            foreach ($converterEventos as $key => $evento) {
                $galeria_evento = DB::table('galeria_evento')->where('evento','=',$evento->id)->get();
                $evento->image = $galeria_evento;
                
            }
            $recintos = DB::table('recinto')->orderBy('nombre')->get();
            $categorias = DB::table('categorias')->orderBy('nombre')->get();
            $recintos_cercas = DB::table('evento')->orderBy('nombre')->where('evento.fecha_inicio', '>=', $fechaHoy)->get();
            $generos = DB::table('generos')->orderBy('nombre')->get();
            

            $objparaTi->para_ti = $datos;
            $objparaTi->generos = $generos;
            $objparaTi->eventos = $converterEventos;
            $objparaTi->recintos = json_decode($recintos);
            $objparaTi->categorias  = json_decode($categorias); 
            $objparaTi->recintos_cercas  = []; 
            return $objparaTi;
        } catch (\Throwable $th) {
            return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
        }
        return \Response::json($objparaTi);
    }

    public function show(Request $request,$id)
    {
      
        try {
            // Obtener el evento específico
            $eventos = DB::table('evento')->where('evento.id', '=', $id)->get();
            $galeriaEventos = DB::table('galeria_evento')->select('galeria_evento.image as foto')->where('galeria_evento.evento', '=', $id)->get();

            // Convertir los resultados a un objeto para poder modificarlos
            $eventosConvertidos = json_decode($eventos);

            // Recorrer cada evento y reemplazar el recinto con los detalles del recinto
            foreach ($eventosConvertidos as $evento) {
                $recintos = DB::table('recinto')->where('id', '=', $evento->recinto)->get();
                $evento->recinto = json_decode($recintos); // Reemplaza el id del recinto con los detalles del recinto
                $evento->galeria = json_decode($galeriaEventos); // Galeria
            }

            // Convertir de nuevo a JSON para la respuesta
            $eventosJson = json_encode($eventosConvertidos);
        } catch (\Throwable $th) {
            // En caso de error, retornar una respuesta con el error
            return \Response::json(['exito' => 'false', 'msg' => $th, 'status' => '500'], 500);
        }

        // Retornar la respuesta con los eventos modificados
        return \Response::json($eventosConvertidos);
    }

        public function liked(Request $request)
        {
            try {

                $objLiked= new \stdClass();
            
                $eventos = DB::table('evento')->orderBy('nombre')->get();
                $recintos = DB::table('recinto')->orderBy('nombre')->get();
                $categorias = DB::table('categorias')->orderBy('nombre')->get();
                $recintos_cercas = DB::table('evento')->orderBy('nombre')->get();
            
                $objLiked->para_ti = [];
                $objLiked->eventos = [];
                
                return $objLiked;
              
            } catch (\Throwable $th) {
                throw $th;
            }
            return \Response::json($objLiked);
        }

        public function toggleLike(Request $request,$evento_id)
        {

            $user_id = $request->user_id;
            try {
                $obj_evento  = new \stdClass();
                $relacion_evento =  DB::table('usuario_evento')->where('usuario_id','=',$user_id)
                ->where('evento_id','=',$evento_id)->get();
    
                $save = DB::table('usuario_evento')->insert([
                    'usuario_id' => $user_id,
                    'evento_id' => $evento_id
                ]);
            }catch (\Illuminate\Database\QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if($errorCode == 1062){
                    return response()->json(['message' => 'Ya existe la relacion del evento con el usuario']);
                }
               }

           
            return \Response::json(['message' => 'Guardado con exito','status' => '200'], 200);
        }

        public function unlikeToggle(Request $request, $evento_id)
        {
            $user_id = $request->user_id;
            try {
                $obj_evento  = new \stdClass();
                $relacion_evento =  DB::table('usuario_evento')->where('usuario_id','=',$user_id)
                ->where('evento_id','=',$evento_id)->delete();
            }catch (\Illuminate\Database\QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if($errorCode == 1062){
                    return response()->json(['message' => 'Ya existe la relacion del evento con el usuario']);
                }
               }
            return \Response::json(['message' => 'Eliminado con exito','status' => '200'], 200);
        }

        public function getFiltros(Request $request)
        {
            $categorias = $request->categoria;

            $getCategoria= DB::table('categorias')->where('id','=',$categorias)->get();
            $filtered = $categorias->filter(function ($value, $key) {
                return $value > 21;
            });
          return $categorias;
        }

        public function likedById(Request $request, $id)
        {

            //$user_id = $request->user_id;
            $fechaHoy = Carbon::now()->format('Y-m-d');
            $obj_evento  = new \stdClass();
            $relacion_evento =  DB::table('usuario_evento')->where('usuario_id','=',$id)->get();

            $datos = json_decode($relacion_evento);
        
            foreach ($datos as $key => $dato) {
                $eventos = DB::table('evento')
                ->join('recinto', 'recinto.id', '=', 'evento.recinto')
                ->join('genero_evento', 'evento.id', '=', 'genero_evento.evento_id')
                ->join('generos', 'genero_evento.genero_id', '=', 'generos.id')
                ->join('categorias', 'categorias.id', '=', 'generos.categoria_id')
                ->select('evento.*',
                    'recinto.nombre as recinto_nombre', 'generos.id as genero_id',
                    'generos.nombre as generos_nombre', 'categorias.nombre as categoria_id', 'categorias.nombre as categoria_nombre',
                    'recinto.direccion as recinto_direccion','recinto.lat as lat', 'recinto.lng as lng')
                ->where('evento.id','=',$dato->evento_id)->where('evento.fecha_inicio', '>=', $fechaHoy)->get();
                
                $dato->eventos = $eventos;
            }
            $obj_evento->intereses = $datos;

            return $obj_evento;
            //return \Response::json(['message' => 'Guardado con exito','status' => '200'], 200);
        
        }
        public function test()
        {

            try {
        
                $objparaTi = new \stdClass();
               // $paraTiArray = DB::table('usuario_evento')->where('usuario_id','=',$user_id)->get();
                $datos = json_decode($paraTiArray);
                //dd($datos   );
                foreach ($datos as $key => $dato) {
                    $query_eventos = DB::table('evento')->where('id',$dato->evento_id)->first();
                    $dato->evento = $query_eventos;
                }
                
                $eventos = DB::table('evento')->orderBy('nombre')->get();
                $converterEventos = json_decode($eventos);
               
                foreach ($converterEventos as $key => $evento) {
                    $galeria_evento = DB::table('galeria_evento')->where('evento','=',$evento->id)->get();
                    $evento->image = $galeria_evento;
                    
                }
                $recintos = DB::table('recinto')->orderBy('nombre')->get();
                $categorias = DB::table('categorias')->orderBy('nombre')->get();
                $recintos_cercas = DB::table('evento')->orderBy('nombre')->get();
                $generos = DB::table('generos')->orderBy('nombre')->get();
                
    
                $objparaTi->para_ti = $datos;
                $objparaTi->generos = $generos;
                $objparaTi->eventos = $converterEventos;
                $objparaTi->recintos = json_decode($recintos);
                $objparaTi->categorias  = json_decode($categorias); 
                $objparaTi->recintos_cercas  = []; 
                return $objparaTi;
            } catch (\Throwable $th) {
                return \Response::json(['exito' => 'false', 'msg' => $th,'status' => '500'], 500);
            }
            return \Response::json($objparaTi);
        }

        public function filtro2(Request $request)
        {
            $filtros = new \stdClass();
            $categoria_request = $request->categoria;
            $genero_request = $request->genero;
            $recinto_request = $request->recinto;
            if ($genero_request) {
                $getGenero = DB::table('generos')->whereIn('id',$genero_request)->get();
                   
            }
            if ($categoria_request) {
                $getCategorias = DB::table('categorias')->whereIn('id',$categoria_request)->get();
                   
            }
            if ($recinto_request) {
                $getRecinto = DB::table('recinto')->whereIn('id', $recinto_request)->get();     
            }
           
            $filtros->filtros = [
               'genero' => $getGenero ?? '',
                'categoria'=>$getCategorias ?? '',
                'recinto'=>$getRecinto ?? ''
            ];
            return $filtros;
        }

        public function filtro4(Request $request)
        {
            $filtros = new \stdClass();
            $genero_request = $request->genero;
            $categoria_request = $request->categoria;
            $recinto_request = $request->recinto;

            if (!empty($genero_request)) {
                $getGenero = DB::table('generos')->whereIn('id',$genero_request)->get();  
            }else{
                $getGenero = DB::table('generos')->get();  
            }
            if (!empty($categoria_request)) {
                $getCategorias = DB::table('categorias')->whereIn('id',$categoria_request)->get();  
            }else{
                $getCategorias = DB::table('categorias')->get();  
            }
            if (!empty($recinto_request)) {
                $getRecinto = DB::table('recinto')->whereIn('id',$categoria_request)->get();  
            }else{
                $getRecinto = DB::table('recinto')->get();  
            }

            $filtros->filtros = [
                'genero' => $getGenero ?? '',
                 'categoria'=>$getCategorias ?? '',
                 'recinto'=>$getRecinto ?? ''
             ];
             return $filtros;
        }

        public function filtros(Request $request)
        {
            $filtros = new \stdClass();
            $genero_request = $request->genero;
            $categoria_request = $request->categoria;
            $recinto_request = $request->recinto;
            
            $eventos = DB::table('evento')
            ->join('recinto', 'recinto.id', '=', 'evento.recinto')
           // ->join('orders', 'users.id', '=', 'orders.user_id')
           // ->select('users.*', 'contacts.phone', 'orders.price')
            ->where('recinto.id', IN ([$recinto_request]))
            ->get();

            $filtros->filtros = [
                'genero' => $getGenero ?? '',
                 'categoria'=>$getCategorias ?? '',
                 'recinto'=>$getRecinto ?? ''
             ];
             return $eventos;
        }

        public function filtro(Request $request)
        {
            $data = json_decode($request->getContent(), true);
            
            $filtros = new \stdClass();
            $genero_request = $data['filtros']['genero'] ?? [];
            $categoria_request = $data['filtros']['categoria'] ?? [];
            $recinto_request = $data['filtros']['recinto'] ?? [];
            $dateStart = $data['filtros']['fecha']['from'] ?? null;
            $dateEnd = $data['filtros']['fecha']['to'] ?? null;
            $price_min = $data['filtros']['precio']['min'] ?? null;
            $price_max = $data['filtros']['precio']['max'] ?? null;
            $texto = $data['filtros']['texto'] ?? '';
            $promocion = $data['filtros']['promocion'] ?? null;
        
            $cleanTexto = str_replace('"', '', $texto);
            // Manejo del filtro de recintos
            if (!empty($recinto_request)) {
                if (is_array($recinto_request) && isset($recinto_request[0]['id'])) {
                    $recinto_request = array_map(function($recinto) {
                        return intval($recinto['id']);
                    }, $recinto_request);
                } elseif (is_object($recinto_request)) {
                    $recinto_request = [intval($recinto_request->id)];
                }
            }
            $cleanRecinto = array_map('intval', (array) $recinto_request);
            // Manejo del filtro de genero
            if (!empty($genero_request)) {
                if (is_array($genero_request) && isset($genero_request[0]['id'])) {
                    $genero_request = array_map(function($recinto) {
                        return intval($recinto['id']);
                    }, $genero_request);
                } elseif (is_object($genero_request)) {
                    $genero_request = [intval($genero_request->id)];
                }
            }
            $cleanGenero = array_map('intval', (array) $genero_request);
            // Manejo del filtro de categorias
            if (!empty($categoria_request)) {
                if (is_array($categoria_request) && isset($categoria_request[0]['id'])) {
                    $categoria_request = array_map(function($recinto) {
                        return intval($recinto['id']);
                    }, $categoria_request);
                } elseif (is_object($categoria_request)) {
                    $categoria_request = [intval($categoria_request->id)];
                }
            }
            $cleanCategoria = array_map('intval', (array) $categoria_request);
        
            $eventos = DB::table('evento')
                ->select(
                    'evento.*', 'recinto.zona_id', 'recinto.contacto', 'recinto.horario_inicio', 'recinto.capacidad', 'recinto.amenidades',
                    'recinto.promocion', 'recinto.lat', 'recinto.lng', 'recinto.nombre as recinto_nombre',
                    'recinto.direccion', 'recinto.horario_fin', 'recinto.telefono', 'genero_evento.evento_id', 'genero_evento.genero_id',
                    'generos.categoria_id', 'generos.created_at', 'generos.updated_at', 'categorias.thumbnail', 'categorias.background',
                    'categorias.deleted_at', 'categorias.nombre as categoria_id', 'categorias.id as categoria', 'categorias.nombre as categoria_nombre'
                )
                ->join('recinto', 'recinto.id', '=', 'evento.recinto')
                ->join('genero_evento', 'genero_evento.evento_id', '=', 'evento.id')
                ->join('generos', 'genero_evento.genero_id', '=', 'generos.id')
                ->join('categorias', 'generos.categoria_id', '=', 'categorias.id');
        
            if (!empty($cleanTexto)) {
                $eventos->where('evento.nombre', 'like', '%' . $cleanTexto . '%');
            }
        
            if (!empty($cleanRecinto)) {
                $eventos->whereIn('recinto', $cleanRecinto);
            }
        
            if (!empty($cleanGenero)) {
                $eventos->whereIn('generos.id', $cleanGenero);
            }
        
            if (!empty($cleanCategoria)) {
                $eventos->whereIn('categorias.id', $cleanCategoria);
            }

            if (!is_null($promocion)) {
                $eventos->where('recinto.promocion', '=', '1');
            }
            
            // Obtener los eventos de la consulta inicial
            $eventosGlobal = $eventos->get()->toArray();
            // Filtrar por precio en PHP
            $eventosFiltrados = array_filter($eventosGlobal, function($eventosGlobalPrecio) use ($price_min, $price_max) {
                if (!is_null($price_min) && !is_null($price_max)) {
                    return $eventosGlobalPrecio->precio_bajo >= $price_min && $eventosGlobalPrecio->precio_alto <= $price_max;
                } elseif (is_null($price_min) && is_null($price_max)) {
                    return $eventosGlobalPrecio->precio_bajo >= 0;
                } elseif (is_null($price_min) && $price_max === 0) {
                    return $eventosGlobalPrecio->precio_bajo == 0 && $eventosGlobalPrecio->precio_alto == 0 && $eventosGlobalPrecio->es_gratuito == 1;
                } elseif (is_null($price_max) && $price_min >= 0) {
                    return $eventosGlobalPrecio->precio_bajo >= $price_min;
                }
                return true;
            });

            // Filtrar por fechas en PHP
            $fechaHoy = Carbon::now()->format('Y-m-d'); // Fecha actual formateada
            $eventosFiltradosFecha = array_filter($eventosFiltrados, function($eventoFFechas) use ($dateStart, $dateEnd, $fechaHoy) {
                $fechaInicioEvento = Carbon::parse($eventoFFechas->fecha_inicio)->format('Y-m-d');
                $fechaFinEvento = Carbon::parse($eventoFFechas->fecha_fin)->format('Y-m-d');
                if (is_null($dateStart) && is_null($dateEnd)) {
                    return $fechaInicioEvento >= $fechaHoy || $fechaFinEvento >= $fechaHoy;
                }
                $startDate = $dateStart ? Carbon::parse($dateStart)->format('Y-m-d') : null;
                $endDate = $dateEnd ? Carbon::parse($dateEnd)->format('Y-m-d') : null;
                if ($dateStart === $dateEnd) {
                    return $startDate >= $fechaInicioEvento && $startDate <= $fechaFinEvento;
                } else {
                    // Filtrar por rango de fechas
                    return (
                        ($fechaInicioEvento >= $startDate && $fechaInicioEvento <= $endDate) ||
                        ($fechaFinEvento >= $startDate && $fechaFinEvento <= $endDate) ||
                        ($fechaInicioEvento <= $startDate && $fechaFinEvento >= $endDate)
                    );
                }
            });

            return collect($eventosFiltradosFecha)->unique('id')->values();
        }


        public function promociones(Request $request)
        {
            $data = json_decode($request->getContent(),true);
            
            $filtros = new \stdClass();
            $genero_request = $data['filtros']['genero'];
            $categoria_request = $data['filtros']['categoria'];
            $recinto_request = $data['filtros']['recinto'];
    
            $dateStart = $data['filtros']['fecha']['from'];
            $dateEnd = $data['filtros']['fecha']['to'];
    
            $price_min = $data['filtros']['precio']['min'];
            $price_max = $data['filtros']['precio']['max'];
    
            $texto = $data['filtros']['texto'];
            $promocion = $data['filtros']['promocion'] ?? null;
            

            $cleanRecinto = array_map('intval',str_replace('"', '', $recinto_request));
            $cleanGenero = array_map('intval',str_replace('"', '', $genero_request));
            $cleanCategoria = [];
            if (isset($categoria_request[0]) && !empty($categoria_request[0])) {
                $cleanCategoria = $categoria_request;
            }
            $cleanTexto = str_replace('"', '', $texto);
            
            $startDate = Carbon::parse($dateStart)->format('Y-m-d');
            $endDate = $dateEnd ? Carbon::parse($dateEnd)->format('Y-m-d') : $startDate;

            $eventos = DB::table('evento')
            ->select('evento.*','recinto.zona_id', 'recinto.contacto','recinto.horario_inicio','recinto.capacidad','recinto.amenidades','recinto.promocion','recinto.lat','recinto.lng','recinto.direccion','recinto.horario_fin','recinto.telefono','recinto.nombre as recinto_nombre','recinto.direccion as recinto_direccion','genero_evento.evento_id','genero_evento.genero_id','generos.categoria_id','generos.created_at','generos.updated_at','generos.nombre as generos_nombre','categorias.thumbnail','categorias.background','categorias.deleted_at','categorias.nombre as categoria_id', 'categorias.nombre as categoria_nombre')
            ->join('recinto', 'recinto.id', '=', 'evento.recinto')
            ->join('genero_evento', 'genero_evento.evento_id', '=', 'evento.id')
            ->join('generos', 'genero_evento.genero_id', '=', 'generos.id')
            ->join('categorias', 'generos.categoria_id', '=', 'categorias.id');

            // $eventos->where('recinto.promocion', '=', '1');
            $eventos->where('evento.texto_promocional', '<>', null);
            $eventos->where('evento.fecha_inicio', '>=', $startDate);
            if ($startDate != $endDate) {
                $eventos->where('evento.fecha_fin', '<=', $endDate);
            }

            if (!empty($cleanTexto)) {
                $eventos->where('evento.nombre', 'like', '%' . $cleanTexto . '%');
            }
        
            if (!empty($cleanRecinto)) {
                $eventos->whereIn('recinto', $cleanRecinto);
            }
        
            if (!empty($cleanGenero)) {
                $eventos->whereIn('generos.id', $cleanGenero);
            }
        
            if (!empty($cleanCategoria)) {
                $eventos->whereIn('categorias.id', $cleanCategoria);
            }


            if (is_null($price_min) && $price_max === 0) {
                $eventos->where(function ($query) {
                    $query->where('precio_bajo', '=', 0)
                          ->where('precio_alto', '=', 0)
                          ->where('es_gratuito', '=', 1);
                });
            } elseif (!is_null($price_min) && !is_null($price_max)) {
                $eventos->where(function ($query) use ($price_min, $price_max) {
                    $query->where('precio_bajo', '<=', $price_min)
                          ->where('precio_alto', '>=', $price_max);
                });
            }

            return $eventos->get();
            
        }

        

        public function futuros(Request $request)
        {
            $data = json_decode($request->getContent(), true);
            
            $filtros = new \stdClass();
            $limite = $data['filtro']['limite'] ?? 20;
            $today = Carbon::today();
            $eventos = DB::table('evento')
                ->select(
                    'evento.*', 'recinto.zona_id', 'recinto.contacto', 'recinto.horario_inicio', 'recinto.capacidad', 'recinto.amenidades',
                    'recinto.promocion', 'recinto.lat', 'recinto.lng', 'recinto.nombre as recinto_nombre',
                    'recinto.direccion', 'recinto.horario_fin', 'recinto.telefono', 'genero_evento.evento_id', 'genero_evento.genero_id',
                    'generos.categoria_id', 'generos.created_at', 'generos.updated_at', 'categorias.thumbnail', 'categorias.background',
                    'categorias.deleted_at', 'categorias.nombre as categoria_id', 'categorias.id as categoria', 'categorias.nombre as categoria_nombre'
                )
                ->join('recinto', 'recinto.id', '=', 'evento.recinto')
                ->join('genero_evento', 'genero_evento.evento_id', '=', 'evento.id')
                ->join('generos', 'genero_evento.genero_id', '=', 'generos.id')
                ->join('categorias', 'generos.categoria_id', '=', 'categorias.id')
                ->whereDate('fecha_inicio', '<=', $today)
                ->whereDate('fecha_fin', '>=', $today)
                ->orderBy('fecha_inicio', 'asc')
                ->limit($limite);
            return $eventos->get()->unique("id")->values();
        }
}