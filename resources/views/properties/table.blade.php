<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Proyecto</th>
            <th>Propiedad</th>
            <th>Propietario</th>
            <th>Costo propiedad</th>
            <th>Enganche</th>
            <th>Abonado</th>
            <th>Fecha de creación</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle">{{$item->id}}</td>
                <td class="align-middle">
                    {!! $item->project ? '<span class="badge badge-info">'.$item->project->name.'</span>' : '<span class="badge badge-danger">N/A</span>' !!}
                </td>
                <td class="align-middle">
                    {!! $item->owner ? '<span class="badge badge-info">'.$item->owner->fullname.'</span>' : '<span class="badge badge-danger">Sin propietario</span>' !!}
                </td>
                <td class="align-middle">{{$item->name}}</td>
                <td class="align-middle">${{$item->price}}</td>
                <td class="align-middle">
                    {!! $item->pay_in_advance ? '<span class="badge badge-info">$'.$item->pay_in_advance.'</span>' : '<span class="badge badge-danger">N/A</span>' !!}
                </td>
                <td class="align-middle">
                    {!! $item->payments->count() ? '<span class="badge badge-info">$'.$item->payments->sum('amount').'</span>' : '<span class="badge badge-danger">N/A</span>' !!}
                </td>
                <td class="align-middle">{{strftime('%d', strtotime($item->created_at)).' de '.strftime('%B', strtotime($item->created_at)). ' del '.strftime('%Y', strtotime($item->created_at))}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('propiedades/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-secondary btn-sm view-charges-payments" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Ver cargos y pagos"><i class="mdi mdi-cash-multiple"></i></button>
                    {{-- This property has installments pending --}}
                    @if(! count( $item->installments ) )
                        <a class="btn btn-success btn-sm" href="{{url('propiedades/crear-plan-de-pagos/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Crear plan de pagos"><i class="mdi mdi-calendar-text"></i></a>
                    @else
                        <button class="btn btn-warning btn-sm change-pay-day" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Cambiar día de pago"><i class="mdi mdi-calendar-edit"></i></button>
                        {{-- <button class="btn btn-warning btn-sm remove-installments-plan" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar plan de pagos"><i class="mdi mdi-lock-reset"></i></button> --}}
                    @endif
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>