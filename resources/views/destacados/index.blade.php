@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Eventos destacados</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar los eventos destacados en la app.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("banners/eventos-destacados")}}" data-refresh="table" data-el-loader="card">
                    
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
                        <h2 class="">Lista de banners publicitarios</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="btn btn-dark filter-rows"> <i class="mdi mdi-filter-variant"></i> Filtrar</a>
                            <a href="javascript:;" class="btn btn-info export-rows"> <i class="mdi mdi-file-excel"></i> Exportar</a>
                            {{-- <a href="{{url('banners/publicitarios/comprar')}}"><button class="btn btn-success" type="button"> <i class="mdi mdi-open-in-new"></i> Comprar espacio publicitario</button></a> --}}
                        </div>
                        <div class="row m-b-20">
                            <div class="col-md-3 my-auto">
                                <h4 class="m-0">Filtros</h4>
                            </div>
                            <div class="col-md-9 text-right my-auto filter-section">
                                <div class="btn-group row" role="group" aria-label="Basic example">
                                    <div class="no-pad col-md-4" style="text-align: left;">
                                        <select id="solo_vigentes" name="solo_vigentes" class="form-control" data-msg="Vigentes">
                                            <option value="" selected>¿Vigentes? (Cualquiera)</option>
                                            <option value="S">Sólo vigentes</option>
                                        </select>
                                    </div>
                                    <div class="no-pad col-md-4">
                                        <input type="text" class="date-picker form-control" name="fecha_inicio" autocomplete="off" placeholder="Fecha compra inicio">
                                    </div>
                                    <div class="no-pad col-md-4">
                                        <input type="text" class="date-picker form-control" name="fecha_fin" autocomplete="off" placeholder="Fecha compra fin">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('destacados.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection