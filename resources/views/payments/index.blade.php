@extends('layouts.main')

@section('content')
@include('payments.modal')
<style type="text/css">
    /*.list-group-item:after {
        display: inline-block!important;
    }*/
    .datepicker {
      z-index: 1600 !important; /* has to be larger than 1050 */
    }
</style>
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-8 m-auto text-white p-t-40 p-b-90">
                    <h1>Pagos </h1>
                    <p class="opacity-75">
                        Aquí podrá visualizar y gestionar los pagos realizados por los clientes.
                    </p>
                </div>
                <div class="col-md-4 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("pagos")}}" data-refresh="table" data-el-loader="card">
                    
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
                        <h2 class="">Lista de pagos</h2>
                        <div class="card-controls">
                            <a href="javascript:;" class="btn btn-dark filter-rows"> <i class="mdi mdi-filter-variant"></i> Filtrar</a>
                            <a href="javascript:;" class="btn btn-info export-rows"> <i class="mdi mdi-file-excel"></i> Exportar</a>
                        </div>
                        <div class="row m-b-20">
                            <div class="col-md-3 my-auto">
                                <h4 class="m-0">Filtros</h4>
                            </div>
                            <div class="col-md-9 text-right my-auto filter-section">
                                <div class="btn-group row" role="group" aria-label="Basic example">
                                    <div class="no-pad col-md-4" style="text-align: left;">
                                        <select id="project_id" name="project_id" class="form-control select2" data-msg="Proyecto">
                                            <option value="" selected>Proyecto (Cualquiera)</option>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}">{{$project->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad col-md-4" style="text-align: left;">
                                        <select id="owner_id" name="owner_id" class="form-control select2" data-msg="Propietario">
                                            <option value="" selected>Propietario (Cualquiera)</option>
                                            @foreach($owners as $owner)
                                                <option value="{{$owner->id}}">{{$owner->fullname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad col-md-4" style="text-align: left;">
                                        <select id="payment_status_id" name="payment_status_id" class="form-control select2" data-msg="Status">
                                            <option value="" selected>Status (Cualquiera)</option>
                                            @foreach($status as $st)
                                                <option value="{{$st->id}}">{{$st->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="no-pad col-md-4">
                                        <input type="text" class="date-picker form-control" name="fecha_inicio" autocomplete="off" placeholder="Fecha inicio">
                                    </div>
                                    <div class="no-pad col-md-4">
                                        <input type="text" class="date-picker form-control" name="fecha_fin" autocomplete="off" placeholder="Fecha fin">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rows-container">
                            @include('payments.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- <script type="text/javascript" src="{{asset('js/eventos.js')}}"></script> --}}
<script type="text/javascript">
    // Visualizar fechas
    $('body').delegate('.change-payment-status', 'click', function() {
        let status = $(this).data('change-to');
        let id = $(this).parent().siblings("td:nth-child(1)").text();

        config = {
            'id'        : id,
            'status'    : status,
            'keepModal' : true,
            'route'     : baseUrl.concat('/change-status/'+id),
            'method'    : 'POST',
            'reload'    : true,
            // 'callback'  : 'displayDates',
        }

        loadingMessage('Espere un momento...');

        ajaxSimple(config);
    });
    
    // Ve los detalles del pago
    $('body').delegate('.view-details', 'click', function() {
        let id = $(this).parent().siblings("td:nth-child(1)").text();

        config = {
            'keepModal'   : true,
            'route'       : baseUrl.concat('/pagos/show/'+id),
            'method'      : 'GET',
            'callback'    : 'viewPaymentDetails',
        }

        loadingMessage('Espere un momento...');

        ajaxSimple(config);
    });

    // Send custom ajax request for process payment
    function processPayment() {
        let id          = $('#payment-details-modal [name="row_id"]').val();
        let property_id = $('#payment-details-modal [name="property_id"]').val();
        let change_to   = $('#payment-details-modal [name="change_to"]').val();
        let route       = baseUrl.concat('/pagos/change-status');
        
        if (! property_id || change_to == "" || ! id ) {
            infoMsg('warning', 'Información incompleta.', 'Complete todos los campos para continuar');
            return;
        }
        let config = {
            'id'          : id,
            'method'      : 'POST',
            'property_id' : property_id,
            'change_to'   : change_to,
            'route'       : route,
            'refresh'     : 'table',
        }
        loadingMessage();
        ajaxSimple(config);
    }

    function viewPaymentDetails(response, config) {
        payment    = response.data.payment;
        properties = response.data.properties;

        $('.payment-foto').addClass('d-none');
        $('.payment_status_data, .payment_date_formated, .payment_created_formated').text('');
        $('.payment_status_data, .payment_date_formated, .payment_created_formated').parent().parent().addClass('d-none');
        $('.payment_status_data, .payment_date_formated, .payment_created_formated').children().remove();
        $('[name="change_to"]').val("");
        $('[name="property_id"]').children("option").remove();
        $('[name="property_id"]').append("<option selected value=''>Seleccione una opción</option>");
        properties.forEach(property => {
            $('[name="property_id"]').append("<option value='"+property.id+"'>"+property.name+"</option>");
        });

        if ( payment.property_id ) {
            $('[name="property_id"]').prop('disabled', true);
            $('[name="property_id"]').val(payment.property_id);
        } else {
            $('[name="property_id"]').prop('disabled', false);
        }

        if ( payment.status.id == 3 || !payment.property_id ) {// Esperando aprobación o no cuenta con una propiedad asignada
            if ( payment.status.id == 3 ) {// Esperando aprobación
                $('[name="change_to"]').parent().removeClass("d-none");
                $('[name="change_to"]').addClass("not-empty");
            } else {
                $('[name="change_to"]').parent().addClass("d-none");
                $('[name="change_to"]').removeClass("not-empty");
                $('[name="change_to"]').val(1);
            }
            $('.process-payment').removeClass('d-none');
        } else {
            $('[name="change_to"]').parent().addClass("d-none");
            $('[name="change_to"]').removeClass("not-empty");
            $('[name="change_to"]').val(1);
            $('.process-payment').addClass('d-none');
        }

        // Llena la información del pedido
        if ( payment ) {
            let statusHTML  = null; 
            let typeHTML    = null;
            let amount      = Number(payment.amount).toFixed(2);
            let paymentCreated = moment(payment.created_at);
            let createdFormat  = paymentCreated.format('DD [de] MMMM [del] YYYY');
            let paymentDate    = moment(payment.payment_date);
            let dateFormat     = paymentDate.format('DD [de] MMMM [del] YYYY');
            // payment_date_formated
            // payment_type
            // payment_status
            // payment_clabe
            // 
            fill_text(payment, null, 'payment_', true);
            $('input[name="row_id"]').val(payment.id);
            // if ( payment.visible_en_app == 'S') {
            //     $('.evento_visible_en_apps').append('<span class="badge bg-success">Si</span>');
            //     $('.evento_visible_en_apps').parent().parent().removeClass('d-none');
            // } else {
            //     $('.evento_visible_en_apps').append('<span class="badge bg-danger">No</span>');
            //     $('.evento_visible_en_apps').parent().parent().removeClass('d-none');
            // }
            
            // Fecha del pago
            $('.payment_date_formated').text(dateFormat);
            $('.payment_date_formated').parent().parent().removeClass('d-none');
            // Fecha de registro de pago
            $('.payment_created_formated').text(createdFormat);
            $('.payment_created_formated').parent().parent().removeClass('d-none');
            // Monto
            $('.payment_amount_format').text('$'+amount+' MXN');
            $('.payment_amount_format').parent().parent().removeClass('d-none');

            if ( payment.photo ) {
                $('.payment-foto').removeClass('d-none');
                $('.payment-foto').children('a').attr('href', baseUrl.concat('/'+payment.photo));
                $('.payment-foto').children('a').children('img#payment-photo').attr('src', baseUrl.concat('/'+payment.photo));
            }
        }

        // Llena la información del cliente
        // if ( user ) {
        //     fill_text(user, null, 'user_', false);
        // }

     

        $('div#payment-details-modal').modal("show");
    }
</script>
@endsection