<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th class="d-none">ID</th>
            <th>Link</th>
            <th>Status</th>
            <th>Fecha de registro</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="d-none">{{$item->id}}</td>
                <td class="align-middle">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset($item->photo)}}" class="avatar-img avatar-sm rounded-circle" alt="user-image">
                    </div>
                    <span class="ml-2">{{$item->fullname}}</span>
                </td>
                <td class="align-middle">{!! $item->deleted_at ? '<span class="badge badge-danger">Dado de baja</span>' : '<span class="badge badge-success">Activo</span>' !!}</td>
                <td class="align-middle">{{strftime('%d', strtotime($item->created_at)).' de '.strftime('%B', strtotime($item->created_at)). ' del '.strftime('%Y', strtotime($item->created_at))}}</td>
                <td class="text-center align-middle">
                    @if(! $item->deleted_at )
                        <a class="btn btn-dark btn-sm" href="{{url('prensa/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                        <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Deshabilitar"><i class="mdi mdi-close-circle"></i></button>
                    @else
                        <button class="btn btn-success btn-sm enable-row" data-row-id="{{$item->id}}" data-change-to="1" data-toggle="tooltip" data-placement="top" title="Habilitar"><i class="mdi mdi-check"></i></button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>