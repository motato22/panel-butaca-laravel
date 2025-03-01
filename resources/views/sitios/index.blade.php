@extends('layouts.main')

@section('title', 'Sitios de Interés')

@section('content')

    <div class="bg-dark m-b-30">
        <div class="container">
            <div class="row p-b-60 p-t-60">
                <div class="col-md-10 mx-auto text-center text-white p-b-30">
                    <div class="m-b-20">
                        <div class="avatar avatar-xl my-auto">
                            <i class="icon-placeholder mdi mdi-link" style="font-size: 5rem"></i>
                        </div>
                    </div>
                    <h1>
                        Sitios de Interés
                        <a href="{{ route('sitios.create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
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

                @if(session('success'))
                    <div class="alert alert-success text-center">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive p-t-10">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Clasificación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($sitios as $sitio)
                                    <tr id="tr_{{ $sitio->id }}">
                                        <td>
                                            {{ $sitio->nombre }}
                                            <a href="{{ $sitio->url }}" target="_blank" class="mx-1">
                                                <i class="mdi mdi-arrow-top-right-thick text-info"></i>
                                            </a>
                                        </td>
                                        <td>{{ $sitio->clasificacion }}</td>
                                        <td>
                                            {{-- Botón para eliminar --}}
                                            <form action="{{ route('sitios.destroy', $sitio->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    class="btn btn-sm ml-1 m-b-15 btn-danger py-0 px-1 action-delete"
                                                    type="submit"
                                                    onclick="return confirm('¿Deseas eliminar el sitio {{ $sitio->nombre }}?')"
                                                >
                                                    <i class="mdi mdi-link-off mdi-18px"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{-- Si quieres paginar, en lugar de all() usas paginate() y aquí pones ->links() --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
