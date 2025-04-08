@extends('layouts.main')

@section('title', 'Recinto - Galería')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-theater" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>{{ $recinto->nombre }}</h1>
                <h4>Galería</h4>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pull-up pb-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Formulario para agregar imágenes -->
                    <form id="uploadForm" method="POST" action="{{ route('recintos.storeImages', $recinto->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-8 col-lg-6 text-center offset-md-2 offset-lg-3">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <label class="custom-file-label" for="galeria">Seleccione una imagen</label>
                                        <input type="file" id="galeria" name="galeria[]" class="custom-file-input" multiple
                                            accept=".png,.jpg,.svg" lang="es">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-8 col-lg-6 text-center offset-md-2 offset-lg-3">
                                <button type="submit" class="btn btn-primary">Agregar</button>
                            </div>
                        </div>
                    </form>

                    <br>

                    <!-- Tabla de imágenes -->
                    <div class="table-responsive p-t-10">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>Imagen</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($miGaleria as $imagen)
                                <tr class="text-center">
                                    <td><img src="{{ asset('storage/uploads/recintos/' . $imagen->image) }}" height="80" alt=""></td>
                                    <td>
                                        <form action="{{ route('recintos.deleteImage', ['recinto' => $recinto->id, 'imagen' => $imagen->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta imagen?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No hay imágenes en la galería.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> <!-- End Table -->
                    <div class="text-center mt-4">
                        <a href="{{ route('recintos.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Regresar a Recintos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("uploadForm").addEventListener("submit", function(e) {
            let inputFile = document.getElementById("galeria");
            if (inputFile.files.length === 0) {
                e.preventDefault();
                alert("Debes subir al menos una imagen antes de continuar.");
            }
        });
    });
</script>