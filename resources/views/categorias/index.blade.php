@extends('layouts.main')

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
                <h1>Categorías
                    <a href="{{ url('categorias/create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
                        <i class="mdi mdi-plus mdi-18px"></i>
                    </a>
                </h1>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pull-up pb-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive p-t-10">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Géneros</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorias as $categoria)
                                <tr>
                                    <td>{{$categoria->nombre}}</td>
                                    <td style="max-width: 50%">

                                        @foreach($categoria->generos as $genero)
                                        <div class="btn ml-2 my-2 badge badge-soft-dark">
                                            {{ $genero->nombre }}

                                            {{-- Botón para abrir el modal de edición --}}
                                            <a href="javascript:void(0)" class="editar-genero text-primary ml-2"
                                                data-id="{{ $genero->id }}" data-nombre="{{ $genero->nombre }}">
                                                <i class="icon-placeholder mdi mdi-pencil-box-outline"></i>
                                            </a>

                                            {{-- Botón para eliminar género --}}
                                            <a href="javascript:void(0)" class="delete-genero text-danger"
                                                data-id="{{ $genero->id }}">
                                                <i class="icon-placeholder mdi mdi-minus-box-outline"></i>
                                            </a>
                                        </div>
                                        @endforeach


                                        <div class="collapse">


                                            <div class="btn ml-2 my-2 badge badge-soft-dark">

                                                <a class="editar-genero text-primary ml-2"></a>

                                                <i class="icon-placeholder mdi mdi-pencil-box-outline"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="delete-genero text-danger">
                                                    <i class="icon-placeholder mdi mdi-minus-box-outline"></i>
                                                </a>
                                            </div>


                                        </div>

                                        <a class="btn py-0 btn-sm btn-outline-primary ml-2 collapsed">
                                            ...
                                        </a>

                                    </td>
                                    <td>
                                        {{$categoria->updated_at->format('d/m/Y')}}
                                    </td>
                                    <td>

                                        <div class="d-flex flex-column align-items-start">


                                            <div class="mb-2">
                                                <!-- Botón para editar la categoría -->
                                                <a href="/categorias/{{$categoria->id}}/edit" class="btn btn-sm m-b-15 ml-1 btn-primary py-0 px-1">
                                                    <i class="mdi mdi-pencil mdi-18px"></i>
                                                </a>


                                                <!-- Botón para eliminar la categoría -->
                                                <form action="{{ route('categorias.delete', $categoria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm ml-1 m-b-15 btn-danger py-0 px-1 action-delete">
                                                        <i class="mdi mdi-tag-minus mdi-18px"></i>
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    </td>

                                </tr>
                                @endforeach
                                <!-- Modal para editar género -->
                                <div class="modal fade" id="modalEditarGenero" tabindex="-1" role="dialog" aria-labelledby="modalEditarGeneroLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEditarGeneroLabel">Editar Género</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="formEditarGenero">
                                                    @csrf
                                                    <input type="hidden" id="genero_id" name="genero_id">
                                                    <div class="form-group">
                                                        <label for="genero_nombre">Nombre del Género</label>
                                                        <input type="text" id="genero_nombre" name="genero_nombre" class="form-control" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Función para abrir el modal de edición con los datos del género seleccionado
    document.querySelectorAll('.editar-genero').forEach(button => {
        button.addEventListener('click', function () {
            let generoId = this.getAttribute('data-id');
            let generoNombre = this.getAttribute('data-nombre');

            document.getElementById('genero_id').value = generoId;
            document.getElementById('genero_nombre').value = generoNombre;

            $('#modalEditarGenero').modal('show'); // Abre el modal
        });
    });

    // Función para manejar el envío del formulario de edición
    document.getElementById('formEditarGenero').addEventListener('submit', function (event) {
        event.preventDefault();

        let generoId = document.getElementById('genero_id').value;
        let nuevoNombre = document.getElementById('genero_nombre').value;

        fetch(`/generos/${generoId}/editar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ nombre: nuevoNombre })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recargar la página para ver los cambios
            } else {
                alert('Error al actualizar el género.');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Función para eliminar un género
    document.querySelectorAll('.delete-genero').forEach(button => {
        button.addEventListener('click', function () {
            let generoId = this.getAttribute('data-id');

            if (confirm("¿Estás seguro de que deseas eliminar este género?")) {
                fetch(`/generos/${generoId}/eliminar`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Recargar la página para ver los cambios
                    } else {
                        alert('Error al eliminar el género.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});

</script>

@endsection