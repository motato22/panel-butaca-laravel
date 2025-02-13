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

                        <button type="submit" onclick="return false;" style="display:none;"></button>

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

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="fecha_inicio">Fecha de inicio<span style="color: red;">*</span></label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}">
                                <small class="form-text text-muted">¿Cuándo comenzará el evento en el recinto configurado?</small>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="fecha_fin">Fecha de fin<span style="color: red;">*</span></label>
                                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}">
                                <small class="form-text text-muted">¿Cuándo finalizará el evento en el recinto configurado?</small>
                            </div>
                        </div>

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

@endsection

@section('javascripts')
<script src="{{ asset('js/dropzone.js') }}"></script>
@endsection