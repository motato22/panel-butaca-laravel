@extends('layouts.main')
@section('content')
<div class="container">
    <h1>Agregar Imágenes al Evento</h1>
    <form action="{{ route('eventos.addImages', $evento->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="galeria" class="form-label">Subir Imágenes</label>
            <input type="file" class="form-control" name="galeria[]" multiple required>
        </div>
        <button type="submit" class="btn btn-success">Subir</button>
    </form>
    <h2>Galería</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($miGaleria as $imagen)
            <tr>
                <td><img src="{{ asset('storage/' . $imagen->image) }}" width="100"></td>
                <td>
                    <form action="{{ route('eventos.deleteImage', $imagen->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection