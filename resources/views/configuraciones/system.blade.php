@extends('layouts.main')

@section('content')
<section class="admin-content">
    <div class=" bg-dark m-b-30 bg-stars">
        <div class="container">
            <div class="row">
                <div class="col-md-7 m-auto text-white p-t-40 p-b-90">
                    <h1>Configuración</h1>
                    <p class="opacity-75">
                        Edite el rango de entrega o actualice la información de los productos de la base de datos.
                    </p>
                </div>
                <div class="col-md-5 m-auto text-white p-t-40 p-b-90 general-info" data-url="{{url("configuracion")}}" data-refresh="table" data-el-loader="card">
                    <div class="rounded text-white bg-white-translucent">
                        <div class="p-all-15">
                            <div class="row">
                                <div class="col-md-12 my-2 m-md-0">
                                    <div class="text-overline opacity-75">Última actualización</div>
                                    <h3 class="m-0 text-success last-update-db">{{$item->fecha_formateada}}</h3>
                                </div>
                                {{-- <div class="col-md-6 my-2 m-md-0">

                                    <div class="text-overline opacity-75">Total de pérdidas</div>
                                    <h3 class="m-0 text-danger">$1,520</h3>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pull-up">
        <div class="row">
            {{-- Table --}}
            <div class="col-lg-7 m-b-30">
                <div class="card">
                    <div class="card-header">
                        <h2 class=""></h2>
                    </div>
                    <div class="card-body">
                        <div class="card-title m-t-10" style="font-size: 16px;">A continuación de muestran algunas de las configuaraciones/opciones del sistema</div>
                        <form id="form-data-contact" action="{{url('configuracion/save/contact-phone')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="" data-table_id="example3" data-container_id="table-container">
                            <input type="text" class="form-control d-none" name="id" value="{{$contact ? $contact->id : ''}}">
                            <label>Número de contacto</label>
                            <input type="text" name="contact" class="form-control not-empty numeric" placeholder="Ej. 66456987123" value="{{$contact ? $contact->descripcion : ''}}" data-msg="Número de contacto">
                            <div class="form-group m-t-15">
                                <button type="submit" class="btn btn-primary save">Guardar</button>
                            </div>
                        </form>

                        <hr>

                        <form id="form-data-global-discount" action="{{url('configuracion/save/global-discount')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="" data-table_id="example3" data-container_id="table-container">
                            <input type="text" class="form-control d-none" name="id" value="{{$descuento ? $descuento->id : ''}}">
                            <label>Porcentaje de descuento global</label>
                            <input type="text" name="descuento" class="form-control" placeholder="Ej. 10" value="{{$descuento ? $descuento->descripcion : ''}}" data-msg="Porcentaje de descuento global">
                            <div class="form-group m-t-15">
                                <button type="submit" class="btn btn-primary save">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 m-b-30">
                <div class="card shadow-lg">
                    <div class="">
                        <div class="p-t-30 p-b-10 text-center">
                            <h5 class="text-center p-t-10 ">Actualizar productos en la base de datos</h5>
                        </div>
                        <div class="bg-dark rounded-bottom card-body text-white">
                            <p class="opacity-75">
                                - Esta operación puede llevar varios minutos dependiendo de la cantidad de registros a revisar por base de datos. <br>
                                - Le pedimos no abandone esta ventana hasta que el sistema le notifique que los productos han sido actualizados. <br>
                                - Si se elimaron productos en sicar, también se eliminarán de aquí. <br>
                            </p>
                            <button class="btn btn-white-translucent btn-block btn-lg update-products"><i class="mdi mdi-download"></i>Actualizar productos</button>
                            <button class="btn btn-white-translucent btn-block btn-lg update-photos"><i class="mdi mdi-image"></i>Descargar foto principal</button>
                            <button class="btn btn-white-translucent btn-block btn-lg update-gallery"><i class="mdi mdi-image-multiple"></i>Descargar fotos de galería</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $('body').delegate('.update-products','click', function() {
        var route = baseUrl.concat('/api/v1/actualizar-productos');
        var refresh = $('div.general-info').data('refresh');
        var main_id = $('div.general-info').data('main-id');
        swal({
            title: 'La base de datos está a punto de actualizarse, ¿desea continuar?',
            icon: 'warning',
            content: {
                element: "div",
                attributes: {
                    innerHTML:"<p class='text-response'>¡Los productos no podrán recuperarse una vez eliminados!</p>"
                },
            },
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if ( accept ) {
                config = {
                    'route'    : route,
                    'callback' : 'refreshUpdateTime',
                }
                loadingMessage();
                ajaxSimple(config);
            }
        }).catch(swal.noop);
    });

    $('body').delegate('.update-photos','click', function() {
        var route = baseUrl.concat('/api/v1/actualizar-fotos');
        var refresh = $('div.general-info').data('refresh');
        var main_id = $('div.general-info').data('main-id');
        swal({
            title: 'Las imágenes principales de los productos están a punto de actualizarse, ¿desea continuar?',
            icon: 'warning',
            content: {
                element: "div",
                attributes: {
                    innerHTML:"<p class='text-response'>¡Esto puede demorar varios minutos dependiendo de la cantidad de fotos a descargar!</p>"
                },
            },
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if ( accept ) {

                config = {
                    'page'      : 0,
                    'limit'     : 500,
                    'route'     : route,
                    'callback'  : 'saveMainImages',
                    'keep_swal' : true,
                }

                response = {
                    'finished' : false
                }

                saveMainImages(response, config);

            }
        }).catch(swal.noop);
    });

    $('body').delegate('.update-gallery','click', function() {
        var route = baseUrl.concat('/api/v1/actualizar-galeria');
        var refresh = $('div.general-info').data('refresh');
        var main_id = $('div.general-info').data('main-id');
        swal({
            title: 'La galería de los productos están a punto de actualizarse, ¿desea continuar?',
            icon: 'warning',
            content: {
                element: "div",
                attributes: {
                    innerHTML:"<p class='text-response'>¡Esto puede demorar varios minutos dependiendo de la cantidad de fotos a descargar!</p>"
                },
            },
            buttons:["Cancelar", "Aceptar"],
            dangerMode: true,
        }).then((accept) => {
            if ( accept ) {

                config = {
                    'page'      : 0,
                    'limit'     : 500,
                    'truncate'  : true,
                    'route'     : route,
                    'callback'  : 'saveMainImages',
                    'keep_swal' : true,
                }

                response = {
                    'finished' : false
                }

                saveMainImages(response, config);

                /*config = {
                    'route'    : route,
                    'callback' : 'refreshUpdateTime',
                }
                loadingMessage();
                ajaxSimple(config);*/
            }
        }).catch(swal.noop);
    });

    function refreshUpdateTime(response, config) {
        swal({
            title: 'Bien: ',
            icon: "success",
            content: {
                element: "div",
                attributes: {
                    innerHTML:"<p class='text-response'>"+response.msg ? response.msg : "¡Cambios guardados exitosamente!"+"</p>"
                },
            },
            /*buttons: false,*/
            closeOnEsc: true,
            closeOnClickOutside: true,
        }).catch(swal.noop);
        $('.last-update-db').text(response.data.actualizacion_formateada);
    }

    function saveMainImages(response, config) {
        // recursiva básicamente 
        if ( response && response.finished == false ) {
            config["page"] = config["page"] + 1;

            // Este parámetro es para sólo truncar la tabla de imágenes la primera vez
            if ( config["page"] > 1 ) {
                config["truncate"] = false;
            }

            loadingMessage('Cargando '+(config["page"] * config["limit"])+' fotos, espere...');
            
            ajaxSimple(config);
            // saveMainImages(response, config);
        } else {
            console.log('se actualizó el updatime');
            refreshUpdateTime(response, config);
        }
    }
</script>
@endsection
