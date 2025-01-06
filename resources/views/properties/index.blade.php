@extends('layouts.main')

@section('content')
@include('properties.modal')
<style type="text/css">
    /*.list-group-item:after {
        display: inline-block!important;
    }*/
    .datepicker {
      z-index: 1600 !important; /* has to be larger than 1050 */
    }
    td.disabled {
        color: red!important;
    }
</style>
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Propiedades </h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y gestionar la información de las propiedades vigentes de la app.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("propiedades")}}" data-refresh="table" data-el-loader="card">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            {{-- Table --}}
            <div class="col-lg-12 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Lista de propiedades</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="btn btn-dark filter-rows"> <i class="mdi mdi-filter-variant"></i> Filtrar</a>
                            <a href="javascript:;" class="btn btn-info export-rows"> <i class="mdi mdi-file-excel"></i> Exportar</a>
                            <a href="{{url('propiedades/form')}}"><button class="btn btn-success" type="button"> <i class="mdi mdi-open-in-new"></i> Nuevo registro</button></a>
                        </div>
                        <div class="row m-b-20">
                            <div class="col-md-3 my-auto">
                                <h4 class="m-0">Filtros</h4>
                            </div>
                            <div class="col-md-9 text-right my-auto filter-section">
                                <div class="btn-group row" role="group" aria-label="Basic example">
                                    <div class="no-pad col-md-3" style="text-align: left;">
                                        <select id="project_id" name="project_id" class="form-control select2" data-msg="Proyecto">
                                            <option value="" selected>Proyecto (Cualquiera)</option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}">{{$project->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad col-md-3" style="text-align: left;">
                                        <select id="owner_id" name="owner_id" class="form-control select2" data-msg="Propietario">
                                            <option value="" selected>Propietario (Cualquiera)</option>
                                            @foreach($owners as $owner)
                                                <option value="{{$owner->id}}">{{$owner->fullname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad col-md-3">
                                        <input type="text" class="date-picker form-control" name="fecha_inicio" autocomplete="off" placeholder="Fecha inicio">
                                    </div>
                                    <div class="no-pad col-md-3">
                                        <input type="text" class="date-picker form-control" name="fecha_fin" autocomplete="off" placeholder="Fecha fin">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('properties.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    // Get state account
    $('body').delegate('.view-charges-payments', 'click', function() {
        var id = $(this).parent().siblings("td:nth-child(1)").text();

        config = {
            'id'        : id,
            'keepModal' : true,
            'route'     : baseUrl.concat('/propiedades/state-account'),
            'method'    : 'POST',
            'callback'  : 'displayChargesPayments',
        }

        loadingMessage('Espere un momento...');

        ajaxSimple(config);
    });

    // Change pay day
    $('body').delegate('.change-pay-day', 'click', function() {
        var id = $(this).parent().siblings("td:nth-child(1)").text();

        config = {
            'id'        : id,
            'keepModal' : true,
            'route'     : baseUrl.concat('/propiedades/state-account'),
            'method'    : 'POST',
            'callback'  : 'showModalChangePayDay',
        }

        loadingMessage('Espere un momento...');

        ajaxSimple(config);
    });

    // Listener que recalcula los pagos pendientes cada que se selecciona una nueva fecha
    $('body').delegate('.new-date','change', function() {
        let initDate = $(this).val();
        let count = 0;
        if (! initDate ) {
            console.log('Fecha inválida');
            return;
        }
        let table = $('table.new-date-installments tbody tr');
        let price = Number( parseFloat( $('[name="price"]').val() ).toFixed(2) );
        let initDateMoment = moment( initDate ).locale("es");
        // console.log('Fecha a partir de:', initDateMoment.format('YYYY-MM-DD'));

        table.each(function( index ) {
            let newDate = null;
            if ( count == 0 ) { newDate = initDateMoment; }
            else { newDate = initDateMoment.add(1, 'month'); }
            $(this).children().siblings("td:nth-child(4)").text( newDate.format('DD [de] MMMM [del] YYYY') );// Se cambia la fecha, esto es solo ilustrativo
            $(this).children().siblings("td:nth-child(4)").data( 'date', newDate.format('YYYY-MM-DD' ) );// Se cambia la fecha, esto es solo ilustrativo
            count++;
        });
    });

    //Display charges and payments for a contract
    function showModalChangePayDay(response, config) {
        $( ".new-date" ).datepicker( "destroy" ); //Destruye las propiedades del datepicker
        $("table.new-date-installments tbody").children().remove();
        $('[name="row_id"]').val(response.id);
        $('[name="property_name"]').val(response.name);
        let count = 0;
        let startDate = null;
        charges = response.installments;
        
        if ( charges.length > 0 ) {
            for ( var key_2 in charges ) {
                if ( charges.hasOwnProperty(key_2) ) {
                    let chargeRow = charges[key_2];
                    if ( chargeRow.status.id != 1 ) {
                        let paymentDate = moment(chargeRow.date);
                        let dateFormate = paymentDate.format('DD [de] MMMM [del] YYYY');
                        let amount      = Number(chargeRow.amount);
                        let amountPaid  = Number(chargeRow.amount_paid);
                        let statusHTML  = '<span class="badge badge-'+(chargeRow.status.class)+'">'+chargeRow.status.name+'</span>';
                        if ( count == 0 ) { startDate = paymentDate.format('YYYY-MM-DD'); }
                        count++;
                        $("table.new-date-installments tbody").append(
                            '<tr class="installment" data-id="'+chargeRow.id+'" id="id_detail'+chargeRow.id+'">'+
                                '<td>'+(count)+'</td>'+
                                '<td>$'+(parseFloat(amount - amountPaid).toFixed(2))+'</td>'+
                                '<td>'+(statusHTML)+'</td>'+
                                '<td>'+(dateFormate)+'</td>'+
                            '</tr>'
                        );
                    }
                }
            }
        } else {
            $("table.new-date-installments tbody").append(
                '<tr>'+
                    '<td class="text-center" color="red" colspan="4">No se han registrado cargos.</td>'+
                '</tr>'
            ); 
        }

        // Se setea el rango mínimo de pago
        $( ".new-date" ).datepicker({
            startDate: startDate,
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd",
        });

        $('#change-pay-day').modal('show');
    }

    // Send custom ajax request for change pay dates for installments
    function changePayDay() {
        let id          = $('#change-pay-day [name="row_id"]').val();// Property id
        let new_date    = $('#change-pay-day [name="new_date"]').val();
        let route       = baseUrl.concat('/pagos/change-pay-day');
        let newDates    = [];

        $('table.new-date-installments tbody').children('tr.installment').each(function( index ) {
            let id = $(this).data('id');
            console.log(id);
            // let id = $(this).children().siblings("td:nth-child(1)").text();
            let date = $(this).children().siblings("td:nth-child(4)").data( 'date' );
            // let amount = Number( $(this).children().siblings("td:nth-child(3)").text() );
            let installmentObj = {
                id     : id,
                date   : date,
            };
            newDates.push(installmentObj);
            // console.log('installmentObj', installmentObj);
        });
        if ( ! new_date || new_date <= 0 || new_date >= 28 ) {
            infoMsg('warning', 'Día de pago inválido.', 'Asigne un día de pago entre 1 y 28 de cada mes');
            return;
        }

        if ( ! newDates.length ) {
            infoMsg('warning', 'Error', 'Debe tener al menos una fecha por modificar');
            return;
        }
        let config = {
            'id'          : id,
            'method'      : 'POST',
            'new_date'    : new_date,
            'new_dates'   : newDates,
            'route'       : route,
            'refresh'     : 'table',
        }

        loadingMessage();
        ajaxSimple(config);
    }

    // Display charges and payments for a contract
    function displayChargesPayments(response, config) {
        $("table.payments tbody").children().remove();
        $("table.charges tbody").children().remove();
        
        payments = response.payments;
        charges = response.installments;
        
        if ( payments.length > 0 ) {
            for (var key in payments) {
                if (payments.hasOwnProperty(key)) {
                    let chargeRow   = payments[key];
                    let chargeDate = moment(chargeRow.payment_date);
                    let dateFormate = chargeDate.format('DD [de] MMMM [del] YYYY');
                    let amount      = Number(chargeRow.amount).toFixed(2);
                    let statusHTML  = '<span class="badge badge-'+(chargeRow.status.class)+'">'+chargeRow.status.name+'</span>';
                    $("table.payments tbody").append(
                        '<tr id="id_detail'+chargeRow.id+'">'+
                            '<td>'+(parseFloat(key)+1)+'</td>'+
                            '<td>$'+(amount)+'</td>'+
                            '<td>'+(statusHTML)+'</td>'+
                            '<td>'+(dateFormate)+'</td>'+
                            // '<td><button class="btn btn-danger m-b-15 btn-sm detele-row-charge" data-url="/pagos/delete" data-parent="table.charges tbody" data-row-id="'+chargeRow.id+'" data-row-descripcion="'+charges[key_2].amount_str+'" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button></td>'+
                        '</tr>'
                    );
                }
            }
        } else {
            $("table.payments tbody").append(
                '<tr>'+
                    '<td class="text-center" color="red" colspan="4">No se han registrado pagos.</td>'+
                '</tr>'
            ); 
        }

        if ( charges.length > 0 ) {
            for (var key_2 in charges) {
                if (charges.hasOwnProperty(key_2)) {
                    let chargeRow   = charges[key_2];
                    let paymentDate = moment(chargeRow.date);
                    let dateFormate = paymentDate.format('DD [de] MMMM [del] YYYY');
                    let amount      = Number(chargeRow.amount);
                    let amountPaid  = Number(chargeRow.amount_paid);
                    let statusHTML  = '<span class="badge badge-'+(chargeRow.status.class)+'">'+chargeRow.status.name+'</span>';
                    $("table.charges tbody").append(
                        '<tr id="id_detail'+chargeRow.id+'">'+
                            '<td>'+(parseFloat(key_2)+1)+'</td>'+
                            '<td>$'+(parseFloat(amount - amountPaid).toFixed(2))+'</td>'+
                            '<td>'+(statusHTML)+'</td>'+
                            '<td>'+(dateFormate)+'</td>'+
                            // '<td><button class="btn btn-danger m-b-15 btn-sm detele-row-charge" data-url="/pagos/delete" data-parent="table.charges tbody" data-row-id="'+chargeRow.id+'" data-row-descripcion="'+charges[key_2].amount_str+'" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button></td>'+
                        '</tr>'
                    );
                }
            }
        } else {
            $("table.charges tbody").append(
                '<tr>'+
                    '<td class="text-center" color="red" colspan="4">No se han registrado cargos.</td>'+
                '</tr>'
            ); 
        }

        $('#view-charges-payments').modal('show');
    }
</script>
@endsection