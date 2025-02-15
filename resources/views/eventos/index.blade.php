@extends('layouts.main')

@section('title', 'Eventos')

@section('content')

<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-calendar-clock" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>Eventos
                    <a href="{{ route('eventos.create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
                        <i class="mdi mdi-plus mdi-18px"></i>
                    </a>
                </h1>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pull-up pb-5 mb-5">

    <div class="row mb-3">
        <div class="col-12">
            <form method="GET" action="{{ route('eventos.index') }}" class="form-inline">
                <input type="text" name="search" class="form-control mr-2" placeholder="Buscar eventos" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive p-t-10">
                        <table class="table default-table" style="width:100%" id="table_eventos">
                            <thead>
                                <tr class="text-center">
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>Recinto</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Observación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($eventos as $evento)
                                <tr class="text-center">
                                    <td>{{ $evento->id }}</td>
                                    <td>{{ $evento->nombre }}</td>
                                    <td>
                                        {{ is_object($evento->recinto) ? $evento->recinto->nombre : (is_array($evento->recinto) ? $evento->recinto['nombre'] : '') }}
                                    </td>
                                    <td>{{ $evento->fecha_inicio }}</td>
                                    <td>{{ $evento->fecha_fin }}</td>
                                    <td>
                                        <div class="btn ml-2 my-2 badge badge-soft-success">
                                            Datos mínimos completos.
                                        </div>
                                    </td>

                                    <td>
                                        <a href="{{ route('eventos.edit', $evento->id) }}" class="btn btn-sm btn-primary py-0 px-1">
                                            <i class="mdi mdi-calendar-edit mdi-18px"></i>
                                        </a>
                                        <form action="{{ route('eventos.destroy', $evento->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger py-0 px-1 action-delete">
                                                <i class="mdi mdi-calendar-remove mdi-18px"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-end mt-4">
                            {{ $eventos->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection