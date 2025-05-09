@extends('layouts.main')

@section('title', 'Editar Zona Recinto')

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
                <h1>Editar Tipo de Zona</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            @if(session('error'))
            <div class="alert alert-danger text-center">
                <p><strong>No se pudo guardar el tipo de zona</strong>: {{ session('error') }}</p>
            </div>
            @endif

            <form action="{{ route('tipo_zona.update', $tipoZona->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <div class="form-group">
                            <label for="tipo">Nombre del tipo de zona<span style="color: red;">*</span></label>
                            <input type="text" name="tipo" id="tipo" class="form-control" value="{{ old('tipo', $tipoZona->tipo) }}">
                            @error('nombre')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-danger float-right mx-1">Guardar</button>
                    <a href="{{ route('tipo_zona.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection