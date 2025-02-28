@extends('layouts.main')

@section('title', 'Editar Zona Recinto')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-map" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>Editar Zona Recinto</h1>
            </div>
        </div>
    </div>
</div>

<div class="container pull-up pb-5 mb-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                
                <div class="col-12 pt-4">

                    {{-- Errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <p><b>No se pudo actualizar la zona</b>:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('zonas.update', $zona->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <div>
                                    <label for="zona" class="required">Zona</label>
                                    <input
                                        type="text"
                                        id="zona"
                                        name="zona"
                                        required
                                        class="form-control"
                                        maxlength="120"
                                        value="{{ old('zona', $zona->zona) }}"
                                    />
                                </div>

                                <div class="mt-3">
                                    <label for="tipo">Tipo</label>
                                    <select
                                        id="tipo"
                                        name="tipo"
                                        class="form-control"
                                    >
                                        <option value="">Sin tipo de zona</option>
                                        @foreach($tipos as $t)
                                            <option
                                                value="{{ $t->id }}"
                                                {{ old('tipo', $zona->tipo) == $t->id ? 'selected' : '' }}
                                            >
                                                {{ $t->tipo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            {{-- Botones de acción --}}
                            <button type="submit" class="btn btn-danger float-right mx-1">
                                Actualizar
                            </button>
                            <a href="{{ route('zonas.index') }}" class="btn btn-secondary float-right mx-1">
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
