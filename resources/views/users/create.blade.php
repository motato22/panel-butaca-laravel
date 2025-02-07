@extends('layouts.main')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-account-plus" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>@yield('titulo_contenido', 'Nuevo Usuario')</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                @if(session('error'))
                <div class="col-12">
                    <div class="alert alert-danger" style="margin-left: auto; margin-right: auto">
                        <p><b>No se pudo guardar el usuario</b>: {{ session('error') }}</p>
                    </div>
                </div>
                @endif

                <div class="col-12 pt-4">

                    {{-- Formulario --}}
                    <form method="POST" action="{{ route('users.add') }}">
                        @csrf

                        <div class="form-row">
                            <!-- Nombre Completo -->
                            <div class="form-group col-md-6">
                                <label for="nombre">Nombre Completo</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="nombre"
                                    name="nombre"
                                    maxlength="120"
                                    placeholder="Nombre y apellido"
                                    value="{{ old('nombre') }}"
                                    required>
                                <small class="form-text text-muted">Nombre a mostrar en pantalla (cliente).</small>
                            </div>

                            <!-- Username -->
                            <div class="form-group col-md-6">
                                <label for="username">Username</label>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    class="form-control"
                                    maxlength="60"
                                    placeholder="Nombre corto para acceso."
                                    value="{{ old('username') }}"
                                    required>
                                <small class="form-text text-muted">Nombre clave para acceso al panel/webapp/app movil.</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Correo Electrónico -->
                            <div class="form-group col-md-6">
                                <label for="correo">Correo electrónico</label>
                                <input
                                    type="email"
                                    id="correo"
                                    name="correo"
                                    class="form-control"
                                    maxlength="120"
                                    value="{{ old('correo') }}"
                                    required>
                                <small class="form-text text-muted">Correo de acceso. Envío de información.</small>
                            </div>

                            <!-- Contraseña -->
                            <div class="form-group col-md-6">
                                <label for="plainPassword">Contraseña</label>
                                <input
                                    type="password"
                                    id="plainPassword"
                                    name="plainPassword"
                                    class="form-control"
                                    maxlength="60">
                                <small class="form-text text-muted">Contraseña de acceso. Deja en blanco para generar automáticamente una segura.</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Tipo de Acceso -->
                            <div class="form-group col-md-6">
                                <label for="role">Tipo de Acceso</label>
                                <select name="role" class="form-control" required>
                                    <option value="ROLE_USER">Usuario de Apps</option>
                                    <option value="ROLE_RECINTO">Administrador Recinto</option>
                                    <option value="ROLE_ADMIN">Administrador</option>
                                </select>
                                <small class="form-text text-muted">Tipo de acceso. Usuarios de Apps no tienen acceso al panel.</small>
                            </div>

                            <!-- Nombre Segmento -->
                            <div class="form-group col-md-6">
                                <label for="segmento">Nombre Segmento</label>
                                <input
                                    type="text"
                                    id="segmento"
                                    name="segmento"
                                    class="form-control"
                                    maxlength="80"
                                    placeholder="Segmento para notificaciones"
                                    value="{{ old('segmento', 'General') }}"
                                    readonly>
                                <small class="form-text text-muted">Segmento a mostrar en pantalla (cliente).</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-danger float-right mx-1">Crear</button>
                            <a href="{{ url('users') }}" class="btn btn-secondary float-right mx-1">Cancelar</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection