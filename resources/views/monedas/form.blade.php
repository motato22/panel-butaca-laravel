@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Moneda</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('configuracion/monedas')}}"></a>Formulario</li>
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
                        <h2 class="">Complete el siguiente formulario</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('configuracion/monedas/'.($item ? 'update' : 'save'))}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="form-group floating-label" style="display: none;">
                                <label>ID</label>
                                <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                            </div>
                            <div class="form-group">
                                <label>Nombre</label>
                                <input type="text" class="form-control not-empty" name="nombre" value="{{$item ? $item->nombre : ''}}" placeholder="Pesos..." data-msg="Moneda">
                            </div>
                            <div class="form-group">
                                <label>ISO</label>
                                <input type="text" class="form-control not-empty" name="iso" value="{{$item ? $item->iso : ''}}" placeholder="Código de moneda..." data-msg="ISO">
                            </div>
                            <div class="form-group m-t-15">
                                <a href="{{url('configuracion/monedas')}}"><button type="button" class="btn btn-primary">Regresar</button></a>
                                <button type="submit" class="btn btn-success save">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection