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
                <h1>{{ isset($recinto) ? 'Editar Recinto' : 'Nuevo Recinto' }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($recinto) ? route('recintos.update', $recinto) : route('recintos.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($recinto))
                @method('PUT')
                @endif

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
                        @if(isset($recinto) && $recinto->foto)
                        <div class="mt-3">
                            <img src="{{ asset('storage/uploads/recintos/' . $recinto->foto) }}" class="img-thumbnail" width="150">
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Promocionado -->
                <div class="form-row py-3">
                    <div class="col-md-6 mx-auto text-center">
                        <label class="cstm-switch">
                            <!-- Campo oculto con valor 0 -->
                            <input type="hidden" name="promocion" value="0">
                            <!-- Checkbox con valor 1 -->
                            <input type="checkbox" id="promocion" name="promocion" class="cstm-switch-input" value="1"
                                {{ old('promocion', $recinto->promocion ?? false) ? 'checked' : '' }}>
                            <span class="cstm-switch-indicator bg-success"></span>
                            <span class="cstm-switch-description">¿Este recinto será promocionado?</span>
                        </label>
                    </div>
                </div>

                <!-- Nombre del recinto -->
                <div class="form-group">
                    <label for="nombre" class="required">Nombre del Recinto *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre del recinto" required value="{{ old('nombre', $recinto->nombre ?? '') }}">
                    @error('nombre')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Información de contacto -->
                <h5 class="pt-3">Información de Contacto</h5>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="correo_contacto">Correo de contacto</label>
                        <input type="email" id="correo_contacto" name="contacto" class="form-control" placeholder="contacto@recinto.com" value="{{ old('contacto', $recinto->contacto ?? '') }}">
                        @error('contacto')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="telefono_contacto">Teléfono de contacto</label>
                        <input type="text" id="telefono_contacto" name="telefono" class="form-control" placeholder="Teléfono" value="{{ old('telefono', $recinto->telefono ?? '') }}">
                        @error('telefono')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Descripción y amenidades -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="descripcion" class="required">Descripción *</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Información adicional sobre el recinto" required>{{ old('descripcion', $recinto->descripcion ?? '') }}</textarea>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="amenidades">Amenidades</label>
                        <textarea id="amenidades" name="amenidades" class="form-control" rows="3" placeholder="Indique las facilidades (cafetería, accesibilidad, etc.)">{{ old('amenidades', $recinto->amenidades ?? '') }}</textarea>
                    </div>
                </div>

                <!-- Capacidad, video y página web -->
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="capacidad">Capacidad</label>
                        <input type="text" id="capacidad" name="capacidad" class="form-control" placeholder="Ej. 200 personas" value="{{ old('capacidad', $recinto->capacidad ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="video">Video</label>
                        <input type="url" id="video" name="video" class="form-control" placeholder="URL del video" value="{{ old('video', $recinto->video ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="web">Página web</label>
                        <input type="url" id="web" name="web" class="form-control" placeholder="http://" value="{{ old('web', $recinto->web ?? '') }}">
                    </div>
                </div>

                <!-- Zona -->
                <div class="form-group">
                    <label for="zona_id">Zona</label>
                    <select id="zona_id" name="zona_id" class="form-control js-select2">
                        <option value="">Sin asignación de zona</option>
                        @foreach($zonas as $zona)
                        <option value="{{ $zona->id }}" {{ (isset($recinto) && $recinto->zona_id == $zona->id) ? 'selected' : '' }}>{{ $zona->zona }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Horario de atención -->
                <h5 class="pt-3">Horario de atención</h5>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="horario_inicio">Inicio</label>
                        <input type="time" id="horario_inicio" name="horario_inicio" class="form-control" value="{{ old('horario_inicio', $recinto->horario_inicio ?? '') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="horario_fin">Fin</label>
                        <input type="time" id="horario_fin" name="horario_fin" class="form-control" value="{{ old('horario_fin', $recinto->horario_fin ?? '') }}">
                    </div>
                </div>

                <!-- Dirección y mapa -->
                <div class="form-group">
                    <label for="direccion" class="required">Dirección *</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección completa" required value="{{ old('direccion', $recinto->direccion ?? '') }}">
                    <input type="hidden" id="lat" name="lat" value="{{ old('lat', $recinto->lat ?? '') }}">
                    <input type="hidden" id="lng" name="lng" value="{{ old('lng', $recinto->lng ?? '') }}">
                    <div id="map" style="height: 300px; width: 100%; margin-top: 15px;"></div>
                </div>

                <!-- Redes sociales -->
                <h5 class="pt-3">Redes sociales</h5>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="twitter">Twitter</label>
                        <input type="url" id="twitter" name="twitter" class="form-control" placeholder="Twitter" value="{{ old('twitter', $recinto->twitter ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="facebook">Facebook</label>
                        <input type="url" id="facebook" name="facebook" class="form-control" placeholder="Facebook" value="{{ old('facebook', $recinto->facebook ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="youtube">YouTube</label>
                        <input type="url" id="youtube" name="youtube" class="form-control" placeholder="YouTube" value="{{ old('youtube', $recinto->youtube ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="instagram">Instagram</label>
                        <input type="url" id="instagram" name="instagram" class="form-control" placeholder="Instagram" value="{{ old('instagram', $recinto->instagram ?? '') }}">
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-group text-right">
                    <a href="{{ route('recintos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-danger">{{ isset($recinto) ? 'Actualizar' : 'Crear' }}</button>
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
        const initialLat = parseFloat(document.getElementById('lat').value) || 20.659698;
        const initialLng = parseFloat(document.getElementById('lng').value) || -103.349609;

        const map = new google.maps.Map(document.getElementById('map'), {
            center: {
                lat: initialLat,
                lng: initialLng
            },
            zoom: 13,
        });

        const input = document.getElementById('direccion');
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        const marker = new google.maps.Marker({
            map: map,
            position: {
                lat: initialLat,
                lng: initialLng
            },
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