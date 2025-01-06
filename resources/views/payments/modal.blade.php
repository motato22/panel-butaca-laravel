<div class="modal fade data-fill" role="dialog" data-keyboard="false" aria-labelledby="label-title" id="payment-details-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="label-title">Detalles del pago</h2>
            </div>
            <form id="form-data" action="{{url('payments/change-status')}}" method="POST" class="" onsubmit="return false;" enctype="multipart/form-data" autocomplete="off" data-ajax-type="ajax-rate-users" data-column="0" data-refresh=table data-table_id="data-table">
                <div class="modal-body">
                    <div class="form-group col-md-12 d-none">
                        <input type="text" class="form-control" name="row_id">
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label" for="type">Propiedad</label>
                        <select id="property_id" name="property_id" class="form-control not-empty" data-msg="Propiedad">
                            <option value="">Seleccione una opción</option>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label" for="type">¿Aprobado?</label>
                        <select id="change_to" name="change_to" class="form-control not-empty" data-msg="Aprobar">
                            <option value="">Seleccione una opción</option>
                            <option value="1">Aprobar</option>
                            <option value="0">Rechazar</option>
                        </select>
                    </div>
                    <div class="row text-left details-content">
                        <div class="col-md-12">
                            <ul class="list-group">
                                <li class="list-group-item active">Datos generales</li>
                                <li class="list-group-item fill-container"><span class="label_show">ID de pago: <span class="payment_id"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Fecha de pago: <span class="payment_date_formated"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Tipo de pago: <span class="payment_type"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Status de pago: <span class="payment_status_data"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Monto: <span class="payment_amount_format"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Clabe: <span class="payment_clabe"></span></span></li>
                                <li class="list-group-item fill-container"><span class="label_show">Fecha de registro: <span class="payment_created_formated"></span></span></li>
                                <li class="list-group-item text-center payment-foto">
                                    <a href="" target="_blank"> <img width="300px;" src="" id="payment-photo"> </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success process-payment save" data-custom-function="processPayment">Procesar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->