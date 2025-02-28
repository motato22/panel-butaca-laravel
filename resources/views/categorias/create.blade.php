@extends('layouts.main')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-tag-plus" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>{{ isset($categoria) ? 'Editar Categoría' : 'Nueva Categoría' }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <form
                action="{{ isset($categoria) ? route('categorias.update', $categoria->id) : route('categorias.add') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @if(isset($categoria))
                @method('PUT')
                @endif

                {{-- Vista previa de la imagen SOLO cuando se edita --}}
@if(isset($categoria) && $categoria->thumbnail)
    <div class="text-center mb-3">
        <img id="preview" src="{{ asset('storage/' . $categoria->thumbnail) }}" class="img-fluid" style="max-width: 200px; border-radius: 10px;">
    </div>
@endif


                <div class="d-flex justify-content-center align-items-center mb-5">
                    <div class="form-group custom-file col-md-6">
                        <input
                            type="file"
                            id="thumbnailFile"
                            name="thumbnailFile"
                            class="custom-file-input"
                            accept=".png,.jpg,.svg"
                            lang="es"
                            onchange="previewImage(event)">
                        <label for="thumbnailFile" class="custom-file-label text-center">
                            Imagen de la Categoría
                        </label>
                        <small class="form-text text-muted text-center">
                            Imagen cuadrada (recomendado: 512px x 512px).
                        </small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre">Nombre de la Categoría</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            class="form-control"
                            maxlength="120"
                            placeholder="Nombre de la categoría"
                            value="{{ old('nombre', $categoria->nombre ?? '') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="background">Color de fondo</label>
                        <div class="input-group">
                            <input
                                type="color"
                                id="background_color_picker"
                                class="form-control form-control-sm w-25"
                                value="{{ old('background', $categoria->background ?? '#000000') }}"
                                onchange="actualizarColor('background')">
                            <input
                                type="text"
                                id="background"
                                name="background"
                                class="form-control with-colorpicker form-control-sm"
                                placeholder="Color del fondo a mostrar en la app."
                                value="{{ old('background', $categoria->background ?? '') }}">
                        </div>
                        <small class="form-text text-muted">Color característico de la categoría.</small>
                    </div>
                </div>

                <hr>

                {{-- SECCIÓN PARA AGREGAR GÉNEROS --}}
                <h4>Agregar Géneros</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="genero_nombre">Nombre del Género</label>
                            <input type="text" id="genero_nombre" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="genero_color">Color del Género</label>
                            <div class="input-group">
                                <input type="color" id="genero_color_picker" class="form-control form-control-sm w-25"
                                    value="#000000" onchange="actualizarColor('genero_color')">
                                <input type="text" id="genero_color" class="form-control form-control-sm"
                                    placeholder="Código de color">
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary mt-3" onclick="agregarGenero()">Agregar Género</button>
                    </div>

                    <div class="col-md-6">
                        <h5>Lista de Géneros</h5>
                        <ul id="lista_generos" class="list-group">
                            @if(isset($categoria) && $categoria->generos)
                            @foreach($categoria->generos as $genero)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $genero->nombre }} - <span style="background-color:{{ $genero->color }}; padding:5px; border-radius:5px;">Color</span>
                                <button class="btn btn-danger btn-sm" onclick="eliminarGenero({{ $loop->index }})">X</button>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>

                <input type="hidden" name="generos" id="generos_json">

                <hr>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-danger float-right mx-1">
                        {{ isset($categoria) ? 'Actualizar' : 'Crear' }}
                    </button>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function actualizarColor(id) {
    let colorPicker = document.getElementById(id + '_color_picker');
    let textInput = document.getElementById(id);

    if (colorPicker && textInput) {
        textInput.value = colorPicker.value.toUpperCase();
    }
}

function previewImage(event) {
    let reader = new FileReader();
    reader.onload = function () {
        let preview = document.getElementById('preview');
        if (preview) {
            preview.src = reader.result;
        }
    };
    reader.readAsDataURL(event.target.files[0]);
}


// Asegurar que los elementos existen antes de agregar eventos
document.addEventListener("DOMContentLoaded", function () {
    let colorInputs = ['background', 'genero_color']; // IDs de los campos de color

    colorInputs.forEach(id => {
        let textInput = document.getElementById(id);
        let colorPicker = document.getElementById(id + '_picker'); // Corregido aquí para género

        if (textInput && colorPicker) {
            // Evento para actualizar el color picker cuando cambia el input de texto
            textInput.addEventListener('input', function () {
                colorPicker.value = this.value;
            });

            // Evento para actualizar el input de texto cuando cambia el color picker
            colorPicker.addEventListener('input', function () {
                textInput.value = this.value.toUpperCase();
            });

            // Sincronizar valores iniciales al cargar la página
            colorPicker.value = textInput.value;
        }
    });
});


    let generos = {!! isset($categoria) ? json_encode($categoria->generos) : '[]' !!};

    function agregarGenero() {
        let nombre = document.getElementById('genero_nombre').value;
        let color = document.getElementById('genero_color').value;

        if (!nombre) {
            alert("El nombre del género es obligatorio.");
            return;
        }

        let genero = {
            nombre: nombre,
            color: color
        };

        generos.push(genero);
        actualizarListaGeneros();
        document.getElementById('generos_json').value = JSON.stringify(generos);

        document.getElementById('genero_nombre').value = "";
        document.getElementById('genero_color').value = "#ffffff";
    }

    function actualizarListaGeneros() {
        let lista = document.getElementById('lista_generos');
        lista.innerHTML = "";

        generos.forEach((gen, index) => {
            let item = document.createElement("li");
            item.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
            item.innerHTML = `
                ${gen.nombre} - <span style="background-color:${gen.color}; padding:5px; border-radius:5px;">Color</span>
                <button class="btn btn-danger btn-sm" onclick="eliminarGenero(${index})">X</button>
            `;
            lista.appendChild(item);
        });
    }

    function eliminarGenero(index) {
        generos.splice(index, 1);
        actualizarListaGeneros();
        document.getElementById('generos_json').value = JSON.stringify(generos);
    }

    document.addEventListener("DOMContentLoaded", function () {
        actualizarListaGeneros();
    });
</script>
@endsection
