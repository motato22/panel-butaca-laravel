@extends('layouts.main')

@section('title', 'Nuevo Evento')

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
                <h1>Nuevo Evento</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-12 text-right">
                    {{-- Espacio para botones o herramientas --}}
                </div>

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
                    <form action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- <button type="submit" style="display:none;"></button> -->

                        <div class="form-row">
                            <div class="col-12 col-md-8 col-lg-6 text-center offset-md-2 offset-lg-3">
                                <img class="img-thumbnail max-height-6">
                                <div class="input-group mb-3">
                                    <input type="file" name="foto" class="custom-file-input" id="foto">
                                    <label class="custom-file-label" for="foto">Seleccionar imagen</label>
                                    <small class="form-text text-muted">Imagen cuadrada (recomendado: 512 x 512).</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nombre">
                                    Nombre del evento <span style="color: red;">*</span>
                                </label>
                                <input type="text" id="nombre" name="nombre"
                                    class="form-control @error('nombre') is-invalid @enderror"
                                    placeholder="Nombre del evento"
                                    value="{{ old('nombre') }}" required>

                                {{-- Muestra el mensaje de error si Laravel detecta un problema --}}
                                @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="recinto" class="required">Recinto <span style="color: red;">*</span></label>
                                <select id="recinto" name="recinto" class="form-control @error('recinto') is-invalid @enderror">
                                    <option value="">Selecciona un recinto</option>
                                    @foreach ($recintos as $recinto)
                                    <option value="{{ $recinto->id }}" {{ old('recinto') == $recinto->id ? 'selected' : '' }}>
                                        {{ $recinto->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Recinto para el cual se configura éste evento</small>

                                {{-- Mostrar error si el campo no se seleccionó --}}
                                @error('recinto')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- SELECCIÓN DE HORARIO -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tipo de Horario <span style="color: red;">*</span></label>
                                <select id="tipo_horario" name="tipo_horario" class="form-control">
                                    <option value="temporada">Temporada (varios días con horarios iguales)</option>
                                    <option value="funciones">Funciones (varios días con uno o varios horarios)</option>
                                    <option value="unico_dia">Único día (un solo día con un solo horario)</option>
                                </select>
                            </div>
                        </div>

                        <!-- CONTENEDOR PARA HORARIOS -->
                        <div id="contenedor_horarios"></div>

                        <hr />

                        <h4 class="pt-0 pb-3">Precio del evento</h4>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="cstm-switch m-0">
                                    <!-- Campo oculto con valor 0 -->
                                    <input type="hidden" name="promocion" value="0">
                                    <!-- Checkbox con valor 1 -->
                                    <input type="checkbox" id="es_gratuito" name="es_gratuito" class="cstm-switch-input" value="1"
                                        {{ old('es_gratuito', $evento->es_gratuito ?? false) ? 'checked' : '' }}>
                                    <span class="cstm-switch-indicator bg-success"></span>
                                    <span class="cstm-switch-description">¿Éste evento es gratuito?</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="precio_bajo">Precio más bajo</label>
                                <input type="number" id="precio_bajo" name="precio_bajo" class="form-control" placeholder="Precio mas bajo" value="{{ old('precio_bajo') }}">
                                <small class="form-text text-muted">Valor más barato del boleto.</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="precio_alto">Precio más alto</label>
                                <input type="number" id="precio_alto" name="precio_alto" class="form-control" placeholder="Precio mas alto" value="{{ old('precio_alto') }}">
                                <small class="form-text text-muted">Valor más alto del boleto.</small>
                            </div>
                        </div>

                        <hr class="pb-3 mt-0" />

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="descripcion">Descripción<span style="color: red;">*</span></label>
                                <textarea id="descripcion" name="descripcion" class="form-control">{{ old('descripcion') }}</textarea>
                                <small class="form-text text-muted">Información adicional que quiera que los usuarios conozcan del evento.</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="url_compra">URL de compra<span style="color: red;">*</span></label>
                                <input type="text" id="url_compra" name="url_compra" class="form-control" placeholder="http://" value="{{ old('url_compra') }}">
                                <small class="form-text text-muted">Url a la cuál se redirecionará para que se realice la compra de boletos.</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="texto_promocional">Texto de promoción (opcional)</label>
                                <select id="texto_promocional" name="texto_promocional" class="form-control js-select2">
                                    <option value="">Sin promoción</option>
                                    @foreach ($promociones as $promo)
                                    <option value="{{ $promo }}" {{ old('texto_promocional') == $promo ? 'selected' : '' }}>
                                        {{ $promo }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Texto que indica en que consiste la promoción.</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="video">Video Promocional (YouTube Link)</label>
                                <input type="text" id="video" name="video" class="form-control" value="{{ old('video') }}">
                                <small class="form-text text-muted">Url del video de Youtube promocional del evento.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="web">Website</label>
                                <input type="text" id="web" name="web" class="form-control" value="{{ old('web') }}">
                            </div>
                        </div>


                        <hr />

                        <h4 class="pt-0 pb-3">Redes Sociales</h4>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="instagram">Instagram</label>
                                <input type="text" id="instagram" name="instagram" class="form-control" value="{{ old('instagram') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="facebook">Facebook</label>
                                <input type="text" id="facebook" name="facebook" class="form-control" value="{{ old('facebook') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="youtube">YouTube</label>
                                <input type="text" id="youtube" name="youtube" class="form-control" value="{{ old('youtube') }}">
                                <small class="form-text text-muted">Url del canal de Youtube para el evento u organizadores. (opcional)</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="twitter">Twitter</label>
                                <input type="text" id="twitter" name="twitter" class="form-control" value="{{ old('twitter') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="snapchat">Snapchat</label>
                                <input type="text" id="snapchat" name="snapchat" class="form-control" value="{{ old('snapchat') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger float-right mx-1">Crear</button>
                            <a href="{{ route('eventos.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>


<script>
    let horarios = {};

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
                        <label>Fecha de fin<span style="color: red;">*</span></label>
                        <input type="date" name="fecha_fin_temporada" class="form-control">
                        <small class="form-text text-muted text-right">Fecha de Término de la Temporada</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Días con actividad<span style="color: red;">*</span></label>
                    <div>
                        ${["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"].map(dia =>
                            `<label><input type="checkbox" name="dias_temporada[]" value="${dia}"> ${dia}</label> | `
                        ).join('')}
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Horario de Inicio<span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_inicio" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Horario de Término<span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_fin" class="form-control">
                    </div>
                </div>
            `;
        } else if (tipo === "funciones") {
            contenedor.innerHTML = `
                <div id="lista_funciones" class="form-row">
                    <div class="form-group col-md-6">
                        <label>Horarios<span style="color: red;">*</span></label>
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
                </div>
            `;
        } else if (tipo === "unico_dia") {
            contenedor.innerHTML = `
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Fecha del evento<span style="color: red;">*</span></label>
                        <input type="date" name="fecha_unico" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label>Horario de Inicio<span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_inicio" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Horario de Término<span style="color: red;">*</span></label>
                        <input type="time" name="horario_temporada_fin" class="form-control">
                    </div>
                </div>
            `;
        }
    });

    document.addEventListener("click", function(event) {
        if (event.target && event.target.id === "btnAgregarFuncion") {
            let fechaInput = document.getElementById("fecha_funcion").value;
            let hora = document.getElementById("hora_funcion").value;

            if (!fechaInput || !hora) {
                alert("Selecciona una fecha y una hora.");
                return;
            }

            let fecha = new Date(fechaInput + "T24:00:00");
            let fechaCorregida = fecha.toISOString().split('T')[0];

            if (!horarios[fechaCorregida]) {
                horarios[fechaCorregida] = [];
            }

            if (!horarios[fechaCorregida].includes(hora)) {
                horarios[fechaCorregida].push(hora);
                renderizarHorarios();
            }
        }
    });

    function eliminarHorario(fecha, hora) {
        horarios[fecha] = horarios[fecha].filter(h => h !== hora);
        if (horarios[fecha].length === 0) {
            delete horarios[fecha];
        }
        renderizarHorarios();
    }

    function eliminarFecha(fecha) {
        delete horarios[fecha];
        renderizarHorarios();
    }

    function renderizarHorarios() {
    let contenedor = document.getElementById("lista_horarios");
    if (!contenedor) return;

    contenedor.innerHTML = "<h5>Lista de horarios</h5>";

    Object.keys(horarios).forEach(fecha => {
        let divFecha = document.createElement("div");
        divFecha.classList.add("p-3", "mb-2", "shadow-sm", "border", "rounded");

        // Crear un row para distribuir el contenido
        let rowDiv = document.createElement("div");
        rowDiv.classList.add("row", "align-items-center");

        // Columna izquierda: Botón de borrar
        let colBorrar = document.createElement("div");
        colBorrar.classList.add("col-md-3", "text-left");

        let btnEliminarFecha = document.createElement("button");
        btnEliminarFecha.classList.add("btn", "btn-danger", "btn-sm", "w-100"); // Hace el botón del ancho de la columna
        btnEliminarFecha.innerText = "Borrar";
        btnEliminarFecha.onclick = () => eliminarFecha(fecha);

        colBorrar.appendChild(btnEliminarFecha);

        // Columna derecha: Fecha y horarios
        let colContenido = document.createElement("div");
        colContenido.classList.add("col-md-9");

        let titulo = document.createElement("div");
        titulo.classList.add("font-weight-bold", "mb-2");
        titulo.innerHTML = `${new Date(fecha).toLocaleDateString("es-ES", { 
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
        })}`;

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

        // Añadir las columnas al row
        rowDiv.appendChild(colBorrar);
        rowDiv.appendChild(colContenido);

        // Añadir el row al divFecha
        divFecha.appendChild(rowDiv);
        contenedor.appendChild(divFecha);
    });

    document.getElementById("horarios_json").value = JSON.stringify(horarios);
}

</script>

@endsection