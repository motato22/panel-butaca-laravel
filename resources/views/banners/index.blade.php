@extends('layouts.main')

@section('title', 'Banners')

@section('content')
<div class="bg-dark m-b-30">
    <div class="container">
        <div class="row p-b-60 p-t-60">
            <div class="col-md-10 mx-auto text-center text-white p-b-30">
                <div class="m-b-20">
                    <div class="avatar avatar-xl my-auto">
                        <i class="icon-placeholder mdi mdi-page-layout-header" style="font-size: 5rem"></i>
                    </div>
                </div>
                <h1>
                    Banners
                    <a href="{{ route('banners.create') }}" class="btn btn-rounded-circle py-1 btn-outline-primary ml-2">
                        <i class="mdi mdi-plus mdi-18px"></i>
                    </a>
                </h1>
            </div>
        </div>
    </div>
</div>

{{-- Estadísticas --}}
<div class="container pull-up pb-5 mb-5">
    <div class="row">
        <div class="col-md-4 m-b-30">
            <div class="card card-hover">
                <div class="card-body">
                    <div class="text-center p-t-20">
                        <div class="avatar-lg avatar">
                            <div class="avatar-title rounded-circle badge-soft-success">
                                <i class="mdi mdi-page-layout-header h1 m-0"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h1 class="fw-600 p-t-20">{{ $activos }}</h1>
                            <p class="text-muted fw-600">Activos</p>
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
                                <i class="mdi mdi-page-layout-header h1 m-0"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h1 class="fw-600 p-t-20">{{ $inactivos }}</h1>
                            <p class="text-muted fw-600">Inactivos</p>
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
                                <i class="mdi mdi-page-layout-header h1 m-0"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h1 class="fw-600 p-t-20">{{ $activos + $inactivos }}</h1>
                            <p class="text-muted fw-600">Totales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="container-fluid pull-up pb-5 mb-5">
    @if(session('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive p-t-10">
                        <table class="table default-table2 nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Descripción</th>
                                    <th>Ubicación</th>
                                    <th>Imagen</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($banners as $banner)
                                <tr>
                                    <td>{{ $banner->id }}</td>
                                    <td>{{ $banner->descripcion }}</td>
                                    <td>{{ $banner->ubicacion == 0 ? 'Arriba' : 'Abajo' }}</td>
                                    <td>
                                        @if($banner->imagen)
                                        <img src="{{ asset('storage/uploads/banners/' . $banner->imagen) }}" alt="Imagen del banner" class="img-thumbnail max-height-2">
                                        @endif
                                    </td>
                                    <td>{{ $banner->fecha_inicio ?? 'No definida' }}</td>
                                    <td>{{ $banner->fecha_fin ?? 'No definida' }}</td>
                                    <td>
                                        @if($banner->fecha_creacion)
                                        {{ $banner->fecha_creacion->format('Y-m-d H:i') }}
                                        @else
                                        No definida
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('banners.edit', $banner->id) }}"
                                            class="btn btn-sm m-b-15 ml-1 btn-primary py-0 px-1">
                                            <i class="mdi mdi-pencil mdi-18px"></i>
                                        </a>

                                        <form
                                            action="{{ route('banners.destroy', $banner->id) }}"
                                            method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar el banner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm ml-1 m-b-15 btn-danger py-0 px-1" type="submit">
                                                <i class="mdi mdi-minus mdi-18px"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- si usas paginate() en vez de get(): --}}
                        {{-- {{ $banners->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        $('.default-table2').DataTable({
            responsive: true,
            order: [
                [0, 'desc']
            ]
        });
    });
</script>
@endpush