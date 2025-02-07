@extends('layouts.main')

@section('title','Usuarios')


@section('content')


<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">

            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-account-group" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>Usuarios
                <a href="{{url ('users/create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
                    @csrf
                        <i class="mdi mdi-plus mdi-18px"></i>
                    </a>
                </h1>
            </div>

        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">

    <div class="row">

        <div class="col-md-4 m-b-30">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-center p-t-20">
                        <div class="avatar-lg avatar">
                            <div class="avatar-title rounded-circle badge-soft-success">
                                <i class="mdi mdi-account-multiple h1 m-0"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-muted fw-600">Usuarios Activos</p>
                            <h3>{{ $usuariosActivos }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none d-md-inline-block col-md-4 m-b-30">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-center p-t-20">
                        <div class="avatar-lg avatar">
                            <div class="avatar-title rounded-circle badge-soft-danger">
                                <i class="mdi mdi-account-multiple h1 m-0"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-muted fw-600">Usuarios Inactivos</p>
                            <h3>{{ $usuariosInactivos }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none d-md-inline-block col-md-4 m-b-30">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-center p-t-20">
                        <div class="avatar-lg avatar">
                            <div class="avatar-title rounded-circle badge-soft-info">
                                <i class="mdi mdi-account-multiple h1 m-0"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-muted fw-600">Usuarios Totales</p>
                            <h3>{{ $usuariosTotales }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="container-fluid pull-up pb-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="col-12 text-right">
                        <a data-toggle="modal" href="javascript:void(0)" id="mostrarEnviarNotificaciones"
                           data-target="#enviar-notificaciones"
                           class="btn btn-rounded-circle m-b-15 py-0 btn-sm btn-outline-primary">
                            <i class="mdi mdi-send mdi-18px"></i>
                        </a>
                    </div>
                    @foreach ((array) session('error', []) as $message)
    <div class="col-12">
        <div class="alert alert-danger" style="margin-left: auto; margin-right: auto">
            <p class="m-0 text-center"><b>Error</b>:  {{ $message }}</p>
        </div>
    </div>
@endforeach

@foreach ((array) session('success', []) as $message)
    <div class="col-12">
        <div class="alert alert-success" style="margin-left: auto; margin-right: auto">
            <p class="m-0 text-center"><b>Enhorabuena</b>:  {{ $message }}</p>
        </div>
    </div>
@endforeach

                    <h3 class="py-3">Usuarios Administradores</h3>

                    <div class="table-responsive p-t-10">
                        <table class="table default-table nowrap" style="width:100%" id="miTablaAdmin">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Rol</th>
                                    <th>Segmento</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuariosAdmin as $usuario)
                                    <tr id="tr_{{ $usuario->id }}">
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $usuario->correo }}</td>
                                        <td>{{ $usuario->role }}</td>
                                        <td>{{ $usuario->segmento }}</td>
                                        <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                                        <td>
                                        <button class="btn btn-danger btn-sm" onclick="eliminar({{ $usuario->id }})">Eliminar</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>


                    <hr/>

                    <h3 class="py-3">Usuarios Apps</h3>
                    
                    <div class="table-responsive p-t-10">
                        <!-- Formulario de búsqueda y ordenación -->
                        <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                            <div class="row">
                                <!-- Campo de búsqueda -->
                                <div class="col-md-6">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Buscar por nombre o correo" 
                                        value="{{ request('search') }}">
                                </div>

                                <!-- Opciones de ordenación -->
                                <div class="col-md-4">
                                    <select name="order_by" class="form-control">
                                        <option value="nombre" {{ request('order_by') == 'nombre' ? 'selected' : '' }}>Ordenar por Nombre</option>
                                        <option value="correo" {{ request('order_by') == 'correo' ? 'selected' : '' }}>Ordenar por Correo</option>
                                        <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>Ordenar por Fecha</option>
                                    </select>
                                </div>

                                <!-- Botón de búsqueda -->
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                                </div>
                            </div>
                        </form>

                    <div class="table-responsive p-t-10">
                        <table class="table default-table nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Segmento</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuariosApp as $usuario)
                                    <tr>
                                        <td>{{ $usuario->nombre }}</td>
                                        <td>{{ $usuario->correo }}</td>
                                        <td>{{ $usuario->segmento }}</td>
                                        <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" onclick="eliminar({{ $usuario->id }})">Eliminar</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-6">
                        {{ $usuariosApp->links('pagination::bootstrap-4') }}
                        </div>
                    </div>


<div class="modal body" id="enviar-notificaciones" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title mx-auto" id="modalLabel">Enviar Notificaciones</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="" method="POST" action="">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="mensaje">Mensaje:</label>
                                            <textarea class="form-control" id="mensaje" name="mensaje" rows="3" required></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group col-auto">
                                        <div class="input-group input-group-flush mb-3">
                                            <input type="text" id="titulo-2" name="titulo"
                                                class="form-control form-control-prepended"
                                                placeholder="Ej. Bienvenido">
                                            <div class="input-group-prepend mx-auto my-auto">
                                                <div class="input-group-text">
                                                    Título
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group col-auto">

                                        <div class="input-group input-group-flush mb-3">
                                            <input type="text" id="mensajeo-2" name="mensaje"
                                                class="form-control form-control-prepended"
                                                placeholder="Ej. Te damos la bienvenida a Butaca!">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Mensaje
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    
                                    <div class="form-group col-auto mx-auto my-auto">
                                        <div class="input-group mb-2">
                                            <div class="custom-file">
                                                <label class="custom-file-label" for="categoria_form_thumbnailFile">Imagen del evento</label>
                                                <input type="file" id="imagen-notificacion-2" name="imagen" class="custom-file-input" accept=".png,.jpg,.svg" lang="es">
                                                <p class="help-text position-absolute">Imagen cuadrada (recomendado: 512px x 512px).</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 col-auto mx-auto my-auto">
                                    <button type="submit" class="btn text-uppercase btn-block btn-primary">Enviar</button>
                                    <button type="button" class="btn text-uppercase btn-block btn-secondary" data-dismiss="modal">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        function eliminar(id) {
            $("#tr_" + id).remove();
        }

        document.getElementById('mostrarEnviarNotificaciones').addEventListener('click', function () {
            $('#enviar-notificaciones').modal('show');
        });

        document.getElementById('formEnviarNotificaciones').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Notificaciones enviadas con éxito');
                    $('#enviar-notificaciones').modal('hide');
                } else {
                    alert('Error al enviar notificaciones');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
@endpush



@push('scripts')
<script>
        function eliminar(id) {
            $("#tr_" + id).remove();
        }
        $(document).ready(function() {
            // Tu código aquí
            console.log("El DOM está listo");

            $("#mostrarEnviarNotificaciones").click(function() {
                $('#enviar-notificaciones').modal('show');
            })
        });
    </script>
@endpush
