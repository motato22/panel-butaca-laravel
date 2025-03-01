@extends('layouts.main')

@section('title', 'Nuevo Sitio de Interés')

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
                <h1>Nuevo Sitio de Interés</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">

                {{-- Si hay errores de validación, mostramos --}}
                @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger">
                        <p><b>No se pudo guardar el sitio de interés</b>:</p>
                        <ul>
                            @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <div class="col-12 pt-4">
                    <form action="{{ route('sitios.store') }}" method="POST">
                        @csrf

                        <div class="form-row pt-5">
                            <div class="form-group col-6">
                                <label for="nombre">Nombre<span style="color: red;">*</span></label>
                                <input
                                    type="text"
                                    name="nombre"
                                    placeholder="Ej. Buscador de google"
                                    id="nombre"
                                    class="form-control"
                                    value="{{ old('nombre') }}"
                                    required />
                                <small class="form-text text-muted">
                                    Nombre a mostrar en pantalla (cliente).
                                </small>
                            </div>

                            <div class="form-group col-6">
                                <label for="clasificacion">Clasificación</label>
                                <input
                                    type="text"
                                    name="clasificacion"
                                    placeholder="Ej. Buscadores"
                                    id="clasificacion"
                                    class="form-control"
                                    value="{{ old('clasificacion') }}" />
                                <small class="form-text text-muted">
                                    Clasificación del link. Agrupador (Opcional).
                                </small>
                            </div>
                        </div>

                        <div class="form-row pb-5">

                            <div class="form-group col-6">
                                <label for="url">URL<span style="color: red;">*</span></label>
                                <input
                                    type="url"
                                    name="url"
                                    placeholder="Ej. http://www.google.com"
                                    id="url"
                                    class="form-control"
                                    value="{{ old('url') }}"
                                    required />
                                <small class="form-text text-muted">
                                    Dirección web del sitio.
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger float-right mx-1">Crear</button>
                            <a href="{{ route('sitios.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection