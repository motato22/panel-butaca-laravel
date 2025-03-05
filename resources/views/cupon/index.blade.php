@extends('layouts.main')

@section('title', 'Promociones | Notificaciones')

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
                <h1>
                    Promociones | Notificaciones
                    <a href="{{ route('cupon.create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
                        <i class="mdi mdi-plus mdi-18px"></i>
                    </a>
                </h1>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de contadores -->
<div class="container pull-up pb-5 mb-5">
    <div class="row">
        <!-- Card 1: Activos -->
        <div class="col-md-4 m-b-30">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-center p-t-20">
                        <div class="avatar-lg avatar">
                            <div class="avatar-title rounded-circle badge-soft-success">
                                <i class="mdi mdi-ticket-percent h1 m-0"></i>
                            </div>
                        </div>
                        <h1 class="fw-600 p-t-20">
                            {{ $meta['activos'] ?? 0 }}
                        </h1>
                        <p class="text-muted fw-600">Activos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Inactivos -->
        <div class="d-none d-md-inline-block col-md-4 m-b-30">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-center p-t-20">
                        <div class="avatar-lg avatar">
                            <div class="avatar-title rounded-circle badge-soft-danger">
                                <i class="mdi mdi-ticket-percent h1 m-0"></i>
                            </div>
                        </div>
                        <h1 class="fw-600 p-t-20">
                            {{ ($meta['total'] ?? 0) - ($meta['activos'] ?? 0) }}
                        </h1>
                        <p class="text-muted fw-600">Inactivos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Totales -->
        <div class="d-none d-md-inline-block col-md-4 m-b-30">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-center p-t-20">
                        <div class="avatar-lg avatar">
                            <div class="avatar-title rounded-circle badge-soft-info">
                                <i class="mdi mdi-ticket-percent h1 m-0"></i>
                            </div>
                        </div>
                        <h1 class="fw-600 p-t-20">
                            {{ $meta['total'] ?? 0 }}
                        </h1>
                        <p class="text-muted fw-600">Totales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Notificaciones -->
<div class="container-fluid pull-up pb-5 mb-5">
    <!-- Mensajes de Error/Éxito -->
    @if(session('error'))
        <div class="col-12">
            <div class="alert alert-danger text-center">
                <p class="m-0"><b>Error:</b> {{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="col-12">
            <div class="alert alert-success text-center">
                <p class="m-0"><b>Enhorabuena:</b> {{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Tabla: Notificaciones -->
    <div class="row">
        <div class="col-12">
            <h2 class="pb-3">Notificaciones</h2>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive p-t-10">
                        <table class="table default-table nowrap" style="width:100%" id="table_notificaciones">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Aplicado A</th>
                                    <th>Imagen</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notificaciones as $noti)
                                    <tr>
                                        <td>{{ $noti->mensaje }}</td>
                                        <td>{{ $noti->aplicadoA() }}</td>
                                        <td>
                                            @if($noti->imagen)
                                                <!-- Ajustamos para que se vea como en banners -->
                                                <img src="{{ asset('storage/uploads/notificaciones/' . $noti->imagen) }}"
                                                     alt="Imagen de la notificación"
                                                     class="img-thumbnail max-height-2">
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($noti->fecha)->format('Y-m-d H:i') }}
                                        </td>
                                        <td>
                                            <!-- Toggle activación -->
                                            <label class="cstm-switch m-b-15 ml-2">
                                                <input
                                                    type="checkbox"
                                                    class="cstm-switch-input action-toggle-activation-noti"
                                                    data-url="{{ route('notificacion.toggleActivacion', $noti->id) }}"
                                                    {{ $noti->activo ? 'checked' : '' }}>
                                                <span class="cstm-switch-indicator mdi-18px"></span>
                                            </label>

                                            <!-- Botón para Editar -->
                                            <a href="{{ route('notificacion.edit', $noti->id) }}"
                                               class="btn btn-sm m-b-15 ml-1 btn-primary py-0 px-1">
                                                <i class="mdi mdi-pencil mdi-18px"></i>
                                            </a>
                                            <!-- Botón para Eliminar -->
                                            <a href="#"
                                               class="btn btn-sm m-b-15 ml-1 btn-danger py-0 px-1 action-delete"
                                               onclick="event.preventDefault(); 
                                                        if(confirm('¿Seguro de eliminar esta notificación?')) {
                                                            document.getElementById('del-noti-{{ $noti->id }}').submit();
                                                        }">
                                                <i class="mdi mdi-trash-can mdi-18px"></i>
                                            </a>
                                            <form id="del-noti-{{ $noti->id }}"
                                                  action="{{ route('notificacion.destroy', $noti->id) }}"
                                                  method="POST"
                                                  style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No hay notificaciones disponibles.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div><!-- /.col-12 -->
    </div><!-- /.row -->
</div><!-- /.container-fluid -->

@push('scripts')
<script>
    $(document).ready(function() {
        // Iniciar DataTable
        $('#table_notificaciones').DataTable({
            dom: 'lfrtip',
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            order: [[3, 'desc']], // Ordena por la columna "Fecha" (índice 3)
            columnDefs: [
                { orderable: false, targets: [4] } // No ordenar la columna "Acciones"
            ],
            language: {
                lengthMenu: 'Mostrar _MENU_ registros',
                zeroRecords: 'No hay resultados',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'No hay registros disponibles',
                infoFiltered: '(filtrado de _MAX_ en total)',
                search: 'Buscar:',
                paginate: {
                    previous: 'Anterior',
                    next: 'Siguiente'
                }
            }
        });

        // Toggle de activación en Notificaciones (AJAX)
        $('.action-toggle-activation-noti').on('change', function() {
            let urlToggle = $(this).data('url');
            $.ajax({
                url: urlToggle,
                method: 'GET',
                success: function(res) {
                    console.log("Notificación: Activación cambiada -> ", res.activo);
                },
                error: function(err) {
                    alert("Ocurrió un error al cambiar la activación de la notificación");
                }
            });
        });
    });
</script>
@endpush

@endsection
