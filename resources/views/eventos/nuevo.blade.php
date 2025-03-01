@extends('layouts.main')

@section('title', isset($evento) ? 'Editar Evento' : 'Nuevo Evento')

@section('stylesheets')
<link rel="stylesheet" href="{{ asset('css/dropzone.css') }}">
@endsection

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-calendar-clock" style="font-size: 5rem"></i>
                    </div>
                </div>
                @if (isset($evento->id) && $evento->id)
                <h1>Editar Evento</h1>
                @else
                <h1>Nuevo Evento</h1>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-12 text-right">
                    {{-- Espacio para botones o herramientas (si lo necesitas) --}}
                </div>

                {{-- Mostrar errores de validación --}}
                @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger" style="margin-left: auto; margin-right: auto">
                        <p><b>No se pudo guardar el evento</b>:</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <div class="col-12 pt-4">

                    {{--
                        Si existe $evento, estamos en modo edición; 
                        caso contrario, en modo creación.
                    --}}
                    @if (isset($evento->id) && $evento->id)
                            <form action="{{ route('eventos.update', $evento->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                        @endif

                            {{-- Imagen del evento --}}
                            <div class="form-row">
                                <div class="col-12 col-md-8 col-lg-6 text-center offset-md-2 offset-lg-3">
                                    @php
                                    // Si existe un evento y tiene foto, la mostramos
                                    $currentImage = isset($evento) && $evento->foto
                                    ? asset('storage/eventos/' . $evento->foto)
                                    : null;
                                    @endphp

                                    @if ($currentImage)
                                        <img class="img-thumbnail max-height-6 mb-2"
                                            src="{{ asset('storage/eventos/' . $currentImage) }}"
                                            alt="Imagen del evento">
                                    @else
                                        <img class="img-thumbnail max-height-6 mb-2"
                                            src="{{ asset('img/placeholder.png') }}"
                                            alt="Sin imagen">
                                    @endif

                                    <div class="input-group mb-3">
                                        @if (!isset($evento->id))
                                            <input type="file" name="foto" class="custom-file-input" id="foto" required>
                                        @else
                                            <input type="file" name="foto" class="custom-file-input" id="foto">
                                        @endif
                                        <label class="custom-file-label" for="foto">Seleccionar imagen</label>
                                        <small class="form-text text-muted">
                                            Imagen cuadrada (recomendado: 512 x 512).
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Nombre y Recinto --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nombre">
                                        Nombre del evento <span style="color: red;">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="nombre"
                                        name="nombre"
                                        class="form-control @error('nombre') is-invalid @enderror"
                                        placeholder="Nombre del evento"
                                        value="{{ old('nombre', $evento->nombre ?? '') }}"
                                        required>
                                    @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="recinto" class="required">
                                        Recinto <span style="color: red;">*</span>
                                    </label>
                                    <select
                                        id="recinto"
                                        name="recinto"
                                        class="form-control @error('recinto') is-invalid @enderror">
                                        <option value="">Selecciona un recinto</option>
                                        @foreach ($recintos as $r)
                                        <option
                                            value="{{ $r->id }}"
                                            {{ old('recinto', $evento->recinto ?? '') == $r->id ? 'selected' : '' }}>
                                            {{ $r->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Recinto para el cual se configura este evento
                                    </small>
                                    @error('recinto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Selección de tipo de horario --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tipo de Horario <span style="color: red;">*</span></label>
                                    <select
                                        id="tipo_horario"
                                        name="tipo_horario"
                                        class="form-control">
                                        <option value="" disabled
                                            {{ !old('tipo_horario') && !isset($evento->tipo_horario) ? 'selected' : '' }}>
                                            -- Selecciona un tipo --
                                        </option>
                                        <option value="temporada"
                                            {{ old('tipo_horario', $evento->tipo_horario ?? '') == 'temporada' ? 'selected' : '' }}>
                                            Temporada (varios días con horarios iguales)
                                        </option>
                                        <option value="funciones"
                                            {{ old('tipo_horario', $evento->tipo_horario ?? '') == 'funciones' ? 'selected' : '' }}>
                                            Funciones (varios días con uno o varios horarios)
                                        </option>
                                        <option value="unico_dia"
                                            {{ old('tipo_horario', $evento->tipo_horario ?? '') == 'unico_dia' ? 'selected' : '' }}>
                                            Único día (un solo día con un solo horario)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- Contenedor para horarios (se llenará con JS) --}}
                            <div id="contenedor_horarios"></div>
                            <hr />

                            <!-- Filtro con dos selects -->
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="selectCategoria">Categoría <span style="color:red">*</span></label>
                                    <select id="selectCategoria" class="form-control">
                                        <option value="">-- Selecciona una categoría --</option>
                                        @foreach ($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="selectGenero">Género</label>
                                    <select id="selectGenero" class="form-control">
                                        <option value="">-- Selecciona un género --</option>
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary" id="btnAgregar">
                                        Agregar
                                    </button>
                                </div>
                            </div>

                            <!-- Tabla de géneros asociados -->
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>CATEGORÍAS</th>
                                        <th>GÉNEROS</th>
                                        <th>ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaGeneros">
                                    @foreach ($evento->generos as $genero)
                                    <tr data-genero-id="{{ $genero->id }}">
                                        <td>{{ $genero->categoria->nombre }}</td>
                                        <td>{{ $genero->nombre }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btnBorrar">
                                                Borrar
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Precio del evento --}}
                            <h4 class="pt-0 pb-3">Precio del evento</h4>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="cstm-switch m-0">
                                        {{-- Campo hidden para controlar check --}}
                                        <input type="hidden" name="es_gratuito" value="0">
                                        <input
                                            type="checkbox"
                                            id="es_gratuito"
                                            name="es_gratuito"
                                            class="cstm-switch-input"
                                            value="1"
                                            {{ old('es_gratuito', $evento->es_gratuito ?? false) ? 'checked' : '' }}>
                                        <span class="cstm-switch-indicator bg-success"></span>
                                        <span class="cstm-switch-description">¿Este evento es gratuito?</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="precio_bajo">Precio más bajo</label>
                                    <input
                                        type="number"
                                        id="precio_bajo"
                                        name="precio_bajo"
                                        class="form-control"
                                        placeholder="Precio más bajo"
                                        value="{{ old('precio_bajo', $evento->precio_bajo ?? '') }}">
                                    <small class="form-text text-muted">Valor más barato del boleto.</small>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="precio_alto">Precio más alto</label>
                                    <input
                                        type="number"
                                        id="precio_alto"
                                        name="precio_alto"
                                        class="form-control"
                                        placeholder="Precio más alto"
                                        value="{{ old('precio_alto', $evento->precio_alto ?? '') }}">
                                    <small class="form-text text-muted">Valor más alto del boleto.</small>
                                </div>
                            </div>

                            <hr class="pb-3 mt-0" />

                            {{-- Descripción y URL de compra --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="descripcion">Descripción <span style="color: red;">*</span></label>
                                    <textarea
                                        id="descripcion"
                                        name="descripcion"
                                        class="form-control">{{ old('descripcion', $evento->descripcion ?? '') }}</textarea>
                                    <small class="form-text text-muted">
                                        Información adicional que quiera que los usuarios conozcan del evento.
                                    </small>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="url_compra">URL de compra <span style="color: red;">*</span></label>
                                    <input
                                        type="text"
                                        id="url_compra"
                                        name="url_compra"
                                        class="form-control"
                                        placeholder="http://"
                                        value="{{ old('url_compra', $evento->url_compra ?? '') }}">
                                    <small class="form-text text-muted">
                                        URL a la cual se redirigirá para la compra de boletos.
                                    </small>
                                </div>
                            </div>

                            {{-- Texto promocional y video --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="texto_promocional">Texto de promoción (opcional)</label>
                                    <select
                                        id="texto_promocional"
                                        name="texto_promocional"
                                        class="form-control js-select2">
                                        <option value="">Sin promoción</option>
                                        @foreach ($promociones as $promo)
                                        <option value="{{ $promo }}"
                                            {{ old('texto_promocional', $evento->texto_promocional ?? '') == $promo ? 'selected' : '' }}>
                                            {{ $promo }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Texto que indica en qué consiste la promoción.
                                    </small>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="video">Video Promocional (YouTube Link)</label>
                                    <input
                                        type="text"
                                        id="video"
                                        name="video"
                                        class="form-control"
                                        value="{{ old('video', $evento->video ?? '') }}">
                                    <small class="form-text text-muted">
                                        URL del video de YouTube promocional del evento.
                                    </small>
                                </div>
                            </div>

                            {{-- Website --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="web">Website</label>
                                    <input
                                        type="text"
                                        id="web"
                                        name="web"
                                        class="form-control"
                                        value="{{ old('web', $evento->web ?? '') }}">
                                </div>
                            </div>

                            <hr />

                            {{-- Redes Sociales --}}
                            <h4 class="pt-0 pb-3">Redes Sociales</h4>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="instagram">Instagram</label>
                                    <input
                                        type="text"
                                        id="instagram"
                                        name="instagram"
                                        class="form-control"
                                        value="{{ old('instagram', $evento->instagram ?? '') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="facebook">Facebook</label>
                                    <input
                                        type="text"
                                        id="facebook"
                                        name="facebook"
                                        class="form-control"
                                        value="{{ old('facebook', $evento->facebook ?? '') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="youtube">YouTube</label>
                                    <input
                                        type="text"
                                        id="youtube"
                                        name="youtube"
                                        class="form-control"
                                        value="{{ old('youtube', $evento->youtube ?? '') }}">
                                    <small class="form-text text-muted">
                                        URL del canal de YouTube para el evento u organizadores. (opcional)
                                    </small>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="twitter">Twitter</label>
                                    <input
                                        type="text"
                                        id="twitter"
                                        name="twitter"
                                        class="form-control"
                                        value="{{ old('twitter', $evento->twitter ?? '') }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="snapchat">Snapchat</label>
                                    <input
                                        type="text"
                                        id="snapchat"
                                        name="snapchat"
                                        class="form-control"
                                        value="{{ old('snapchat', $evento->snapchat ?? '') }}">
                                </div>
                            </div>

                            {{-- Botón "Crear" o "Actualizar" --}}
                            <div class="form-group">
                                @if (isset($evento->id) && $evento->id)
                                    <button type="submit" class="btn btn-danger float-right mx-1">Actualizar</button>
                                @else
                                    <button type="submit" class="btn btn-success float-right mx-1">Crear</button>
                                @endif
                                <a href="{{ route('eventos.index') }}" class="btn btn-secondary float-right mx-1">
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

@section('scripts')

@php
    $oldFechaInicioTemporada     = old('fecha_inicio_temporada', '');
    $oldFechaFinTemporada        = old('fecha_fin_temporada', '');
    $oldHorarioTemporadaInicio   = old('horario_temporada_inicio', '');
    $oldHorarioTemporadaFin      = old('horario_temporada_fin', '');
    $oldFechaUnico               = old('fecha_unico', '');
@endphp

<script>
    
    let horarios = @json(isset($evento) ? $evento->horario : []);
        if (!horarios) {
        horarios = {}; // o []
    }

    // Variables en JavaScript para usar en los template strings
    let oldFechaInicioTemporada   = "{{ $oldFechaInicioTemporada }}";
    let oldFechaFinTemporada      = "{{ $oldFechaFinTemporada }}";
    let oldHorarioTemporadaInicio = "{{ $oldHorarioTemporadaInicio }}";
    let oldHorarioTemporadaFin    = "{{ $oldHorarioTemporadaFin }}";
    let oldFechaUnico             = "{{ $oldFechaUnico }}";

    // Cuando cargue el DOM, disparamos 'change' en #tipo_horario para renderizar el bloque correcto
    document.addEventListener("DOMContentLoaded", function() {
        let tipoHorarioSelect = document.getElementById("tipo_horario");
        if (tipoHorarioSelect) {
            tipoHorarioSelect.dispatchEvent(new Event('change'));
        }
    });

    // Al cambiar el select de tipo de horario
    document.getElementById("tipo_horario").addEventListener("change", function() {
        let tipo = this.value;
        let contenedor = document.getElementById("contenedor_horarios");
        contenedor.innerHTML = "";

        if (tipo === "temporada") {
            contenedor.innerHTML = `
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Fecha de inicio <span style="color: red;">*</span></label>
                        <input type="date" name="fecha_inicio_temporada" class="form-control"
                               value="${oldFechaInicioTemporada}">
                        <small class="form-text text-muted text-right">Fecha de Inicio de la Temporada</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Fecha de fin <span style="color: red;">*</span></label>
                        <input type="date" name="fecha_fin_temporada" class="form-control"
                               value="${oldFechaFinTemporada}">
                        <small class="form-text text-muted text-right">Fecha de Término de la Temporada</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Días con actividad <span style="color: red;">*</span></label>
                    <div>
                        ${["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"].map(dia => {
                            return `<label><input type="checkbox" name="dias_temporada[]" value="${dia}"> ${dia}</label> | `;
                        }).join('')}
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Horario de Inicio <span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_inicio" class="form-control"
                               value="${oldHorarioTemporadaInicio}">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Horario de Término <span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_fin" class="form-control"
                               value="${oldHorarioTemporadaFin}">
                    </div>
                </div>
            `;
        }
        else if (tipo === "funciones") {
            contenedor.innerHTML = `
                <div id="lista_funciones" class="form-row">
                    <div class="form-group col-md-6">
                        <label>Horarios <span style="color: red;">*</span></label>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="date" id="fecha_funcion" class="form-control mr-2">
                                <small class="form-text text-muted text-right">Fecha de la Función</small>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="time" id="hora_funcion" class="form-control mr-2">
                                <small class="form-text text-muted text-right">Horario de Inicio</small>
                            </div>
                            <div class="form-group col-md-1">
                                <button type="button" class="btn btn-primary" id="btnAgregarFuncion">+</button>
                            </div>
                        </div>
                    </div>
                    <!-- Contenedor donde se mostrarán las funciones agregadas -->
                    <div class="form-group col-md-6">
                        <div id="lista_horarios">
                            <h5>Lista de horarios</h5>
                        </div>
                    </div>
                </div>
                <!-- Input oculto para enviar los horarios en el formulario -->
                <input type="hidden" name="horarios" id="horarios_json">
            `;
            // Renderiza los horarios existentes (si estamos en edición)
            renderizarHorarios();
        }
        else if (tipo === "unico_dia") {
            contenedor.innerHTML = `
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Fecha del evento <span style="color: red;">*</span></label>
                        <input type="date" name="fecha_unico" class="form-control"
                               value="${oldFechaUnico}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Horario de Inicio <span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_inicio" class="form-control"
                               value="${oldHorarioTemporadaInicio}">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Horario de Término <span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_fin" class="form-control"
                               value="${oldHorarioTemporadaFin}">
                    </div>
                </div>
            `;
        }
    });

    // Lógica para agregar funciones (cuando tipo=funciones)
    document.addEventListener("click", function(event) {
        if (event.target && event.target.id === "btnAgregarFuncion") {
            let fechaInput = document.getElementById("fecha_funcion").value;
            let hora       = document.getElementById("hora_funcion").value;

            if (!fechaInput || !hora) {
                alert("Selecciona una fecha y una hora.");
                return;
            }

            let fechaCorregida = fechaInput;

            if (!horarios[fechaCorregida]) {
                horarios[fechaCorregida] = [];
            }
            if (!horarios[fechaCorregida].includes(hora)) {
                horarios[fechaCorregida].push(hora);
                renderizarHorarios();
            }
        }
    });

    // Eliminar un horario de una fecha
    function eliminarHorario(fecha, hora) {
        horarios[fecha] = horarios[fecha].filter(h => h !== hora);
        if (horarios[fecha].length === 0) {
            delete horarios[fecha];
        }
        renderizarHorarios();
    }

    // Eliminar fecha completa
    function eliminarFecha(fecha) {
        delete horarios[fecha];
        renderizarHorarios();
    }

    // Renderizar la lista de horarios en #lista_horarios
    function renderizarHorarios() {
        let contenedor = document.getElementById("lista_horarios");
        if (!contenedor) return;

        contenedor.innerHTML = "<h5>Lista de horarios</h5>";

        Object.keys(horarios).forEach(fecha => {
            let divFecha = document.createElement("div");
            divFecha.classList.add("p-3", "mb-2", "shadow-sm", "border", "rounded");

            let rowDiv = document.createElement("div");
            rowDiv.classList.add("row", "align-items-center");

            let colBorrar = document.createElement("div");
            colBorrar.classList.add("col-md-3", "text-left");

            let btnEliminarFecha = document.createElement("button");
            btnEliminarFecha.classList.add("btn", "btn-danger", "btn-sm", "w-100");
            btnEliminarFecha.innerText = "Borrar";
            btnEliminarFecha.onclick = () => eliminarFecha(fecha);
            colBorrar.appendChild(btnEliminarFecha);

            let colContenido = document.createElement("div");
            colContenido.classList.add("col-md-9");

            let titulo = document.createElement("div");
            titulo.classList.add("font-weight-bold", "mb-2");

            let [yyyy, mm, dd] = fecha.split("-");
            let dateObj = new Date(yyyy, mm - 1, dd, 12); // 12 = mediodía

            titulo.innerHTML = dateObj.toLocaleDateString("es-ES", {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });

            let horariosDiv = document.createElement("div");
            horariosDiv.classList.add("d-flex", "flex-wrap");

            horarios[fecha].forEach(hora => {
                let badge = document.createElement("span");
                badge.classList.add("badge", "badge-secondary", "p-2", "m-1", "d-flex", "align-items-center");

                let closeButton = document.createElement("button");
                closeButton.classList.add("btn", "btn-sm", "btn-light", "ml-1", "p-0");
                closeButton.innerHTML = "✖";
                closeButton.onclick = () => eliminarHorario(fecha, hora);

                badge.innerHTML = `${hora} `;
                badge.appendChild(closeButton);
                horariosDiv.appendChild(badge);
            });

            colContenido.appendChild(titulo);
            colContenido.appendChild(horariosDiv);

            rowDiv.appendChild(colBorrar);
            rowDiv.appendChild(colContenido);
            divFecha.appendChild(rowDiv);

            contenedor.appendChild(divFecha);
        });

        // Guardar en el input hidden
        let hiddenField = document.getElementById("horarios_json");
        if (hiddenField) {
            hiddenField.value = JSON.stringify(horarios);
        }
    }

    @if(isset($evento->id))
        // ========== LÓGICA DE EDICIÓN (AJAX) ==========
        const eventoId        = "{{ $evento->id }}";
        const urlFetchGeneros = "{{ url('categorias') }}/"; 
        const urlAttachGenero = "{{ route('eventos.attachGenero', $evento->id) }}";
        const urlDetachGenero = "{{ url('eventos') }}/" + eventoId + "/generos/";

        document.addEventListener('DOMContentLoaded', function() {
            const selectCategoria = document.getElementById('selectCategoria');
            const selectGenero    = document.getElementById('selectGenero');
            const btnAgregar      = document.getElementById('btnAgregar');
            const tablaGeneros    = document.getElementById('tablaGeneros');

            // Cargar géneros por categoría (para el <select>)
            selectCategoria.addEventListener('change', () => {
                const categoriaId = selectCategoria.value;
                if (!categoriaId) {
                    selectGenero.innerHTML = '<option value="">-- Selecciona un género --</option>';
                    return;
                }

                fetch(urlFetchGeneros + categoriaId + '/generos')
                    .then(res => res.json())
                    .then(data => {
                        let opciones = '<option value="">-- Selecciona un género --</option>';
                        data.forEach(g => {
                            opciones += `<option value="${g.id}">${g.nombre}</option>`;
                        });
                        selectGenero.innerHTML = opciones;
                    })
                    .catch(err => console.error(err));
            });

            // Al hacer clic en "Agregar", POST a attachGenero
            btnAgregar.addEventListener('click', () => {
                const generoId = selectGenero.value;
                if (!generoId) {
                    alert("Selecciona un género primero");
                    return;
                }

                fetch(urlAttachGenero, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ genero_id: generoId })
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            // Cargamos info del género y lo inyectamos en la tabla
                            fetch(`/api/generos/${generoId}`)
                                .then(r => r.json())
                                .then(gen => {
                                    let fila = document.createElement('tr');
                                    fila.setAttribute('data-genero-id', gen.id);
                                    fila.innerHTML = `
                                        <td>${gen.categoria.nombre}</td>
                                        <td>${gen.nombre}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btnBorrar">Borrar</button>
                                        </td>
                                    `;
                                    tablaGeneros.appendChild(fila);
                                });
                        } else {
                            alert(response.message || 'Error al agregar género');
                        }
                    })
                    .catch(err => console.error(err));
            });

            // Borrar género (detach)
            tablaGeneros.addEventListener('click', (e) => {
                if (e.target.classList.contains('btnBorrar')) {
                    let fila = e.target.closest('tr');
                    let generoId = fila.dataset.generoId;

                    if (confirm('¿Seguro que deseas quitar este género?')) {
                        fetch(urlDetachGenero + generoId, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => res.json())
                            .then(resp => {
                                if (resp.success) {
                                    fila.remove();
                                } else {
                                    alert(resp.message || 'Error al borrar');
                                }
                            })
                            .catch(err => console.error(err));
                    }
                }
            });
        });
    @else
        // ========== LÓGICA DE CREACIÓN (SIN AJAX) ==========
        document.addEventListener('DOMContentLoaded', function() {
            const selectCategoria = document.getElementById('selectCategoria');
            const selectGenero    = document.getElementById('selectGenero');
            const btnAgregar      = document.getElementById('btnAgregar');
            const tablaGeneros    = document.getElementById('tablaGeneros');

            // Cargar géneros para el <select> según la categoría
            selectCategoria.addEventListener('change', () => {
                const categoriaId = selectCategoria.value;
                if (!categoriaId) {
                    selectGenero.innerHTML = '<option value="">-- Selecciona un género --</option>';
                    return;
                }
                fetch('/categorias/' + categoriaId + '/generos')
                    .then(res => res.json())
                    .then(data => {
                        let opciones = '<option value="">-- Selecciona un género --</option>';
                        data.forEach(g => {
                            opciones += `<option value="${g.id}">${g.nombre}</option>`;
                        });
                        selectGenero.innerHTML = opciones;
                    })
                    .catch(err => console.error(err));
            });

            // Al hacer clic en "Agregar", solo creamos una <tr> con un input hidden generos[]
            btnAgregar.addEventListener('click', () => {
                const generoId = selectGenero.value;
                if (!generoId) {
                    alert("Selecciona un género primero");
                    return;
                }

                const categoriaTxt = selectCategoria.options[selectCategoria.selectedIndex].text;
                const generoTxt    = selectGenero.options[selectGenero.selectedIndex].text;

                let fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${categoriaTxt}</td>
                    <td>${generoTxt}</td>
                    <td>
                        <button type="button" class="btn btn-danger btnBorrar">Borrar</button>
                    </td>
                `;
                // Input hidden para enviar generos[] en el formulario
                let inputHidden = document.createElement('input');
                inputHidden.type  = 'hidden';
                inputHidden.name  = 'generos[]';
                inputHidden.value = generoId;

                fila.appendChild(inputHidden);
                tablaGeneros.appendChild(fila);
            });

            // Borrar la fila
            tablaGeneros.addEventListener('click', (e) => {
                if (e.target.classList.contains('btnBorrar')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    @endif
</script>
@endsection
