<?php

namespace App\Http\Controllers;

use Excel;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\ProjectStage;

use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $menu = "Proyectos";
        $title = "Proyectos";
        $filters = [ 
            'user' => auth()->user(), 
            'limit' => 100, 
            'ordenar_fecha' => null,
        ];

        $items = Project::filter( $filters )->orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('projects.table', compact('items'));
        }
        return view('projects.index', compact('items', 'menu', 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de proyecto";
        $menu = "Proyectos";
        $item = null;
        $filters = [ 'user' => auth()->user(), 'roles' => [2] ];
        $users = User::filter( $filters )->get();

        if ( $id ) {
            $item = Project::find($id);
        }
        return view('projects.form', compact('item', 'users', 'menu', 'title'));
    }

    /**
     * Show the orders acording to the filters given for user.
     *
     */
    public function filter( Request $req )
    {
        $extraFilters = [ 
            'user' => auth()->user(), 
            'ordenar_fecha' => null, 
        ];

        $req->request->add( $extraFilters );
        
        $items = Project::filter( $req->all() )->orderBy('id', 'desc')->get();

        $view = 'projects.table';
        
        return view($view, compact('items'));
    }

    /**
     * Get the gallery view from project
     *
     */
    public function getGallery($id)
    {
        $item = Project::find($id);

        return view('projects.gallery', compact(['item']));
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

        $item = Project::find($id);
        if (! $item ) { 
            return response(['msg' => 'Registro no encontrado', 'status' => 'error'], 404); 
        }

        $time = $item->created_at;

        $item->fecha_formateada = strftime('%d', strtotime($time)).' de '.strftime('%B', strtotime($time)). ' del '.strftime('%Y', strtotime($time)). ' a las '.strftime('%H:%M', strtotime($time)). ' hrs.';
        
        return response(['msg' => 'Información mostrada a continuación', 'status' => 'success', 'data' => $item], 200); 
    }

    /**
     * Save a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $req)
    {
        $photo = $this->uploadFile($req->file('photo'), 'img/proyectos', true);
        $logo  = $this->uploadFile($req->file('logo'), 'img/proyectos', true);

        $item = New Project;

        $item->name           = $req->name;
        $item->description    = $req->description;
        $item->video_link     = $req->video_link;
        $item->address        = $req->address;
        $photo ? $item->photo = $photo : '';
        $logo  ? $item->logo  = $logo : '';

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('proyectos'), 'status' => 'success', 'data' => $item ], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $item = Project::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('proyectos')], 404); }

        $photo = $this->uploadFile($req->file('photo'), 'img/proyectos', true);
        $logo  = $this->uploadFile($req->file('logo'), 'img/proyectos', true);

        $item->name           = $req->name;
        $item->description    = $req->description;
        $item->video_link     = $req->video_link;
        $item->address        = $req->address;
        $photo ? $item->photo = $photo : '';
        $logo  ? $item->logo  = $logo : '';
        $item->save();
        
        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('proyectos'), 'status' => 'success', 'data' => $item ], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $req)
    {
        $item = Project::whereIn('id', $req->ids)
        ->first();

        if ( $item ) {
            // Remove permanently
            if ( $req->forceDelete ) {
                $this->deletePath( $item->photo );
    
                // Remove gallery
                if ( count( $item->photos ) ) {
                    foreach($item->photos as $photo ) {
                        $this->deletePath( $photo->path );
                    }
                }
                $item->forceDelete();
            } else {
                $item->delete();
            }
            return response(['msg' => 'Éxito eliminando el proyecto', 'url' => url('proyectos'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status del proyecto ', 'status' => 'error', 'url' => url('proyectos')], 404);
        }
    }

    /**
     * Remove the main photo of the project.
     *
     * @return \Illuminate\Http\Response
     */
    public function deletePhoto(Request $req)
    {
        $item = Project::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error'], 404); }

        if ( $req->type == 'photo' ) {
            $this->deletePath( $item->photo );
            $item->photo = 'img/no-image.png';
        } elseif ( $req->type == 'logo' ) {
            $this->deletePath( $item->logo );
            $item->logo = null;
        }

        $item->save();

        return response(['msg' => 'Archivo removido exitósamente', 'status' => 'success'], 200);
    }

    /**
     * Upload files (images) to the server.
     *
     * @return ['uploaded'=>true]
     */
    public function uploadContent(Request $req) 
    {
        sleep(1);//Need it for not overwrite some contents
        $resize = $req->resize ? (array) json_decode($req->resize) : false;

        $file = $this->uploadFile( $req->file('file'), $req->path, $req->rename, $resize );

        if (! $file ) { return response(['msg' => 'not uploaded', 'status' => 'error'], 200); }
        
        $photo = New ProjectImage;

        $photo->project_id = $req->row_id;
        $photo->name = $req->file('file')->getClientOriginalName();
        $photo->path = $file;
        // $photo->size = $req->file('file')->getClientOriginalName()();

        $photo->save();

        return response(['msg' => 'uploaded', 'status' => 'ok'], 200);
    }

    /**
     * Change the status of the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteContent(Request $req)
    {
        $items = ProjectImage::whereIn('id', $req->ids)
        ->get();

        foreach ($items as $item) {
            // $this->deletePath( $item->photo );
            File::delete(asset($item->photo));
            $item->delete();
        }

        if ( count($items) ) {
            return response(['msg' => 'Éxito eliminando la(s) imagen(es)', 'url' => route('project.getGallery', $req->id), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Registro de imágenes no encontrados', 'status' => 'error', 'url' => route('project.getGallery', $req->id)], 404);
        }
    }

    /**
     * Export the orders to excel according to the filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function export( Request $req )
    {
        $filters = [ 'user' => auth()->user() ];

        $items = Project::filter( $filters )->get();
        $rows = $titulos = array();

        foreach ( $items as $item ) {
            $rows [] = [
                'ID proyecto'   => $item->id,
                'Nombre'        => $item->user ? $item->user->fullname : 'No asignado',
                'Descripción'   => $item->categoria ? $item->categoria->nombre : 'No asignada',
                'Video link'    => $item->subcategoria ? $item->subcategoria->nombre : 'No asignada',
                'Visible'       => $item->deleted_at == 'S' ? 'Si' : 'No',
                'Fecha de alta' => strftime('%d', strtotime($item->created_at)).' de '.strftime('%B', strtotime($item->created_at)). ' del '.strftime('%Y', strtotime($item->created_at)). ' a las '.strftime('%H:%M', strtotime($item->created_at)). ' hrs.',
            ];
        }

        // More than 1 row
        if ( count($rows) ) {
            $titulos = array_keys($rows[0]);
        }
        return Excel::download(new EventoExport($rows, $titulos), 'Listado de proyectos '.date('d-m-Y').'.xlsx');
    }
}
