@extends('layouts.main')

@section('title', 'Zona de recintos')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-map-legend" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>
                    Tipo de Zonas
                    <a href="{{ route('tipo_zona.create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
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
                    <div class="table-responsive p-t-10">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>id</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zonas as $zona)
                                <tr class="text-center">
                                    <td>{{ $zona->id }}</td>
                                    <td>{{ $zona->tipo }}</td>
                                    <td>
                                        <a href="{{ route('tipo_zona.edit', $zona->id) }}" class="btn btn-sm m-b-15 ml-1 btn-primary py-0 px-1">
                                            <i class="mdi mdi-pencil mdi-18px"></i>
                                        </a>
                                        <form action="{{ route('tipo_zona.destroy', $zona->id) }}"
                                            method="POST"
                                            style="display:inline-block"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar el Tipo de zona {{ $zona->tipo }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm m-b-15 ml-1 btn-primary py-0 px-1 btn-danger">
                                                <i class="mdi mdi-minus mdi-18px"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection