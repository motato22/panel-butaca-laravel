@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Monedas</h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y modificar las monedas en curso dentro de la app móvil.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("configuracion/monedas")}}" data-refresh="table" data-el-loader="card">
                    
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
                        <h2 class="">Lista de monedas</h2>
                        <div class="card-controls">
                            <a href="{{url('configuracion/monedas/form')}}"><button class="btn btn-success" type="button"> <i class="mdi mdi-open-in-new"></i> Nuevo registro</button></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('monedas.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection