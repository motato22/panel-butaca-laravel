@extends('layouts.main')

@section('title', 'Zona de recintos')



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
                <h1>Zona de recintos
                    <a href="{{ route('zonas.create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
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
                                <tr class="text-center">
                                    <th>id</th>
                                    <th>Zona</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zonas as $zona)
                                <tr class="text-center">
                                    <td>{{ $zona->id }}</td>
                                    <td>{{ $zona->zona }}</td>
                                    <td>{{ optional($zona->tipoZona)->tipo }}</td>
                                    <td>
                                        <a href="{{ route('zonas.edit', $zona->id) }}" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-pencil mdi-18px"></i>
                                        </a>
                                        <form action="{{ route('zonas.destroy', $zona->id) }}"
                                            method="POST"
                                            style="display:inline-block"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar la zona {{ $zona->zona }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="mdi mdi-minus mdi-18px"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


<!-- @section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ejemplo de confirmación de borrado
        const deleteButtons = document.querySelectorAll('.action-delete');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const zonaNombre = this.dataset.nombre;
                const urlEliminar = this.dataset.url;

                if (confirm(`¿Seguro que deseas eliminar la zona "${zonaNombre}"?`)) {
                    // Aquí puedes hacer un form dinámico o usar fetch para enviar DELETE
                    // Ejemplo con fetch:
                    fetch(urlEliminar, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(resp => {
                            if (resp.ok) {
                                // recargar la página o eliminar la fila
                                location.reload();
                            } else {
                                alert('Error al eliminar la zona.');
                            }
                        })
                        .catch(err => console.error(err));
                }
            });
        });
    });
</script>
@endsection -->