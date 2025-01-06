@extends('layouts.main')

@section('content')
@include('projects.modal')
<style type="text/css">
    /*.list-group-item:after {
        display: inline-block!important;
    }*/
    .datepicker {
      z-index: 1600 !important; /* has to be larger than 1050 */
    }
</style>
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Proyectos</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar los proyectos de la app.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("proyectos")}}" data-refresh="table" data-el-loader="card">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            {{-- Table --}}
            <div class="col-lg-12 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Lista de proyectos</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="btn btn-dark filter-rows"> <i class="mdi mdi-filter-variant"></i> Filtrar</a>
                            {{-- <a href="javascript:;" class="btn btn-info export-rows"> <i class="mdi mdi-file-excel"></i> Exportar</a> --}}
                            <a href="{{url('proyectos/form')}}"><button class="btn btn-success" type="button"> <i class="mdi mdi-open-in-new"></i> Nuevo registro</button></a>
                        </div>
                        <div class="row m-b-20">
                            <div class="col-md-3 my-auto">
                                <h4 class="m-0">Filtros</h4>
                            </div>
                            <div class="col-md-9 text-right my-auto filter-section">
                                <div class="btn-group row" role="group" aria-label="Basic example">
                                    {{-- <div class="no-pad col-md-3" style="text-align: left;">
                                        <select id="visible_en_app" name="visible_en_app" class="form-control" data-msg="Visible">
                                            <option value="" selected>Visible en app (Cualquiera)</option>
                                            <option value="S">Si</option>
                                            <option value="N">No</option>
                                        </select>
                                    </div> --}}
                                    <div class="no-pad col-md-6">
                                        <input type="text" class="date-picker form-control" name="fecha_inicio" autocomplete="off" placeholder="Fecha inicio">
                                    </div>
                                    <div class="no-pad col-md-6">
                                        <input type="text" class="date-picker form-control" name="fecha_fin" autocomplete="off" placeholder="Fecha fin">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('projects.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    // Visualizar fechas
    // $('body').delegate('.view-dates', 'click', function() {
    //     var id = $(this).parent().siblings("td:nth-child(1)").text();

    //     config = {
    //         'id'        : id,
    //         'keepModal' : true,
    //         'route'     : baseUrl.concat('/eventos/'+id),
    //         'method'    : 'GET',
    //         'callback'  : 'displayDates',
    //     }

    //     loadingMessage('Espere un momento...');

    //     ajaxSimple(config);
    // });
    
    // Agrega la fecha a la tabla
    // $('body').delegate('.agregar-fecha-tabla', 'click', function() {
    //     config = {
    //         'evento_id'   : $('.form-fecha input[name="evento_id"').val(),
    //         'moneda_id'   : $('.form-fecha select[name="moneda_id"').val(),
    //         'fecha'       : $('.form-fecha input[name="fecha"').val(),
    //         'hora'        : $('.form-fecha input[name="hora"').val(),
    //         'cupon'       : $('.form-fecha input[name="cupon"').val(),
    //         'gratis'      : $('.form-fecha input[name="gratis"').is(":checked") ? 1 : 0,
    //         'precio_bajo' : $('.form-fecha input[name="precio_bajo"').val(),
    //         'precio_alto' : $('.form-fecha input[name="precio_alto"').val(),
    //         'keepModal'   : true,
    //         'route'       : baseUrl.concat('/fechas/save'),
    //         'method'      : 'POST',
    //         'callback'    : 'configurarTrFecha',
    //     }

    //     loadingMessage('Espere un momento...');

    //     ajaxSimple(config);
    // });
</script>
@endsection