<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Blog;
use App\Models\Project;
use App\Models\BlogImage;

use Illuminate\Http\Request;

class BlogsController extends Controller
{
    /**
     * Show the main view.
     *
     */
    public function index(Request $req)
    {
        $menu = "Blogs";
        $title = "Blogs";
        $filters = [ 
            'user' => auth()->user(), 
            'limit' => 100, 
        ];

        $projects = Project::all();
        $items = Blog::filter( $filters )->orderBy('id', 'desc')->get();

        if ( $req->ajax() ) {
            return view('blogs.table', compact('items'));
        }
        return view('blogs.index', compact('items', 'projects', 'menu', 'title'));
    }

    /**
     * Show the form for creating/editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id = 0)
    {
        $title = "Formulario de blog";
        $menu = "Blogs";
        $item = null;
        $filters = [ 'user' => auth()->user(), 'roles' => [2] ];
        $projects = Project::all();

        if ( $id ) {
            $item = Blog::find($id);
        }
        return view('blogs.form', compact('item', 'projects', 'menu', 'title'));
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
        
        $items = Blog::filter( $req->all() )->orderBy('id', 'desc')->get();

        return view('blogs.table', compact('items'));
    }

    /**
     * Get the gallery view from project
     *
     */
    public function getGallery($id)
    {
        $item = Blog::find($id);

        return view('blogs.gallery', compact(['item']));
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

        $item = Blog::find($id);
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
        $project = Project::find($req->project_id);
        if (! $project ) { return response(['msg' => 'Seleccione un proyecto para continuar', 'status' => 'error'], 404); }

        $item = New Blog;

        $item->project_id = $project->id;
        $item->title      = $req->title;
        $item->content    = $req->content;
        $item->date       = $req->date;

        $item->save();

        return response(['msg' => 'Registro guardado correctamente', 'url' => url('blogs'), 'status' => 'success', 'data' => $item ], 200);
    }

    /**
     * Edit a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req)
    {
        $project = Project::find($req->project_id);
        if (! $project ) { return response(['msg' => 'Seleccione un proyecto para continuar', 'status' => 'error', 'url' => url('blogs')], 404); }

        $item = Blog::find($req->id);
        if (! $item ) { return response(['msg' => 'No se encontró el registro a editar', 'status' => 'error', 'url' => url('blogs')], 404); }

        $item->project_id = $project->id;
        $item->title      = $req->title;
        $item->content    = $req->content;
        $item->date       = $req->date;

        $item->save();
        
        return response(['msg' => 'Registro actualizado correctamente', 'url' => url('blogs'), 'status' => 'success', 'data' => $item ], 200);
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
            return response(['msg' => 'Éxito eliminando el proyecto', 'url' => url('blogs'), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Error al cambiar el status del proyecto ', 'status' => 'error', 'url' => url('blogs')], 404);
        }
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
        
        $photo = New BlogImage;

        $photo->blog_id = $req->row_id;
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
        $items = BlogImage::whereIn('id', $req->ids)
        ->get();

        foreach ($items as $item) {
            $this->deletePath( $item->path );
            $item->delete();
        }

        if ( count($items) ) {
            return response(['msg' => 'Éxito eliminando la(s) imagen(es)', 'url' => route('blog.getGallery', $req->id), 'status' => 'success'], 200);
        } else {
            return response(['msg' => 'Registro de imágenes no encontrados', 'status' => 'error', 'url' => route('blog.getGallery', $req->id)], 404);
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
