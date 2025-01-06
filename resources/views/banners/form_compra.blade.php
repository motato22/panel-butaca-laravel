@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class="bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <h1>Compra de banner publicitario</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-t-20 p-b-90">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-b-0 bg-transparent ol-breadcrum float-right">
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{url('banners/publicitarios')}}"></a>Formulario de compra</li>
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
                        <h2 class="">Complete el formulario</h2>
                    </div>
                    <div class="card-body">
                        <form id="form-evento-data" action="{{url('banners/publicitarios/'.($item ? 'update' : 'save'))}}" data-custom-function="comprarBanner" method="POST" autocomplete="off" onsubmit="return false;" enctype="multipart/form-data">
                            <div id="formWizard">

                                <ul class="nav nav-pills nav-justified">
                                    <li class="nav-item"><a class="nav-link active" href="#tabBanners" data-toggle="tab">Banner</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tabPayment" data-toggle="tab">Método de pago</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tabConfirm" data-toggle="tab">Confirmación</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane p-t-20 p-b-20 active" id="tabBanners">
                                        <div class="form-group floating-label d-none">
                                            <label>ID</label>
                                            <input type="text" class="form-control" name="id" value="{{$item ? $item->id : ''}}">
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="control-label" for="type">País*</label>
                                                <select id="pais_id" name="pais_id" class="form-control not-empty select2" data-msg="Info: País">
                                                    <option value="">Seleccione una opción</option>
                                                    @if ( $item )
                                                        @foreach($paises as $pais)
                                                            <option value="{{$pais->id}}" {{$item->pais_id == $pais->id ? 'selected' : ''}}>{{$pais->nombre}}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($paises as $pais)
                                                            <option value="{{$pais->id}}">{{$pais->nombre}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <hr>
                                                <h4>Banners disponibles</h4>
                                            </div>

                                            {{-- <div class="form-group col-md-12 m-b-10">
                                                <p class="font-secondary">
                                                    Banner de evento principal
                                                </p>
                                                @for( $x=1; $x <= 10; $x++ )
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="bannerEventoPrincipal{{$x}}" name="bannerEventoPrincipal" class="custom-control-input">
                                                        <label class="custom-control-label" for="bannerEventoPrincipal{{$x}}">
                                                            Posición {{$x}} (${{$x * 100}} MXN)
                                                        </label>
                                                    </div>
                                                @endfor
                                            </div> --}}

                                            <div class="form-group col-md-12 m-b-10">
                                                @foreach( $tiposBanner as $tipo )
                                                    <div class="option-box" style="width: 100%;">
                                                        <input id="banner_{{$tipo->id}}" data-row="{{$tipo}}" data-select="#meses_{{$tipo->id}}" class="banner-checkbox" type="checkbox">
                                                        <label for="banner_{{$tipo->id}}" style="width: 100%;">
                                                            <span class="radio-content">
                                                                <span class="h6 d-block">{{$tipo->sub}}
                                                                    <span class="badge-soft-primary badge">${{$tipo->costo}} MXN mensual</span>
                                                                </span>
                                                                <span class="text-muted d-block m-b-10">
                                                                    {{$tipo->descripcion}}
                                                                </span>
                                                                <div class="col-md-3 no-pad">
                                                                    <select id="meses_{{$tipo->id}}" data-parent="#banner_{{$tipo->id}}" data-row="{{$tipo}}" class="form-control banner-meses not-empty">
                                                                        <option value="1">1 Mes</option>
                                                                        <option value="3">3 Meses</option>
                                                                        <option value="6">6 Meses</option>
                                                                        <option value="9">9 Meses</option>
                                                                        <option value="12">12 Meses</option>
                                                                    </select>
                                                                </div>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane p-t-20 p-b-20" id="tabPayment">
                                        <div class="row">
                                            <div class="form-group col-md-12 text-center">
                                                <img src="https://www.internationalscienceediting.com/wp-content/uploads/2017/06/logo-stripe.png" style="max-width: 400px;">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label style="width: 100%;">Tarjeta
                                                    <div id="card-element" class="m-t-10">
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="form-group col-md-12 text-center msg-errors d-none" style="color: firebrick;">

                                            </div>

                                            {{-- <div class="form-group col-md-6">
                                                <label>Nombre de tarjetahabiente*</label>
                                                <input type="text" class="form-control not-empty" name="tarjetahabiente" id="tarjetahabiente" placeholder="" data-msg="Método de pago: Tarjetahabiente">
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="num_tarjeta">Número de tarjeta*</label>
                                                <input type="text" class="form-control not-empty" name="num_tarjeta" id="num_tarjeta" maxlength="16" placeholder="" data-msg="Método de pago: Número de tarjeta">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="mes">Mes (MM)*</label>
                                                <input type="text" class="form-control not-empty" name="mes" id="mes" placeholder="" maxlength="2" data-msg="Método de pago: Mes (MM)">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="anio">Año (YY)*</label>
                                                <input type="text" class="form-control not-empty" name="anio" id="anio" placeholder="" maxlength="2" data-msg="Método de pago: Año (YY)">
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="cvc">CVC*</label>
                                                <input type="text" class="form-control not-empty" name="cvc" id="cvc" placeholder="" maxlength="4" data-msg="Método de pago: CVC">
                                            </div> --}}
                                        </div>
                                    </div>

                                    <div class="tab-pane p-t-20 p-b-20" id="tabConfirm">
                                        <div class="col-md-12">
                                            <table class="table table-hover table-sm banners-table">
                                                <thead>
                                                    <tr>
                                                        <th>Banner</th>
                                                        <th class="text-right">Costo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2">Ningún banner seleccionado.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <ul class="nav nav-pills {{-- justify-content-between  --}} wizard m-b-30">
                                        <li><button class="btn btn-secondary previo m-l-5 disabled" href="#!"> <i class="mdi mdi-arrow-left-bold"></i> Previo</button></li>
                                        <li><button class="btn btn-secondary siguiente m-l-5" href="#!"><i class="mdi mdi-arrow-right-bold"></i> Siguiente</button></li>
                                        <li><button type="submit" class="btn btn-success m-l-5 pagar-banners save d-none"><i class="mdi mdi-content-save"></i> Pagar</button></li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('vendor/jquery.bootstrap.wizard/jquery.bootstrap.wizard.min.js')}}"></script>
<script type="text/javascript">
    bannerArray = [];
    stripe      = Stripe('{{env('STRIPE_PUBLIC_KEY')}}');
    elements    = stripe.elements();
    cardElement = elements.create('card');
    cardElement.mount('#card-element');

    (function ($) {
        'use strict';
        $(document).ready(function () {
            $('#formWizard').bootstrapWizard({
                'tabClass': 'nav nav-pills',
                'onNext': function (tab, navigation, index) {
                    
                },
                'onTabShow': function (tab, navigation, index) {
                    var size = $('#formWizard').bootstrapWizard('navigationLength');
                    var index = $('#formWizard').bootstrapWizard('currentIndex');

                    // Botón pagar
                    if ( index == 2 ) {
                        $('.pagar-banners').removeClass('d-none');
                    } else {
                        $('.pagar-banners').addClass('d-none');
                    }

                    // Botón previo
                    if ( index == 0 ) {
                        $('.previo').addClass('disabled');
                    } else {
                        $('.previo').removeClass('disabled');
                    }

                    // Se oculta el botón de siguiente
                    if ( $('#formWizard').bootstrapWizard('currentIndex') == size ) {
                        $('.siguiente').addClass('disabled');
                    } else {
                        $('.siguiente').removeClass('disabled');
                    }
                },
            });
        });
    })(window.jQuery);

    $('body').delegate('.previo', 'click', function() {
        $('#formWizard').bootstrapWizard('previous');
    });

    $('body').delegate('.siguiente', 'click', function() {
        $('#formWizard').bootstrapWizard('next');
    });

    // Arma el objeto de banners
    $('body').delegate('.banner-checkbox', 'change', function() {
        var tipoBanner = $(this).data('row');
        var meses      = $( $(this).data('select') ).val();
        
        if ( $(this).is(':checked') ) {
            var tipo = {
                'tipo_banner_id' : tipoBanner.id,
                'nombre'         : tipoBanner.sub,
                'costo'          : tipoBanner.costo,
                'meses'          : meses,
            }
            bannerArray.push(tipo);
            listaConfirmacion(bannerArray);
        } else {
            // Quita el banner del elemento
            bannerArray = bannerArray.filter(function(item) { return item.tipo_banner_id != tipoBanner.id; }); 
            listaConfirmacion(bannerArray);
        }
    });

    $('body').delegate('.banner-meses', 'change', function() {
        var tipoBanner = $(this).data('row');
        var index = bannerArray.findIndex((item => item.tipo_banner_id == tipoBanner.id));

        //Actualiza las propiedades de un objeto
        if ( index !== -1 ) {
            bannerArray[index].meses = $(this).val();
        }

        listaConfirmacion(bannerArray);
    });

    function comprarBanner(config = null) {
        if( bannerArray.length == 0 ) {
            swal('Error', 'Seleccione al menos un banner para continuar', 'error');
        } else {
            loadingMessage('Validando información...');
        
            stripe.createToken(cardElement).then(function(result) {
                swal.close();
                // Errores por mostrar
                if ( result.error ) {
                    swal('Error', result.error.message, 'error');
                    $('.msg-errors').children().remove();
                    $('.msg-errors').append('<span>'+result.error.message+'</span>');
                    $('.msg-errors').removeClass('d-none');
                } 
                // Procede al pago
                else {
                    $('.msg-errors').children().remove();
                    $('.msg-errors').addClass('d-none');
                    // Se arma el ajax aquí...
                    config = {
                        'route'    : baseUrl.concat('/banners/publicitarios/save'),
                        'pais_id'  : $('select[name="pais_id"]').val(),
                        'banners'  : bannerArray,
                        'token'    : result.token.id,
                        'redirect' : true,
                    }
                    
                    loadingMessage('Espere un momento...');
                    ajaxSimple(config);
                }
                console.log(result);
            });
        }
    }

    function listaConfirmacion(banners) {
        $('.banners-table tbody').children().remove();
        var total = 0;

        if ( banners.length ) {
            for ( var key in banners ) {
                if ( banners.hasOwnProperty( key ) ) {
                    var subtotal = banners[key].meses * banners[key].costo;
                    total += subtotal;
                    
                    $('.banners-table tbody').append(
                        '<tr>'+
                            '<td class="align-middle">'+banners[key].nombre+'</td>'+
                            '<td class="align-middle text-right">'+banners[key].meses+' meses ( $'+subtotal+' mxn )</td>'+
                        '</tr>'
                    );
                }
            }

            // Fila para el total
            $('.banners-table tbody').append(
                '<tr class="total-payment">'+
                    '<td class="align-middle text-right">Total a pagar.</td>'+
                    '<td class="align-middle text-right">$'+total+' MXN</td>'+
                '</tr>'
            );

        } else {
            $('.banners-table tbody').append(
                '<tr>'+
                    '<td colspan="2">Ningún banner seleccionado.</td>'+
                '</tr>'
            );
        }
    }
</script>
@endsection