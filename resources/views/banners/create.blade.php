@extends('layouts.main')

@section('title', 'Nuevo Banner')

@section('content')
{{-- Sección de encabezado oscuro con título --}}
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-page-layout-header" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>Nuevo Banner</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-12 pt-4">
                    {{-- Errores de validación (si los hay) --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <b>No se pudo guardar el banner</b>:
                        <ul>
                            @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('banners.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        name="banner_form">

                        @csrf

                        <div class="form-row">
                            <div class="col-12 col-md-8 col-lg-6 text-center offset-md-2 offset-lg-3">



                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <label class="custom-file-label" for="imagen_file">
                                            Imagen a mostrar en el banner
                                        </label>
                                        <input
                                            type="file"
                                            id="imagen_file"
                                            name="imagen_file"
                                            class="custom-file-input"
                                            accept=".png,.jpg,.svg"
                                            aria-describedby="banner_form_file_help" />
                                    </div>
                                </div>
                                <small id="banner_form_file_help" class="help-text">
                                    Imágen a mostrar en el banner.
                                </small>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Descripción --}}
                            <div class="form-group col-md-6">
                                <label for="descripcion">Nota descriptiva</label>
                                <input
                                    type="text"
                                    id="descripcion"
                                    name="descripcion"
                                    class="form-control"
                                    maxlength="190"
                                    placeholder="Texto de referencia para administración"
                                    value="{{ old('descripcion') }}">
                                <small class="help-text">
                                    Texto de referencia para administración.
                                </small>
                            </div>

                            {{-- Texto opcional para el banner --}}
                            <div class="form-group col-md-6">
                                <label for="texto">Texto</label>
                                <input
                                    type="text"
                                    id="texto"
                                    name="texto"
                                    class="form-control"
                                    maxlength="190"
                                    placeholder="Texto a mostrar en el banner"
                                    value="{{ old('texto') }}">
                                <small class="help-text">
                                    Mensaje a mostrar en el banner (opcional).
                                </small>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Ubicación de la imagen --}}
                            <div class="form-group col-md-6">
                                <label for="ubicacion_imagen" class="required">
                                    Ubicación de la imagen<span style="color: red;">*</span>
                                </label>
                                <select
                                    id="ubicacion_imagen"
                                    name="ubicacion_imagen"
                                    class="form-control js-select2">
                                    <option value="0" @selected(old('ubicacion_imagen')=='0' )>
                                        Llenar banner (texto ignorado)
                                    </option>
                                    <option value="1" @selected(old('ubicacion_imagen')=='1' )>
                                        Lado Izquierdo (texto requerido)
                                    </option>
                                    <option value="2" @selected(old('ubicacion_imagen')=='2' )>
                                        Lado Derecho (texto requerido)
                                    </option>
                                </select>
                                <small class="help-text">
                                    Se refiere a la ubicación de la imagen dentro del banner.
                                </small>
                            </div>

                            {{-- Ubicación global del banner (Arriba o Abajo) --}}
                            <div class="form-group col-md-6">
                                <label for="ubicacion" class="required">Ubicación<span style="color: red;">*</span></label>
                                <select
                                    id="ubicacion"
                                    name="ubicacion"
                                    class="form-control js-select2">
                                    <option value="0" @selected(old('ubicacion')=='0' )>
                                        Arriba
                                    </option>
                                    <option value="1" @selected(old('ubicacion')=='1' )>
                                        Abajo
                                    </option>
                                </select>
                                <small class="help-text">
                                    Ubicación del banner en el cliente (webapp / app mobil).
                                </small>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Fecha de inicio --}}
                            <div class="form-group col-md-6">
                                <label for="fecha_inicio">Fecha de inicio</label>
                                <input
                                    type="date"
                                    id="fecha_inicio"
                                    name="fecha_inicio"
                                    class="form-control datedropper2"
                                    data-large-mode="true"
                                    data-format="Y-m-d"
                                    data-lang="es"
                                    value="{{ old('fecha_inicio') }}">
                                <small class="help-text">
                                    ¿Cuándo comenzará el banner?
                                </small>
                            </div>

                            {{-- Fecha de fin --}}
                            <div class="form-group col-md-6">
                                <label for="fecha_fin">Fecha de fin</label>
                                <input
                                    type="date"
                                    id="fecha_fin"
                                    name="fecha_fin"
                                    class="form-control datedropper2"
                                    data-large-mode="true"
                                    data-format="Y-m-d"
                                    data-lang="es"
                                    value="{{ old('fecha_fin') }}">
                                <small class="help-text">
                                    ¿Cuándo finalizará el banner?
                                </small>
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- URL del banner --}}
                            <div class="form-group col-md-6">
                                <label for="url">URL del Banner</label>
                                <input
                                    type="text"
                                    id="url"
                                    name="url"
                                    class="form-control"
                                    placeholder="https://ejemplo.com"
                                    value="{{ old('url') }}">
                                <small class="help-text">
                                    Ingrese la URL que se abrirá al hacer clic en el banner.
                                </small>
                            </div>

                            {{-- Campo Activo (boolean) --}}
                            <div class="form-group col-md-6">
                                <label for="activo" class="required">
                                    ¿Activo? <span style="color: red;">*</span>
                                </label>
                                <select
                                    id="activo"
                                    name="activo"
                                    class="form-control js-select2">
                                    <option value="1" @selected(old('activo', 1)=='1' )>Sí</option>
                                    <option value="0" @selected(old('activo')=='0' )>No</option>
                                </select>
                                <small class="help-text">
                                    Indica si el banner estará activo (visible).
                                </small>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger float-right mx-1">
                                Crear
                            </button>
                            <a href="{{ route('banners.index') }}" class="btn btn-secondary float-right mx-1">
                                Cancelar
                            </a>
                        </div>

                    </form>
                    {{-- /Formulario --}}
                </div> {{-- col-12 --}}
            </div> {{-- row --}}
        </div> {{-- card-body --}}
    </div> {{-- card --}}
</div> {{-- container --}}
@endsection

@push('scripts')
<script>
    (function($) {
        'use strict';
        document.styleSheets[0].addRule('.custom-file-label:after', 'content: "Elegir" !important;');

        $(document).ready(function() {
            let el$ = $('.datedropper2');
            if (el$.dateDropper) {
                el$.dateDropper({});
            }
        });
    })(window.jQuery);
</script>
@endpush