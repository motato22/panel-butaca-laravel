@extends('layouts.main')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-theater" style="font-size: 5rem;"></i>
                    </div>
                </div>
                <h1>Nuevo Recinto</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('recintos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Imagen principal -->
                <div class="form-row">
                    <div class="col-md-8 mx-auto text-center">
                        <label for="imagen_principal" class="form-label">Imagen principal</label>
                        <div class="custom-file">
                            <input type="file" id="foto" name="foto" class="custom-file-input">
                            <label class="custom-file-label" for="foto">Seleccionar archivo</label>
                        </div>
                        <small class="form-text text-muted">Imagen cuadrada (recomendado: 512 x 512).</small>
                    </div>
                </div>

                <!-- Promocionado -->
                <div class="form-row py-3">
                    <div class="col-md-6 mx-auto text-center">
                        <label class="cstm-switch">
                            <input type="checkbox" id="promocionado" name="promocionado" class="cstm-switch-input">
                            <span class="cstm-switch-indicator bg-success"></span>
                            <span class="cstm-switch-description">¿Este recinto será promocionado?</span>
                        </label>
                    </div>
                </div>

                <!-- Nombre del recinto -->
                <div class="form-group">
                    <label for="nombre" class="required">Nombre del Recinto *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre del recinto" required>
                    @error('nombre')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Información de contacto -->
                <h5 class="pt-3">Información de Contacto</h5>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="correo_contacto">Correo de contacto</label>
                        <input type="email" id="correo_contacto" name="correo_contacto" class="form-control" placeholder="contacto@recinto.com">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="telefono_contacto">Teléfono de contacto</label>
                        <input type="text" id="telefono_contacto" name="telefono_contacto" class="form-control" placeholder="Teléfono">
                    </div>
                </div>

                <!-- Descripción y amenidades -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="descripcion" class="required">Descripción *</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Información adicional sobre el recinto" required></textarea>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="amenidades">Amenidades</label>
                        <textarea id="amenidades" name="amenidades" class="form-control" rows="3" placeholder="Indique las facilidades (cafetería, accesibilidad, etc.)"></textarea>
                    </div>
                </div>

                <!-- Capacidad, video y página web -->
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="capacidad">Capacidad</label>
                        <input type="text" id="capacidad" name="capacidad" class="form-control" placeholder="Ej. 200 personas">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="video">Video</label>
                        <input type="url" id="video" name="video" class="form-control" placeholder="URL del video">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="web">Página web</label>
                        <input type="url" id="web" name="web" class="form-control" placeholder="http://">
                    </div>
                </div>

                <!-- Zona -->
                <div class="form-group">
                    <label for="zona_id">Zona</label>
                    <select id="zona_id" name="zona_id" class="form-control js-select2">
                        <option value="">Sin asignación de zona</option>
                        @foreach($zonas as $zona)
                            <option value="{{ $zona->id }}">{{ $zona->zona }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Horario de atención -->
                <h5 class="pt-3">Horario de atención</h5>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="horario_inicio">Inicio</label>
                        <input type="time" id="horario_inicio" name="horario_inicio" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="horario_fin">Fin</label>
                        <input type="time" id="horario_fin" name="horario_fin" class="form-control">
                    </div>
                </div>

                <!-- Dirección y mapa -->
                <div class="form-group">
                    <label for="direccion" class="required">Dirección *</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección completa" required>
                    <input type="hidden" id="lat" name="lat">
                    <input type="hidden" id="lng" name="lng">
                    <div id="map" style="height: 300px; width: 100%; margin-top: 15px;"></div>
                </div>

                <!-- Redes sociales -->
                <h5 class="pt-3">Redes sociales</h5>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="twitter">Twitter</label>
                        <input type="url" id="twitter" name="twitter" class="form-control" placeholder="Twitter">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="facebook">Facebook</label>
                        <input type="url" id="facebook" name="facebook" class="form-control" placeholder="Facebook">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="youtube">YouTube</label>
                        <input type="url" id="youtube" name="youtube" class="form-control" placeholder="YouTube">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="instagram">Instagram</label>
                        <input type="url" id="instagram" name="instagram" class="form-control" placeholder="Instagram">
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-group text-right">
                    <button type="reset" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://maps.google.com/maps/api/js?key=AIzaSyCx5syL0jRIx6U17sIZlBb7QnC2ly1UMtc&libraries=places"></script>
<script>
    // Inicializar el mapa
    function initMap() {
        const map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 20.659698, lng: -103.349609 },
            zoom: 13,
        });

        const input = document.getElementById('direccion');
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        const marker = new google.maps.Marker({
            map: map,
            draggable: true,
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                alert("No se encontró información para esta dirección.");
                return;
            }

            map.setCenter(place.geometry.location);
            map.setZoom(17);
            marker.setPosition(place.geometry.location);
        });

        google.maps.event.addListener(marker, 'position_changed', () => {
            const position = marker.getPosition();
            document.getElementById('lat').value = position.lat();
            document.getElementById('lng').value = position.lng();
        });
    }

    // Cargar el mapa cuando la página esté lista
    window.onload = initMap;
</script>
@endsection
