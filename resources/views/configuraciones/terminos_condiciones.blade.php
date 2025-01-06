@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Configuración</h1>
                    <p class="opacity-75">
                        Aquí podrá configurar el contenido mostrado en la aplicación.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("configuracion")}}" data-refresh="table" data-el-loader="card">
                    
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
                        <h2 class="">Términos y condiciones</h2>
                    </div>
                    <div class="card-body">
                        <div class="card-title m-t-10" style="font-size: 16px;">Escriba detalladamente los términos y condiciones que se mostrarán en la aplicación</div>
                        @include('configuraciones.forms.terminos_condiciones_form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
