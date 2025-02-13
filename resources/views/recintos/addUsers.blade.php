@extends('layouts.main')

@section('title', 'Usuarios Add')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-tag-multiple" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>{{ $recinto->nombre }}</h1>
                <h4>Agregar usuarios</h4>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pull-up pb-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Formulario para agregar usuario -->
                    <form method="POST" action="{{ route('recintos.addUsers', $recinto->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control" name="user" required>
                                    <option value="" disabled selected>Seleccionar usuario</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary">Agregar</button>
                            </div>
                        </div>
                    </form>

                    <br>

                    <!-- Tabla de usuarios agregados -->
                    <div class="table-responsive p-t-10">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr class="text-center">
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Username</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recinto->users as $usuario)
                                <tr class="text-center">
                                    <td>{{ $usuario->nombre }}</td>
                                    <td>{{ $usuario->correo }}</td>
                                    <td>{{ $usuario->username }}</td>
                                    <td>
                                        <form action="{{ route('recintos.removeUser', ['recinto' => $recinto->id, 'user' => $usuario->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario del recinto?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay usuarios agregados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> <!-- End Table -->
                    <div class="text-center mt-4">
                        <a href="{{ route('recintos.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Regresar a Recintos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection