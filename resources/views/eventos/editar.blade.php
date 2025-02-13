@extends('layouts.main')
@section('content')
<div class="container">
    <h1>Editar Evento</h1>
    <form action="{{ route('eventos.update', $evento->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="{{ $evento->nombre }}" required>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Foto Principal</label>
            <input type="file" class="form-control" name="foto">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection