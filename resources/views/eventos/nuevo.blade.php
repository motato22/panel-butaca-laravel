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

                    {{-- Modo edición o creación --}}
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
                                        $currentImage = isset($evento) && $evento->foto
                                            ? asset('storage/uploads/eventos/' . $evento->foto)
                                            : null;
                                    @endphp

                                    @if ($currentImage)
                                        <img class="img-thumbnail max-height-6 mb-2"
                                             src="{{ $currentImage }}"
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

                            <!-- Filtro con dos selects (Categoría y Género) -->
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
    // Variables para prellenar temporada/unico
    $oldFechaInicioTemporada     = old('fecha_inicio_temporada', '');
    $oldFechaFinTemporada        = old('fecha_fin_temporada', '');
    $oldHorarioTemporadaInicio   = old('horario_temporada_inicio', '');
    $oldHorarioTemporadaFin      = old('horario_temporada_fin', '');
    $oldFechaUnico               = old('fecha_unico', '');
@endphp

<script>
    // DEBUG: Cargamos el JSON de horarios y fechas
    let horarios = @json(isset($evento) ? $evento->horario : null);
    if (!horarios || typeof horarios !== 'object') {
        horarios = {};
    }

    let fechaInicioBD = @json(isset($evento->fecha_inicio) ? $evento->fecha_inicio : null);
    let fechaFinBD    = @json(isset($evento->fecha_fin)    ? $evento->fecha_fin    : null);

    // DEBUG: Revisamos qué valores llegan
    console.log("DEBUG - fechaInicioBD:", fechaInicioBD, "fechaFinBD:", fechaFinBD);
    console.log("DEBUG - Horarios crudos:", horarios);

    function esNuevoFormatoHorario(h) {
        return Array.isArray(h) && 
               h.length > 0 && 
               h[0].dias !== undefined && 
               h[0].horas !== undefined;
    }

    // Expansión a fechas reales si es el nuevo formato
    if (esNuevoFormatoHorario(horarios) && fechaInicioBD && fechaFinBD) {
        let [iYear, iMonth, iDay] = fechaInicioBD.split('-').map(Number);
        let [fYear, fMonth, fDay] = fechaFinBD.split('-').map(Number);

        let startDate = new Date(`${iYear}-${String(iMonth).padStart(2,"0")}-${String(iDay).padStart(2,"0")}T00:00:00`);
        let endDate   = new Date(`${fYear}-${String(fMonth).padStart(2,"0")}-${String(fDay).padStart(2,"0")}T00:00:00`);

        let transformado = {};

        // JS getDay(): Dom=0, Lun=1, ... Sab=6
        // System: 0=Lunes, 6=Domingo => daySistema = (jsDay + 6) % 7
        function daySistema(jsDay) {
            return (jsDay + 6) % 7;
        }

        for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
            let sysDay = daySistema(d.getDay());
            horarios.forEach(item => {
                if (item.dias.includes(String(sysDay)) || item.dias.includes(sysDay)) {
                    // DEBUG: console.log("Día coincide:", sysDay, "fecha:", d);
                    let yyyy = d.getFullYear();
                    let mm   = String(d.getMonth() + 1).padStart(2, '0');
                    let dd   = String(d.getDate()).padStart(2, '0');
                    let fechaClave = `${yyyy}-${mm}-${dd}`;

                    if (!transformado[fechaClave]) {
                        transformado[fechaClave] = [];
                    }
                    if (!transformado[fechaClave].includes(item.horas)) {
                        transformado[fechaClave].push(item.horas);
                    }
                }
            });
        }

        // Reemplazamos con la versión expandida
        horarios = transformado;

        // Forzamos "funciones"
        document.addEventListener('DOMContentLoaded', function() {
            let tipoHorarioSelect = document.getElementById("tipo_horario");
            if (tipoHorarioSelect) {
                tipoHorarioSelect.value = "funciones";
                tipoHorarioSelect.dispatchEvent(new Event('change'));
            }
        });
    }

    // Variables antiguas
    let oldFechaInicioTemporada   = "{{ $oldFechaInicioTemporada }}";
    let oldFechaFinTemporada      = "{{ $oldFechaFinTemporada }}";
    let oldHorarioTemporadaInicio = "{{ $oldHorarioTemporadaInicio }}";
    let oldHorarioTemporadaFin    = "{{ $oldHorarioTemporadaFin }}";
    let oldFechaUnico             = "{{ $oldFechaUnico }}";

    function actualizarHorariosInput() {
        let hiddenField = document.getElementById("horarios_json");
        if (hiddenField) {
            hiddenField.value = JSON.stringify(horarios);
        }
    }

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
            btnEliminarFecha.onclick = () => {
                delete horarios[fecha];
                renderizarHorarios();
                actualizarHorariosInput();
            };
            colBorrar.appendChild(btnEliminarFecha);

            let colContenido = document.createElement("div");
            colContenido.classList.add("col-md-9");
            let titulo = document.createElement("div");
            titulo.classList.add("font-weight-bold", "mb-2");

            let [yyyy, mm, dd] = fecha.split("-");
            let dateObj = new Date(yyyy, mm - 1, dd, 12);
            titulo.innerHTML = dateObj.toLocaleDateString("es-ES", {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
            });

            let horariosDiv = document.createElement("div");
            horariosDiv.classList.add("d-flex", "flex-wrap");

            horarios[fecha].forEach(hora => {
                let badge = document.createElement("span");
                badge.classList.add("badge", "badge-secondary", "p-2", "m-1", "d-flex", "align-items-center");
                badge.innerHTML = `${hora} `;
                let closeButton = document.createElement("button");
                closeButton.classList.add("btn", "btn-sm", "btn-light", "ml-1", "p-0");
                closeButton.innerHTML = "✖";
                closeButton.onclick = () => {
                    horarios[fecha] = horarios[fecha].filter(h => h !== hora);
                    if (horarios[fecha].length === 0) {
                        delete horarios[fecha];
                    }
                    renderizarHorarios();
                    actualizarHorariosInput();
                };
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

        actualizarHorariosInput();
    }

    // Función para "temporada"
    function actualizarHorariosTemporada() {
    const fechaInicioStr   = document.querySelector('input[name="fecha_inicio_temporada"]').value;
    const fechaFinStr      = document.querySelector('input[name="fecha_fin_temporada"]').value;
    const horarioInicio    = document.querySelector('input[name="horario_temporada_inicio"]').value;
    const horarioFin       = document.querySelector('input[name="horario_temporada_fin"]').value;

    // Días marcados (lunes, martes, etc.)
    const diasCheckboxes   = document.querySelectorAll('input[name="dias_temporada[]"]:checked');
    if (!fechaInicioStr || !fechaFinStr || diasCheckboxes.length === 0) {
        horarios = {};
        actualizarHorariosInput();
        return;
    }

    // CAMBIO: Forzamos T00:00:00 para no "desplazar" la fecha
    let fechaInicio = new Date(`${fechaInicioStr}T00:00:00`);
    let fechaFin    = new Date(`${fechaFinStr}T00:00:00`);

    horarios = {};
    const diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];

    for (let d = new Date(fechaInicio.getTime()); d <= fechaFin; d.setDate(d.getDate() + 1)) {
        let diaNombre = diasSemana[d.getDay()];
        if ([...diasCheckboxes].some(cb => cb.value === diaNombre)) {
            let anio = d.getFullYear();
            let mes  = String(d.getMonth() + 1).padStart(2, '0');
            let dia  = String(d.getDate()).padStart(2, '0');
            let fechaFormateada = `${anio}-${mes}-${dia}`;
            horarios[fechaFormateada] = [horarioInicio, horarioFin];
        }
    }
    actualizarHorariosInput();
}

    // Función para "unico_dia"
    function actualizarHorariosUnico() {
        const fechaUnico     = document.querySelector('input[name="fecha_unico"]').value;
        const horarioInicio  = document.querySelector('input[name="horario_temporada_inicio"]').value;
        const horarioFin     = document.querySelector('input[name="horario_temporada_fin"]').value;

        // DEBUG: console.log
        console.log("[UnicoDia] Called with:", {fechaUnico, horarioInicio, horarioFin});

        if (fechaUnico) {
            horarios = {};
            horarios[fechaUnico] = [horarioInicio, horarioFin];
        } else {
            horarios = {};
        }
        actualizarHorariosInput();
    }

    // Detectar tipo de horario al editar
    function detectarTipoHorario() {
        let keys = Object.keys(horarios);

        // DEBUG:
        console.log("horarios en edición:", horarios, "keys:", keys);

        let tipoSeleccionado = "";
        if (keys.length === 0) {
            tipoSeleccionado = "";
        } else if (keys.length === 1) {
            // 2 elementos => "unico_dia", si no => "funciones"
            if (horarios[keys[0]].length === 2) {
                tipoSeleccionado = "unico_dia";
            } else {
                tipoSeleccionado = "funciones";
            }
        } else {
            // Más de una fecha: si todas tienen 2 elementos iguales => temporada, sino => funciones
            let allSame = true;
            let first = horarios[keys[0]];
            for (let i = 1; i < keys.length; i++) {
                let arr = horarios[keys[i]];
                if (arr.length !== 2 || arr[0] !== first[0] || arr[1] !== first[1]) {
                    allSame = false;
                    break;
                }
            }
            tipoSeleccionado = allSame ? "temporada" : "funciones";
        }
        return tipoSeleccionado;
    }

    document.addEventListener("DOMContentLoaded", function() {
        console.log("DOMContentLoaded - Attempting to detect type of horario...");
        let tipoHorarioSelect = document.getElementById("tipo_horario");
        if (tipoHorarioSelect) {
            tipoHorarioSelect.value = detectarTipoHorario();
            tipoHorarioSelect.dispatchEvent(new Event('change'));
        }
    });

    // Listener para changes en el select
    document.getElementById("tipo_horario").addEventListener("change", function() {
        let tipo = this.value;
        let contenedor = document.getElementById("contenedor_horarios");
        contenedor.innerHTML = "";

        if (tipo === "temporada") {
            contenedor.innerHTML = `
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Fecha de inicio <span style="color: red;">*</span></label>
                        <input type="date" name="fecha_inicio_temporada" class="form-control">
                        <small class="form-text text-muted text-right">Fecha de Inicio de la Temporada</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Fecha de fin <span style="color: red;">*</span></label>
                        <input type="date" name="fecha_fin_temporada" class="form-control">
                        <small class="form-text text-muted text-right">Fecha de Término de la Temporada</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Días con actividad <span style="color: red;">*</span></label>
                    <div>
                        ${["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"]
                            .map(dia => `<label><input type="checkbox" name="dias_temporada[]" value="${dia}"> ${dia}</label>`)
                            .join(' | ')}
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Horario de Inicio <span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_inicio" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Horario de Término <span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_fin" class="form-control">
                    </div>
                </div>
                <input type="hidden" name="horarios" id="horarios_json">
            `;

            document.querySelector('input[name="fecha_inicio_temporada"]').addEventListener("change", actualizarHorariosTemporada);
            document.querySelector('input[name="fecha_fin_temporada"]').addEventListener("change", actualizarHorariosTemporada);
            document.querySelector('input[name="horario_temporada_inicio"]').addEventListener("change", actualizarHorariosTemporada);
            document.querySelector('input[name="horario_temporada_fin"]').addEventListener("change", actualizarHorariosTemporada);
            document.querySelectorAll('input[name="dias_temporada[]"]').forEach(cb => {
                cb.addEventListener("change", actualizarHorariosTemporada);
            });

            let keys = Object.keys(horarios);
            if (keys.length > 0) {
                keys.sort();
                let fechaInicio = keys[0];
                let fechaFin = keys[keys.length - 1];
                let tiempos = horarios[fechaInicio] || [];
                document.querySelector('input[name="fecha_inicio_temporada"]').value = fechaInicio;
                document.querySelector('input[name="fecha_fin_temporada"]').value = fechaFin;
                if (tiempos.length === 2) {
                    document.querySelector('input[name="horario_temporada_inicio"]').value = tiempos[0];
                    document.querySelector('input[name="horario_temporada_fin"]').value = tiempos[1];
                }

                const diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
                let diasSet = new Set();
                keys.forEach(fecha => {
                    let [yyyy, mm, dd] = fecha.split("-");
                    let d = new Date(yyyy, mm - 1, dd, 12);
                    diasSet.add(diasSemana[d.getDay()]);
                });
                document.querySelectorAll('input[name="dias_temporada[]"]').forEach(cb => {
                    if (diasSet.has(cb.value)) {
                        cb.checked = true;
                    }
                });
            }
            actualizarHorariosTemporada();

        } else if (tipo === "funciones") {
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
                    <div class="form-group col-md-6">
                        <div id="lista_horarios">
                            <h5>Lista de horarios</h5>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="horarios" id="horarios_json">
            `;
            let keys = Object.keys(horarios);
            if (keys.length > 0) {
                document.getElementById("fecha_funcion").value = keys[0];
                if(horarios[keys[0]].length > 0) {
                    document.getElementById("hora_funcion").value = horarios[keys[0]][0];
                }
            }
            renderizarHorarios();

            document.getElementById("btnAgregarFuncion").addEventListener("click", () => {
                let fechaInput = document.getElementById("fecha_funcion").value;
                let hora       = document.getElementById("hora_funcion").value;
                if (!fechaInput || !hora) {
                    alert("Selecciona una fecha y una hora.");
                    return;
                }
                if (!horarios[fechaInput]) {
                    horarios[fechaInput] = [];
                }
                if (!horarios[fechaInput].includes(hora)) {
                    horarios[fechaInput].push(hora);
                }
                renderizarHorarios();
            });

        } else if (tipo === "unico_dia") {
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
                <input type="hidden" name="horarios" id="horarios_json">
            `;
            if(Object.keys(horarios).length > 0) {
                let fecha   = Object.keys(horarios)[0];
                let tiempos = horarios[fecha];
                document.querySelector('input[name="fecha_unico"]').value = fecha;
                document.querySelector('input[name="horario_temporada_inicio"]').value = tiempos[0];
                document.querySelector('input[name="horario_temporada_fin"]').value    = tiempos[1];
            }
            document.querySelector('input[name="fecha_unico"]').addEventListener("change", actualizarHorariosUnico);
            document.querySelector('input[name="horario_temporada_inicio"]').addEventListener("change", actualizarHorariosUnico);
            document.querySelector('input[name="horario_temporada_fin"]').addEventListener("change", actualizarHorariosUnico);
            actualizarHorariosUnico();
        }
    });

    // LÓGICA DE GÉNEROS (AJAX O SIN AJAX)
    @if(isset($evento->id))
    // MODO EDICIÓN: usar AJAX (attach/detach)
    const eventoId        = "{{ $evento->id }}";
    const urlAttachGenero = "{{ route('eventos.attachGenero', $evento->id) }}";
    // Quedaría algo como: /eventos/123/generos
    const urlDetachGenero = "/eventos/" + eventoId + "/generos/";

    const urlFetchGenerosCategoria = "/categorias/"; 
    // => /categorias/{id}/generos

    document.addEventListener('DOMContentLoaded', function() {
        const selectCategoria = document.getElementById('selectCategoria');
        const selectGenero    = document.getElementById('selectGenero');
        const btnAgregar      = document.getElementById('btnAgregar');
        const tablaGeneros    = document.getElementById('tablaGeneros');

        // Al cambiar categoría => cargar géneros
        selectCategoria.addEventListener('change', () => {
            const catId = selectCategoria.value;
            if (!catId) {
                selectGenero.innerHTML = '<option value="">-- Selecciona un género --</option>';
                return;
            }
            fetch(urlFetchGenerosCategoria + catId + '/generos')
                .then(res => res.json())
                .then(data => {
                    let opciones = '<option value="">-- Selecciona un género --</option>';
                    data.forEach(g => {
                        opciones += `<option value="${g.id}">${g.nombre}</option>`;
                    });
                    selectGenero.innerHTML = opciones;
                })
                .catch(console.error);
        });

        // Al hacer clic en "Agregar"
        btnAgregar.addEventListener('click', () => {
            const generoId = selectGenero.value;
            if (!generoId) {
                alert("Selecciona un género primero");
                return;
            }
            // Llamamos AJAX POST /eventos/{evento}/generos
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
                    // Para mostrarlo en la tabla, podemos volver a cargar la info
                    // o hacer un fetch a /api/generos/{generoId}, etc.
                    // En tu ejemplo, usas fetch(`/api/generos/${generoId}`) (asegúrate de tener esa ruta).
                    // Aquí, para simplificar, asumimos que ya conocemos la categoría
                    // a partir de selectCategoria.

                    const catText = selectCategoria.options[selectCategoria.selectedIndex].text;
                    const genText = selectGenero.options[selectGenero.selectedIndex].text;

                    let fila = document.createElement('tr');
                    fila.dataset.generoId = generoId;
                    fila.innerHTML = `
                        <td>${catText}</td>
                        <td>${genText}</td>
                        <td>
                            <button type="button" class="btn btn-danger btnBorrar">Borrar</button>
                        </td>
                    `;
                    tablaGeneros.appendChild(fila);

                } else {
                    alert(response.message || 'Error al agregar género');
                }
            })
            .catch(console.error);
        });

        // Al hacer clic en "Borrar" un género
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
                            alert(resp.message || 'Error al quitar género');
                        }
                    })
                    .catch(console.error);
                }
            }
        });
    });
    @else
    // MODO CREACIÓN: sin AJAX, solo inputs ocultos
    document.addEventListener('DOMContentLoaded', function() {
        const selectCategoria = document.getElementById('selectCategoria');
        const selectGenero    = document.getElementById('selectGenero');
        const btnAgregar      = document.getElementById('btnAgregar');
        const tablaGeneros    = document.getElementById('tablaGeneros');

        selectCategoria.addEventListener('change', () => {
            const catId = selectCategoria.value;
            if (!catId) {
                selectGenero.innerHTML = '<option value="">-- Selecciona un género --</option>';
                return;
            }
            fetch('/categorias/' + catId + '/generos')
                .then(res => res.json())
                .then(data => {
                    let opciones = '<option value="">-- Selecciona un género --</option>';
                    data.forEach(g => {
                        opciones += `<option value="${g.id}">${g.nombre}</option>`;
                    });
                    selectGenero.innerHTML = opciones;
                })
                .catch(console.error);
        });

        btnAgregar.addEventListener('click', () => {
            const generoId = selectGenero.value;
            if (!generoId) {
                alert("Selecciona un género primero");
                return;
            }
            const catTxt = selectCategoria.options[selectCategoria.selectedIndex].text;
            const genTxt = selectGenero.options[selectGenero.selectedIndex].text;

            let fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${catTxt}</td>
                <td>${genTxt}</td>
                <td><button type="button" class="btn btn-danger btnBorrar">Borrar</button></td>
                <input type="hidden" name="generos[]" value="${generoId}">
            `;
            tablaGeneros.appendChild(fila);
        });

        tablaGeneros.addEventListener('click', (e) => {
            if (e.target.classList.contains('btnBorrar')) {
                e.target.closest('tr').remove();
            }
        });
    });
    @endif
</script>
@endsection