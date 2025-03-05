@extends('layouts.main')

@section('title', 'Nuevo Cupón')

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
                <h1>Nuevo: Promoción | Notificación</h1>
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

            <form action="{{ route('cupon.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h3>Segmento</h3>
                <p class="text-secondary">Llena el formulario para enviar la información</p>

                <div class="form-row">
                    <div class="col-12 col-md-4">
                        <label class="cstm-switch" for="cupon_dominio_0">
                            <input type="radio" value="0" name="cupon[dominio]" checked id="cupon_dominio_0" class="cstm-switch-input">
                            <span class="cstm-switch-indicator bg-primary"></span>
                            <span class="cstm-switch-description">Notificación todos los usuario</span>
                        </label>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="cstm-switch" for="cupon_dominio_1">
                            <input type="radio" value="1" name="cupon[dominio]" id="cupon_dominio_1" class="cstm-switch-input">
                            <span class="cstm-switch-indicator bg-primary"></span>
                            <span class="cstm-switch-description">Promoción Comunidad UDG</span>
                        </label>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="cstm-switch" for="cupon_dominio_2">
                            <input type="radio" value="2" name="cupon[dominio]" id="cupon_dominio_2" class="cstm-switch-input">
                            <span class="cstm-switch-indicator bg-primary"></span>
                            <span class="cstm-switch-description">Notificación Público en General</span>
                        </label>
                    </div>
                </div>

                <hr/>

                <!-- Filtros para Comunidad UDG -->
                <div id="filtros" style="display:none;">
                    <div class="form-group">
                        <label for="segmento">Segmento</label>
                        <select class="form-control" name="segmento" id="segmento">
                            <option value="Todos">Todos</option>
                            <option value="Alumno">Alumnos</option>
                            <option value="Egresado">Egresados</option>
                            <option value="Academico">Académicos</option>
                            <option value="Administrativo">Administrativos</option>
                        </select>
                    </div>
                    <!-- Aquí podrías incluir otros campos de filtros si lo requieres -->
                </div>

                <hr/>

                <h2>Detalles:</h2>
                <div class="form-row">
                    <div class="form-group col-md-6 px-3">
                        <div class="form-group">
                            <label for="cupon_descripcion" class="required">Descripción</label>
                            <textarea id="cupon_descripcion" name="cupon[descripcion]" required="required" class="form-control" placeholder="Información descriptiva">{{ old('cupon.descripcion') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group col-md-6 px-3">
                        <!-- Dropzone para subir imagen (puedes integrar DropzoneJS o mantener la estructura) -->
                        <div class="dropzone dz-clickable" id="upload-galeria">
                            <div class="dz-message">
                                <h1 class="display-4"><i class="mdi mdi-progress-upload"></i></h1>
                                Suelta el archivo aquí o haz clic en "Subir Archivo".<br>
                                <div class="p-t-5">
                                    <button type="button" class="btn btn-lg btn-primary">Subir Archivo</button>
                                </div>
                            </div>
                            <input type="hidden" id="cupon_imagePath" name="cupon[imagePath]" />
                        </div>
                    </div>

                    <div class="form-group col-12 text-right">
                        <button type="submit" class="btn btn-primary float-left mx-1">Publicar</button>
                        <a href="{{ route('cupon.index') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                    </div>
                </div>

                <!-- Si necesitas incluir secciones adicionales (por ejemplo, Estratos), puedes agregarlas aquí -->
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var radios = document.querySelectorAll('input[type="radio"][name="cupon[dominio]"]');
    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value == 1) { // Opción de Comunidad UDG
                document.getElementById('filtros').style.display = 'block';
            } else {
                document.getElementById('filtros').style.display = 'none';
            }
        });
    });

    // Inicialmente, si no se selecciona "Comunidad UDG", se ocultan los filtros.
    var checked = document.querySelector('input[type="radio"][name="cupon[dominio]"]:checked');
    if (!checked || checked.value != 1) {
        document.getElementById('filtros').style.display = 'none';
    }
});
</script>
@endpush
