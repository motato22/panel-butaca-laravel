@extends('layouts.main')

@section('content')
@include('news.modal')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Prensa</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar las noticias respecto los proyectos mostrados en la app móvil de WBC.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("prensa")}}" data-refresh="table" data-el-loader="refreshable">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            {{-- Table --}}
            <div class="col-lg-12 m-b-30">
                <div class="card refreshable">
                    <div class="card-header">
                        <h2 class="no-color">&nbsp;</h2>
                        <div class="card-controls">
                            <a href="{{url('prensa/form')}}"><button class="btn btn-success" type="button"> <i class="mdi mdi-open-in-new"></i> Nuevo registro</button></a>
                            <a href="javascript:;" class="btn btn-dark filter-rows"> <i class="mdi mdi-filter-variant"></i> Filtrar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row m-b-20">
                            <div class="col-md-3 my-auto">
                                <h4 class="m-0">Filtros</h4>
                            </div>
                            <div class="col-md-9 text-right my-auto filter-section">
                                <div class="btn-group row" role="group" aria-label="Basic example">
                                    {{-- <div class="no-pad col-md-4">
                                        <select class="form-control" name="verificado">
                                            <option value="">Verificado (Cualquiera)</option>
                                            <option value="0">No</option>
                                            <option value="1">Si</option>
                                        </select>
                                    </div> --}}
                                    <div class="no-pad col-md-6">
                                        <input type="text" class="date-picker form-control" name="fecha_inicio" autocomplete="off" placeholder="Fecha registro inicio">
                                    </div>
                                    <div class="no-pad col-md-6">
                                        <input type="text" class="date-picker form-control" name="fecha_fin" autocomplete="off" placeholder="Fecha registro fin">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive rows-container">
                            @include('news.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    // Visualizar fechas
    $('body').delegate('.view-documents', 'click', function() {
        var row = $(this).data('row');

        user          = row;
        documentacion = row.documentacion;

        // Llena la información del cliente
        if ( user ) {
            fill_text(user, null, 'user_', false);//Creador
            $('li.user-foto img').attr('src', user.foto);
        }

        // Llena la información del cliente
        if ( documentacion ) {
            fill_text(documentacion, null, 'documentacion_', false);//Creador
            $('li.documentacion-foto a.doc_foto_documento img').attr('src', baseUrl.concat('/'+documentacion.foto_documento));
            $('li.documentacion-foto a.doc_foto_documento').attr('href', baseUrl.concat('/'+documentacion.foto_documento));

            $('li.documentacion-foto a.doc_foto_personal img').attr('src', baseUrl.concat('/'+documentacion.foto_personal));
            $('li.documentacion-foto a.doc_foto_personal').attr('href', baseUrl.concat('/'+documentacion.foto_personal));
        }

        $('.verify-user-modal').data('row-id', user.id);

        $('div#view-documents').modal("show");
    });

    $('body').delegate('.verify-user-modal, .verify-user', 'click', function() {
        var refresh = $('div.general-info').data('refresh');
        var row_id = $(this).data('row-id');

        swal({
            title: 'Se verificará el usuario con el ID '+row_id+', ¿Está seguro de continuar? No podrá deshacer esta acción',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if (accept){
                config = {
                    'route'     : baseUrl.concat('/usuarios/promotores/verify'),
                    'user_id'   : row_id,
                    'refresh'   : refresh,
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    $('body').delegate('.verify-user-modal', 'click', function() {
        var refresh = $('div.general-info').data('refresh');
        var row_id = $(this).data('row-id');

        swal({
            title: 'Se verificará el usuario con el ID '+row_id+', ¿Está seguro de continuar? No podrá deshacer esta acción',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if (accept){
                config = {
                    'route'     : baseUrl.concat('/usuarios/promotores/verify'),
                    'user_id'   : row_id,
                    'refresh'   : refresh,
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    $('body').delegate('.view-apikey', 'click', function() {
        // var refresh = $('div.general-info').data('refresh');
        var promotor_id = $(this).data('row-id');

        swal({
            title: '¿Está seguro de mostrar el apikey del promotor con ID '+promotor_id+'? El apikey se reiniciará, no podrá deshacer esta acción.',
            icon: 'warning',
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if (accept){
                config = {
                    'route'    : baseUrl.concat('/usuarios/promotores/get-api-user'),
                    'user_id'  : promotor_id,
                    // 'refresh'  : refresh,
                    'callback' : 'setApirestModal',
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    function setApirestModal(response, config) {
        var data = response.data;
        var user = response.data.userApi;
        var token = response.data.token;
        
        if ( user ) {
            fill_text(user, null, 'user_', false);//Creador
            // $('li.user-foto img').attr('src', baseUrl.concat('/'+user.foto));
        }

        if ( token ) {
            fill_text(data, null, null, false);
        }
        
        $('#view-apikey-modal').modal();
    }

</script>
@endsection