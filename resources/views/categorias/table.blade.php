<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Cantidad de eventos asociados</th>
            <th>Mostrar en app</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle">{{$item->id}}</td>
                <td class="align-middle">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset($item->foto)}}" class="avatar-img avatar-sm rounded-circle" alt="Foto categoría">
                    </div>
                    <span class="ml-2">{{$item->nombre}}</span>
                </td>
                <td class="align-middle"><span class="badge badge-soft-dark">{{$item->eventos->count()}}</span></td>
                <td class="align-middle">{!! $item->mostrar == 'S' ? '<span class="badge badge-success">Si</span>' : '<span class="badge badge-danger">No</span>' !!}</td>
                {{-- <td class="align-middle">
                    <label class="cstm-switch">
                        <input type="checkbox" {{$item->mostrar == 'S' ? 'checked' : ''}} data-row-id="{{$item->id}}" name="option" value="1" class="cstm-switch-input checkbox checkbox{{$item->id}}">
                        <span class="cstm-switch-indicator bg-info"></span>
                        <span class="cstm-switch-description"></span>
                    </label>
                </td> --}}
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('categorias/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>