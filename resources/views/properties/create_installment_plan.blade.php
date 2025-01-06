@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Crear plan de pagos</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('propiedades')}}"></a>Formulario</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            <div class="col-lg-12 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class="">Complete el formulario para ajustar el plan de pagos.</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-data" action="{{url('propiedades/create-installment-plan')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
                            <div class="form-group floating-label d-none">
                                <label>Propiedad ID</label>
                                <input type="text" class="form-control" name="id" value="{{ $item->id }}">
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Proyecto*</label>
                                    <input type="text" class="form-control" disabled name="id" value="{{$item->project->name}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Nombre de la propiedad*</label>
                                    <input type="text" class="form-control not-empty" disabled name="name" value="{{ $item->name }}" placeholder="" data-msg="Nombre de la propiedad">
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label" for="type">Propietario</label>
                                    <select id="user_id" name="user_id" class="form-control not-empty select2" data-msg="Propietario">
                                        <option value="">Seleccione una opción</option>
                                        @foreach($users as $owner)
                                            <option value="{{$owner->id}}">{{$owner->fullname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Precio*</label>
                                    <input type="text" class="form-control not-empty" disabled name="price" value="{{ $item->price }}" placeholder="" data-msg="Precio de la propiedad">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Anticipo*</label>
                                    <input type="text" class="form-control not-empty" name="pay_in_advance" value="{{ $item->pay_in_advance }}" placeholder="" data-msg="Anticipo">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Número de mensualidades*</label>
                                    <input type="number" class="form-control not-empty" name="months" value="" placeholder="" data-msg="Número de mensualidades">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha de inicio de pagos*</label>
                                    <input type="" class="form-control date-picker not-empty" name="fecha_inicio_pago" value="" placeholder="" data-msg="Fecha de inicio de pagos">
                                </div>
                                <div class="form-group col-md-12 text-center">
                                    <button type="button" class="btn btn-info m-l-5 calculate-installments"><i class="mdi mdi mdi-account-details"></i> Previsualizar plan de pagos</button>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <div class="table-responsive rows-container">
                                        <table class="table table-hover table-sm table-installments">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Fecha de pago</th>
                                                    <th class="text-center">Monto</th>
                                                    {{-- <th class="text-center">Acciones</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="text-center">
                                                    <td class="align-middle" colspan="5">Sin registros</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success save m-l-5" data-custom-function="createInstallmentPlan" data-form="#form-data"><i class="mdi mdi-content-save"></i> Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment-with-locales.min.js"></script>
{{-- <script src="{{asset("js/moment.js")}}"></script> --}}
<script>
    $('body').delegate('.calculate-installments','click', function() {
        let table = $('.table-installments');
        let months = Number( parseInt( $('[name="months"]').val() ) );
        let pay_in_advance = Number( parseFloat( $('[name="pay_in_advance"]').val() ).toFixed(2) );
        let price = Number( parseFloat( $('[name="price"]').val() ).toFixed(2) );
        let initDate = $('[name="fecha_inicio_pago"]').val();
        let initDateMoment = moment( initDate ).locale("es");
        // Falta código para decir que si el día es mayor al 28, seleccione otra fecha diferente
        if (!months || !initDate || !pay_in_advance ) {
            infoMsg('warning', 'Información incompleta.', 'Proporcione una fecha de inicio, el número de mensualidades y anticipo');
            return;
        }

        let monthly_payment = Number( ( price - pay_in_advance ) / months);

        table.children('tbody').children('tr').remove();

        for (let i = 0; i < months; i++) {
            let newDate = null;
            if (i == 0) {
                newDate = initDateMoment;
            } else {
                newDate = initDateMoment.add(1, 'month');
            }
            table.children('tbody').append(
                '<tr class="text-center installment">'+
                    '<td class="align-middle">'+( i + 1 )+'</td>'+
                    '<td class="align-middle" data-date="'+(newDate.format('YYYY-MM-DD'))+'">'+( newDate.format('LL') )+'</td>'+
                    // '<td class="align-middle">'+( newDate.format('DD de MMMM del año YYYY') )+'</td>'+
                    '<td class="align-middle" data-amount="'+(monthly_payment.toFixed(2))+'">$'+( monthly_payment.toFixed(2) )+' MXN</td>'+
                '</tr>'
            );
        }
    });

    // Send custom ajax request for create installment plan
    function createInstallmentPlan() {
        let id       = $('[name="id"]').val();
        let pay_in_advance = Number( parseFloat( $('[name="pay_in_advance"]').val() ).toFixed(2) );
        let price    = Number( parseFloat( $('[name="price"]').val() ).toFixed(2) );
        let months   = Number( parseInt( $('[name="months"]').val() ) );
        let initDate = $('[name="fecha_inicio_pago"]').val();
        let owner_id = $('[name="user_id"]').val();
        let route    = baseUrl.concat('/propiedades/create-installment-plan');
        
        let installmentsArray = [];

        $('table.table-installments tbody').children('tr.installment').each(function( index ) {
            // let item = $(this).data('row');
            let id = $(this).children().siblings("td:nth-child(1)").text();
            let date = $(this).children().siblings("td:nth-child(2)").data('date');
            let amount = Number( $(this).children().siblings("td:nth-child(3)").data('amount') );
            // let amount = Number( $(this).children().siblings("td:nth-child(3)").text() );
            let installmentObj = {
                id     : id,
                date   : date,
                amount : amount
            };
            installmentsArray.push(installmentObj);
            // console.log('installmentObj', installmentObj);
        });

        if (! installmentsArray.length ) {
            infoMsg('warning', 'Información incompleta.', 'Plan de mensualidades incompleto');
            return;
        }
        let config = {
            'id'       : id,
            'pay_in_advance' : pay_in_advance,
            'price'    : price,
            'months'   : months,
            'initDate' : initDate,
            'owner_id' : owner_id,
            'installmentsArray' : installmentsArray,
            'route'    : route,
            'redirect' : true,
        }
        loadingMessage();
        ajaxSimple(config);
    }
</script>
@endsection