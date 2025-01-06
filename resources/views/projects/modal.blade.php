<div class="modal fade data-fill" role="dialog" data-keyboard="false" aria-labelledby="label-title" id="fechas-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="label-title">Detalles del evento</h2>
            </div>
            <form id="form-data-date-users" action="{{url('eventos/fechas')}}" method="POST" class="" onsubmit="return false;" enctype="multipart/form-data" autocomplete="off" data-ajax-type="ajax-rate-users" data-column="0" {{(Route::currentRouteName() == 'pedidos.supermarket.info' ? 'data-callback=getPrintable' : 'data-refresh=table' )}} data-table_id="data-table" data-container_class="printable-area">
                <div class="modal-body">
                    <div class="form-group d-none">
                        <input type="text" class="form-control" name="row_id">
                    </div>
                    <div class="row text-left details-content">
                        <div class="col-md-12">
                            {{-- <ul class="list-group d-none">
                                <li class="list-group-item active">Datos generales</li>
                                <li class="list-group-item fill-container"><span class="label_show">ID de evento: <span class="evento_id"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">¿Mostrar en app?: <span class="evento_visible_en_apps"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Título: <span class="evento_nombre"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Descripción: <span class="evento_descripcion"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Fecha de creación: <span class="evento_fecha_formateada"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">País: <span class="evento_pais"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Dirección: <span class="evento_direccion_evento"></span></span></li>
                            </ul>

                            <ul class="list-group ul-mapa">
                                <li class="list-group-item active">Mapa</li>
                                <li class="list-group-item map-location"><span class="label_show">Ubicación: </span>
                                    <div class="map-container" style="height: 300px; width: 100%;"></div>
                                </li>
                            </ul> --}}

                            <ul class="list-group ul-fechas">
                                <li class="list-group-item active">Fechas</li>
                                
                                <li class="list-group-item">
                                    <div class="row form-fecha">
                                        <div class="form-group col-md-12 d-none">
                                            <label>ID</label>
                                            <input type="text" class="form-control" name="evento_id" value="">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Fecha*</label>
                                            <input type="text" class="form-control date-picker" name="fecha" value="" placeholder="" data-msg="Fecha">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Hora*</label>
                                            <input type="text" class="form-control timepicker" name="hora" value="" placeholder="" data-msg="Hora">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Cupón</label>
                                            <input type="text" class="form-control" name="cupon" value="" placeholder="10MAYDESC" data-msg="Cupón">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="cstm-switch">
                                                <input type="checkbox" checked name="gratis" value="1" class="cstm-switch-input">
                                                <span class="cstm-switch-indicator bg-info "></span>
                                                <span class="cstm-switch-description">¿Gratis?</span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-4 d-none">
                                            <label>Precio bajo*</label>
                                            <input type="text" class="form-control" name="precio_bajo" value="" placeholder="" data-msg="Precio bajo">
                                        </div>
                                        <div class="form-group col-md-4 d-none">
                                            <label>Precio alto*</label>
                                            <input type="text" class="form-control" name="precio_alto" value="" placeholder="" data-msg="Precio alto">
                                        </div>
                                        <div class="form-group col-md-4 d-none">
                                            <label class="control-label" for="moneda_id">Moneda</label>
                                            <select id="moneda_id" name="moneda_id" class="form-control" data-msg="Moneda">
                                                <option value="0">Seleccione una opción</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <button type="button" class="btn btn-success agregar-fecha-tabla">Añadir fecha</button>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item">
                                    <table class="table tabla-fechas table-hover table-sm">
                                        <thead>
                                            <th class="d-nones">ID</th>
                                            <th>Precio</th>
                                            <th>Cupón</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Acciones</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-success save send-rate" data-dismiss="modal">Guardar fechas</button> --}}
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->