@extends('layouts.main')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-theater" style="font-size: 5rem;"></i>
                    </div>
                </div>
                <h1>Recintos
                    <a href="{{ url('recintos/create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
                        <i class="mdi mdi-plus mdi-18px"></i>
                    </a>
                </h1>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pull-up pb-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="text-uppercase text-muted">
                                    <th>Nombre</th>
                                    <th>Zona</th>
                                    <th>Facebook</th>
                                    <th>Twitter</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recintos as $recinto)
                                <tr>
                                    <td>{{ $recinto->nombre }}</td>
                                    <td>{{ $recinto->zona->zona ?? 'Sin zona asignada' }}</td>
                                    <td>
                                        @if($recinto->facebook)
                                        <a href="{{ $recinto->facebook }}" target="_blank">
                                            {{ $recinto->facebook }}
                                        </a>
                                        @else
                                        Sin enlace
                                        @endif
                                    </td>
                                    <td>
                                        @if($recinto->twitter)
                                        <a href="{{ $recinto->twitter }}" target="_blank">
                                            {{ $recinto->twitter }}
                                        </a>
                                        @else
                                        Sin enlace
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column align-items-start">
                                            <div class="mb-2">
                                                <a href="{{ url('recintos/' . $recinto->id . '/edit') }}"
                                                    class="btn btn-primary btn-sm"
                                                    title="Editar">
                                                    <i class="mdi mdi-pencil me-2"></i>
                                                </a>
                                                <a href="{{ url('recintos/' . $recinto->id . '/delete') }}"
                                                    class="btn btn-danger btn-sm"
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Estás seguro de eliminar este recinto?')">
                                                    <i class="mdi mdi-minus me-2"></i>
                                                </a>
                                            </div>
                                            <div>
                                                <a href="#"
                                                    class="btn btn-warning btn-sm"
                                                    title="Ver detalles">
                                                    <i class="mdi mdi-account-group me-2"></i>
                                                </a>
                                                <a href="#"
                                                    class="btn btn-info btn-sm"
                                                    title="Configurar">
                                                    <i class="mdi mdi-image-plus me-2"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ $recintos->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection