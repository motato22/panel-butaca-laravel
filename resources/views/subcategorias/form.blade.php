@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Subcategorías</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('subcategorias')}}"></a>Formulario</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            <div class="col-lg-12 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Ingresa la descripción de la subcategoría</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('subcategorias/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="row">
                                <div class="form-group" style="display: none;">
                                    <label>ID</label>
                                    <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label" for="type">Subcategoría</label>
                                    <select id="categoria_id" name="categoria_id" class="form-control not-empty select2" data-msg="Subcategoría">
                                        <option value="" selected>Seleccione una opción</option>
                                        @if ( $item )
                                            @foreach($categorias as $subcategoria)
                                                <option value="{{$subcategoria->id}}" {{$item->categoria_id == $subcategoria->id ? 'selected' : ''}}>{{$subcategoria->nombre}}</option>
                                            @endforeach
                                        @else
                                            @foreach($categorias as $subcategoria)
                                                <option value="{{$subcategoria->id}}">{{$subcategoria->nombre}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control not-empty" name="nombre" value="{{$item ? $item->nombre : ''}}" placeholder="Nombre de la subcategoría" data-msg="Nombre">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="cstm-switch">
                                        <input type="checkbox" {{$item && $item->mostrar == 'S' ? 'checked' : (!$item ? 'checked' : '') }} name="mostrar" value="1" class="cstm-switch-input">
                                        <span class="cstm-switch-indicator bg-info "></span>
                                        <span class="cstm-switch-description">Mostrar en app</span>
                                    </label>
                                </div>
                                <div class="form-group col-md-12">
                                    <a href="{{url('subcategorias')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
                                    <button type="submit" class="btn btn-success save">Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection