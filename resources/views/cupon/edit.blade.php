@extends('layouts.main')

@section('title', 'Editar Cupón')

@section('content')
    <div class="bg-dark m-b-30">
        <div class="container">
            <div class="row p-b-60 p-t-60">
                <div class="col-md-10 mx-auto text-center text-white p-b-30">
                    <div class="m-b-20">
                        <div class="avatar avatar-xl my-auto">
                            <i class="icon-placeholder mdi mdi-ticket-percent" style="font-size: 5rem"></i>
                        </div>
                    </div>
                    <h1>Editar Cupón</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up pb-5 mb-5">
        <div class="card">
            <div class="card-body">

                @if(session('error'))
                    <div class="alert alert-danger text-center">
                        <p><b>No se pudo guardar la información</b>: {{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('cupon.update', $cupon->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="dominio">Dominio</label>
                        <input type="number" name="dominio" id="dominio" class="form-control" 
                               value="{{ old('dominio', $cupon->dominio) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control" required>
                            {{ old('descripcion', $cupon->descripcion) }}
                        </textarea>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                               value="{{ old('fecha_inicio', $cupon->fecha_inicio) }}">
                    </div>

                    <div class="form-group">
                        <label for="notificacoin">Notificación</label>
                        <input type="number" name="notificacoin" id="notificacoin" class="form-control" 
                               value="{{ old('notificacoin', $cupon->notificacoin) }}">
                    </div>

                    <!-- Si tuvieras guardada la imagen en DB, podrías mostrarla aquí -->
                    <!--
                    @if($cupon->image_path)
                        <div class="form-group">
                            <label>Imagen Actual</label><br>
                            <img src="{{ asset($cupon->image_path) }}" alt="Promo" style="max-height:100px;">
                        </div>
                    @endif
                    -->

                    <div class="form-group">
                        <label for="image">Cambiar Imagen</label>
                        <input type="file" name="image" id="image" class="form-control-file">
                    </div>

                    <button type="submit" class="btn btn-danger float-right mx-1">Guardar</button>
                    <a href="{{ route('cupon.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
@endsection
