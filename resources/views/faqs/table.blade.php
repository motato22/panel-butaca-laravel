<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Pregunta</th>
            <th>Respuesta</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle">{{$item->id}}</td>
                <td class="align-middle">{{$item->question}}</td>
                <td class="align-middle">{{$item->answer}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('configuracion/preguntas-frecuentes/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>