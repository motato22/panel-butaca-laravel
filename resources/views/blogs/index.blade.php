@extends('layouts.main')

@section('content')
@include('blogs.modal')
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
                    <h1>Blogs</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar los blogs de los proyectos de WBC.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("blogs")}}" data-refresh="table" data-el-loader="card">
                    
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
                        <h2 class="">Lista de blogs</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="btn btn-dark filter-rows"> <i class="mdi mdi-filter-variant"></i> Filtrar</a>
                            <a href="{{url('blogs/form')}}"><button class="btn btn-success" type="button"> <i class="mdi mdi-open-in-new"></i> Nuevo registro</button></a>
                        </div>
                        <div class="row m-b-20">
                            <div class="col-md-3 my-auto">
                                <h4 class="m-0">Filtros</h4>
                            </div>
                            <div class="col-md-9 text-right my-auto filter-section">
                                <div class="btn-group row" role="group" aria-label="Basic example">
                                    <div class="no-pad col-md-4" style="text-align: left;">
                                        <select id="project_id" name="project_id" class="form-control select2" data-msg="Proyecto">
                                            <option value="" selected>Proyecto (Cualquiera)</option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}">{{$project->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad col-md-4">
                                        <input type="text" class="date-picker form-control" name="fecha_inicio" autocomplete="off" placeholder="Fecha inicio">
                                    </div>
                                    <div class="no-pad col-md-4">
                                        <input type="text" class="date-picker form-control" name="fecha_fin" autocomplete="off" placeholder="Fecha fin">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('blogs.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    
</script>
@endsection