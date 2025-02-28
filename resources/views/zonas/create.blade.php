@extends('layouts.main')

@section('title', 'Nueva Zona recinto')

{{-- Si necesitas estilos adicionales, agrégalos aquí --}}
@section('stylesheets')
    {{-- Por ejemplo:
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    --}}
@endsection

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-map" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>Nueva Zona recinto</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                
                <div class="col-12 pt-4">

                    {{-- Mostrar errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <p><b>No se pudo guardar la zona</b>:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Formulario para crear --}}
                    <form action="{{ route('zonas.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                {{-- Campo ZONA --}}
                                <div class="form-group">
                                    <label for="zona" class="required">Zona<span style="color: red;">*</span></label>
                                    <input
                                        type="text"
                                        id="zona"
                                        name="zona"
                                        required
                                        class="form-control @error('zona') is-invalid @enderror"
                                        maxlength="120"
                                        value="{{ old('zona') }}">
                                    @error('zona')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <select
                                        id="tipo"
                                        name="tipo"
                                        class="form-control @error('tipo') is-invalid @enderror">
                                        <option value="">Sin tipo de zona</option>
                                        {{-- Ejemplo de opciones “hardcodeadas” 
                                             Si las obtienes de DB, harías un @foreach de tu TipoZona --}}
                                        <option value="3" {{ old('tipo') == 3 ? 'selected' : '' }}>Centros Universitarios</option>
                                        <option value="4" {{ old('tipo') == 4 ? 'selected' : '' }}>Auditorio</option>
                                        <option value="5" {{ old('tipo') == 5 ? 'selected' : '' }}>Auditorio José Cornejo Franco</option>
                                        <option value="6" {{ old('tipo') == 6 ? 'selected' : '' }}>Biblioteca Pública Juan José Arreola</option>
                                    </select>
                                    @error('tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger float-right mx-1">Crear</button>
                            <a href="{{ route('zonas.index') }}" class="btn btn-secondary float-right mx-1">
                                Cancelar
                            </a>
                        </div>
                    </form>

                </div> 
            </div> 
        </div> 
    </div> 
</div> 
@endsection


