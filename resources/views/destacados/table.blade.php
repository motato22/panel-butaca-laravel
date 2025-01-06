<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th class="d-none">ID</th>
            <th>Propietario</th>
            <th>País</th>
            <th>Foto</th>
            <th>Clicks e impresiones</th>
            <th>Evento</th>
            <th>Categoría</th>
            <th>Fecha de caducidad</th>
            <th class="d-none">Fecha de creación</th>
            {{-- <th>Mostrar en app</th> --}}
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle d-none">{{$item->id}}</td>
                <td class="align-middle">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset($item->user->foto)}}" class="avatar-img avatar-sm rounded-circle" alt="Foto categoría">
                    </div>
                    <span class="ml-2">{{$item->user->fullname}}</span>
                </td>
                <td class="align-middle"><span class="badge badge-info">{{$item->pais->nombre}}</span></td>
                <td class="align-middle">
                    @if( $item->foto )
                    <a data-toggle="tooltip" data-placement="top" title="Ver foto de banner" href="{{asset($item->foto)}}" target="_blank"> <i class="mdi mdi-24px mdi-file-image"></i> </a>
                    @endif
                </td>
                <td class="align-middle">Clicks: {{$item->clicks}} / Impresiones: {{$item->cargado}}</td>
                <td class="align-middle">{!! $item->evento ? '<span class="badge badge-success">'.$item->evento->nombre.'</span>' : '<span class="badge badge-danger">Evento eliminado o no vigente</span>' !!}</td>
                <td class="align-middle">{!! $item->categoria ? '<span class="badge badge-success">'.$item->categoria->nombre.'</span>' : '<span class="badge badge-dark">No aplica</span>' !!}</td>
                <td class="align-middle">
                    {{strftime('%d', strtotime($item->fecha_caducidad)).' de '.strftime('%B', strtotime($item->fecha_caducidad)). ' del '.strftime('%Y', strtotime($item->fecha_caducidad))}}
                </td>
                <td class="align-middle d-none">
                    {{strftime('%d', strtotime($item->created_at)).' de '.strftime('%B', strtotime($item->created_at)). ' del '.strftime('%Y', strtotime($item->created_at))}}
                </td>
                <td class="text-center align-middle">
                    {{-- <a class="btn btn-dark btn-sm" href="{{url('banners/eventos-destacados/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a> --}}
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>