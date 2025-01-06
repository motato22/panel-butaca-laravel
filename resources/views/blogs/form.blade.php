@extends('layouts.main')

@section('content')
@include('general_views.upload-images')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Blogs</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90 general-info" data-url="{{url("blogs")}}" data-refresh="galery" data-main-id="{{$item ? $item->id : ''}}">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('blogs')}}"></a>Formulario</li>
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
                        <form id="form-data" action="{{url('blogs/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div id="formWizard">

                                <ul class="nav nav-pills nav-justified">
                                    <li class="nav-item"><a class="nav-link active" href="#tabInfo" data-toggle="tab">Información general</a></li>
                                    @if($item)
                                    <li class="nav-item"><a class="nav-link" href="#tabPhotos" data-toggle="tab">Galería de fotos</a></li>
                                    @endif
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane p-t-20 p-b-20 active" id="tabInfo">

                                        <div class="form-group floating-label d-none">
                                            <label>ID</label>
                                            <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label>Título*</label>
                                                <input type="text" class="form-control not-empty" name="title" value="{{$item ? $item->title : ''}}" placeholder="" data-msg="Título">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label id="date">Fecha de publicación*</label>
                                                <input type="text" class="form-control date-picker not-empty" name="date" value="{{$item ? $item->date : ''}}" placeholder="" data-msg="Fecha de publicación">
                                            </div>

                                            <div class="form-group col-md-6">
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

                                            <div class="form-group col-md-12">
                                                <label>Contenido*</label>
                                                <textarea id="summernote" name="content" class="form-control not-empty" placeholder="Escriba cualquier cosa..." data-msg="Contenido">{{$item ? $item->content : ''}}</textarea>
                                            </div>

                                        </div>
                                    </div>
                                    @if( $item && $item->id )
                                        <div class="tab-pane fade" id="tabPhotos" role="tabpanel">
                                            @if( $item->photos->count() )
                                            <div class="card-title m-t-10" style="font-size: 16px;">Seleccione imágenes para eliminarlas</div>
                                            @endif
                                            <div class="card-controls">
                                                <a href="javascript:;" class="btn btn-dark upload-content" data-refresh="galery" data-row-id="{{$item->id}}" data-route-action="{{route("blog.uploadContent")}}" data-rename="true" data-reload-url="{{route('blog.getGallery',$item->id)}}" data-path="img/blogs/{{$item->id}}" {{-- data-resize='{"width": 338, "height": 217}' --}} data-toggle="modal" data-target="#modal-upload-content">Cargar fotos</a>
                                                <a href="javascript:;" class="btn btn-danger delete-content disabled" data-refresh="galery" data-route-action="{{route("blog.deleteContent")}}">Eliminar imágenes</a>
                                            </div>
                                            <div class="galery-collection galery-container row">
                                                @include('blogs.gallery')
                                            </div>
                                        </div>
                                    @endif
                                    {{-- <div class="tab-pane fade p-t-20 p-b-20" id="tabPhotos">
                                    </div> --}}
                                </div>

                                <ul class="nav nav-pills {{-- justify-content-between  --}} wizard m-b-30">
                                    @if( $item && $item->id )
                                    <li><button class="btn btn-secondary previo m-l-5 disabled" href="#!"> <i class="mdi mdi-arrow-left-bold"></i> Previo</button></li>
                                    <li><button class="btn btn-secondary siguiente m-l-5" href="#!"><i class="mdi mdi-arrow-right-bold"></i> Siguiente</button></li>
                                    @endif
                                    <li><button type="submit" class="btn btn-success save m-l-5" data-form="#form-data"><i class="mdi mdi-content-save"></i> Guardar</button></li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="{{ asset('vendor/jquery.bootstrap.wizard/jquery.bootstrap.wizard.min.js')}}"></script>
<script type="text/javascript">
    (function ($) {
        'use strict';
        $(document).ready(function () {
            $('#formWizard').bootstrapWizard({
                'tabClass': 'nav nav-pills',
                'onNext': function (tab, navigation, index) {
                    
                },
                'onTabShow': function (tab, navigation, index) {
                    var size = $('#formWizard').bootstrapWizard('navigationLength');
                    var index = $('#formWizard').bootstrapWizard('currentIndex');

                    // console.warn(size, index);
                    // Se oculta el botón previo
                    if ( index == 0 ) {
                        $('.previo').addClass('disabled');
                    } else {
                        $('.previo').removeClass('disabled');
                    }

                    // Se oculta el botón de siguiente
                    if ( $('#formWizard').bootstrapWizard('currentIndex') == size ) {
                        $('.siguiente').addClass('disabled');
                    } else {
                        $('.siguiente').removeClass('disabled');
                    }
                },
            });
        });
    })(window.jQuery);

    //Verify if the button for delete multiple can be clickable
    $('body').delegate('.check-multiple','click', function() {
        if ($(this).is(':checked')) {
            id_photos.push($(this).data('row-id'));
        } else {
            var index = id_photos.indexOf($(this).data('row-id'));
            if (index > -1) {
              id_photos.splice(index, 1);
            }
        }

        $('.delete-content').attr('disabled', id_photos.length > 0 ? false : true);
        
        if ( id_photos.length > 0 ) {
            $('.delete-content').removeClass('disabled');
        } else {
            $('.delete-content').addClass('disabled');
        }
    });

    $('body').delegate('.delete-content','click', function() {
        var route = $('div.general-info').data('url')+'/delete-content';
        console.log(route);
        var refresh = $('div.general-info').data('refresh');
        var main_id = $('div.general-info').data('main-id');
        swal({
            title: 'Se eliminarán '+id_photos.length+' imágenes, ¿Está seguro de continuar?',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if ( accept ) {
                config = {
                    'route'    : route,
                    'id'       : main_id,
                    'ids'      : id_photos,
                    /*'refresh'  : refresh,*/
                    'callback' : 'fillGalery',
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    // Send a request for delete photo or flyer
    $('body').delegate('.delete-photo','click', function() {
        var route = baseUrl.concat('/blogs/delete/photo');
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

    // // Remove the button
    // function clearRemovePhotoButton(response, config) {
    //     if ( config.type == 'photo' ) {
    //         $('.foto-preview').addClass('d-none');
    //         $('.foto-preview').addClass('d-none');
    //     } else if ( config.type == 'logo' ) {
    //         $('.logo-preview').addClass('d-none');
    //         $('.logo-preview').addClass('d-none');
    //     }
    // }

    $('body').delegate('.previo', 'click', function() {
        $('#formWizard').bootstrapWizard('previous');
    });

    $('body').delegate('.siguiente', 'click', function() {
        $('#formWizard').bootstrapWizard('next');
    });
</script>
@endsection