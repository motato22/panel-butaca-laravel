@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Propiedades</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('propiedades')}}"></a>Formulario</li>
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
                        <h2 class="">Complete el formulario</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('propiedades/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="form-group floating-label d-none">
                                <label>ID</label>
                                <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Nombre de la propiedad*</label>
                                    <input type="text" class="form-control not-empty" name="name" value="{{$item ? $item->name : ''}}" placeholder="" data-msg="Nombre de la propiedad">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Descripción</label>
                                    <textarea type="text" class="form-control" name="description" placeholder="" rows="6" data-msg="Descripción">{{$item ? $item->description : ''}}</textarea>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Precio*</label>
                                    <input type="text" class="form-control not-empty" name="price" value="{{$item ? $item->price : ''}}" placeholder="" data-msg="Precio de la propiedad">
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Precio mensual</label>
                                    <input type="text" class="form-control" disabled name="monthly_payment" value="{{$item ? $item->monthly_payment : ''}}" placeholder="" data-msg="Pago mensual aproximado.">
                                </div>

                                <div class="form-group {{$item && $item->photo ? 'col-md-10' : 'col-md-12' }}">
                                    <label class="label-control" for="foto">Foto principal*</label>
                                    <input type="file" class="form-control file image {{$item ? '' : 'not-empty'}}" name="photo" data-msg="Foto principal">
                                </div>

                                @if( $item && $item->photo )
                                    <div class="col-md-2 foto-preview" style="margin: auto; text-align: center;">
                                        <a data-toggle="tooltip" data-placement="top" title="" href="{{ asset($item->photo) }}" target="_blank" data-original-title="Ver foto principal"> <i class="mdi mdi-24px mdi-file-image"></i> </a>
                                        <span data-toggle="tooltip" data-row-id="{{$item->id}}" data-type="photo" class="delete-photo" style="color: firebrick;" data-placement="top" title="" href="{{ asset($item->photo) }}" target="_blank" data-original-title="Eliminar foto principal"> <i class="mdi mdi-24px mdi mdi-file-remove"></i> </span>
                                    </div>
                                @endif

                                @if( auth()->user()->role->name == 'Administrador' )
                                    <div class="form-group col-md-12">
                                        <label class="control-label" for="type">Propietario de la propiedad</label>
                                        <select id="user_id" disabled name="user_id" class="form-control not-empty select2" data-msg="Info: Propietario de evento">
                                            <option value="">Seleccione una opción</option>
                                            @if ( $item )
                                                @foreach($owners as $owner)
                                                    <option value="{{$owner->id}}" {{$item->user_id == $owner->id ? 'selected' : ''}}>{{$owner->fullname}}</option>
                                                @endforeach
                                            @else
                                                @foreach($owners as $owner)
                                                    <option value="{{$owner->id}}">{{$owner->fullname}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                @endif

                                <div class="form-group col-md-12">
                                    <label class="control-label" for="type">Proyecto*</label>
                                    <select id="project_id" name="project_id" class="form-control not-empty select2" data-msg="Proyecto">
                                        <option value="">Seleccione una opción</option>
                                        @if ( $item )
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}" {{$item->project_id == $project->id ? 'selected' : ''}}>{{$project->name}}</option>
                                            @endforeach
                                        @else
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}">{{$project->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success save m-l-5" data-form="#form-data"><i class="mdi mdi-content-save"></i> Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    // Send a request for delete photo or flyer
    $('body').delegate('.delete-photo','click', function() {
        var route = baseUrl.concat('/propiedades/delete/photo');
        var ids_array = [];
        var id = $(this).data('row-id');
        var type = $(this).data('type');

        swal({
            title: '¿Está seguro de remover este archivo? No podrá recuperarlo',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then(( accept ) => {
            if ( accept ) {
                config = {
                    'route'    : route,
                    'id'       : id,
                    'type'     : type,
                    'callback' : 'clearRemovePhotoButton',
                }
                loadingMessage('Espere un momento');
                ajaxSimple( config );
            }
        }).catch(swal.noop);
    });

    // Remove the button
    function clearRemovePhotoButton(response, config) {
        if ( config.type == 'photo' ) {
            $('.foto-preview').addClass('d-none');
            $('.foto-preview').addClass('d-none');
        }
    }
</script>
@endsection