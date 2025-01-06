<div class="modal fade data-fill" tabindex="-1" role="dialog" aria-labelledby="label-title" id="view-charges-payments">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="label-title">Estado de cuenta</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs tab-line" id="accountStatusTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="charges-tab" data-toggle="tab" href="#line-charges" role="tab" aria-controls="charges" aria-selected="true">Cargos generados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="payments-tab" data-toggle="tab" href="#line-payments" role="tab" aria-controls="payments" aria-selected="false">Pagos realizados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sendmail-tab" data-toggle="tab" href="#line-sendmail" role="tab" aria-controls="sendmail" aria-selected="false">Enviar por correo</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent2">
                    <div class="tab-pane fade show active" id="line-charges" role="tabpanel" aria-labelledby="charges-tab">
                        <div style="margin-top: 10px;">
                            <div class="row text-left charges-content">
                                <div class="col-md-12">
                                    <ul class="list-group">
                                        {{-- <li class="list-group-item active">Historial de cargos</li> --}}
                                        <li class="list-group-item">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-sm charges">
                                                    <thead>
                                                        <th class="align-middle">No.</th>
                                                        <th class="align-middle">Monto a pagar</th>
                                                        <th class="align-middle">Status</th>
                                                        <th class="align-middle">Fecha límite para pagar</th>
                                                        {{-- <th class="align-middle">Acciones</th> --}}
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="line-payments" role="tabpanel" aria-labelledby="payments-tab">
                        <div style="margin-top: 10px;">
                            <div class="row text-left payments-content">
                                <div class="col-md-12">
                                    <ul class="list-group">
                                        {{-- <li class="list-group-item active">Historial de cargos</li> --}}
                                        <li class="list-group-item">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-sm payments">
                                                    <thead>
                                                        <th class="align-middle">No.</th>
                                                        <th class="align-middle">Monto</th>
                                                        <th class="align-middle">Status</th>
                                                        <th class="align-middle">Fecha y hora del cargo</th>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="line-sendmail" role="tabpanel" aria-labelledby="sendmail-tab">
                        <div style="margin-top: 10px;">
                            <div class="row text-left charges-content">
                                <div class="col-md-12">
                                    <ul class="list-group">
                                        {{-- <li class="list-group-item active">Historial de cargos</li> --}}
                                        <li class="list-group-item">
                                            <form id="form-data-sendmail" class="valid ajax-plus" action="{{url('propiedades/state-account')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form-modal" data-keep_modal="true" data-column="0" data-refresh="" data-redirect="0" data-table_id="" data-container_id="">
                                                <div class="row">
                                                    <div class="form-group col-sm-12 col-xs-12 d-none">
                                                        <label class="required" for="id">ID</label>
                                                        <input type="text" class="not-empty form-control" name="id" data-msg="ID contrato">
                                                    </div>
                                                    <div class="form-group col-sm-12 col-xs-12">
                                                        <div class="alert alert-info">
                                                            Separe cada correo por coma, si no proporciona un correo electrónico, 
                                                            se enviará el estado de cuenta al o los correos registrados en el contrato del cliente.
                                                        </div>
                                                        <label class="required" for="list_price">Correo(s)</label>
                                                        <input data-role="tagsinput" type="text" class="form-control" value="" name="sendmail" data-msg="Correo(s)">
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-success save" data-target="form-data-sendmail">Enviar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-primary export-account-status">Exportar</button> --}}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade data-fill" tabindex="-1" role="dialog" aria-labelledby="label-title" id="change-pay-day">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="label-title">Cambiar día de pago</h4>
            </div>
            <form id="form-data" action="{{url('payments/change-pay-day')}}" method="POST" class="" onsubmit="return false;" enctype="multipart/form-data" autocomplete="off" data-ajax-type="ajax-rate-users" data-column="0" data-refresh=table data-table_id="data-table">
                <div class="modal-body">
                    <div class="form-group col-md-12 d-none">
                        <input type="text" class="form-control" name="row_id">
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label" for="type">Propiedad</label>
                        <input type="text" class="form-control" disabled name="property_name" data-msg="Propiedad">
                    </div>
                    {{-- <div class="form-group col-md-12">
                        <label class="control-label" for="type">Nuevo día de pago (Del 1 al 28)</label>
                        <input type="text" class="form-control" name="new_date" data-msg="Nuevo día de pago (Del 1 al 28)">
                    </div> --}}
                    <div class="form-group col-md-12">
                        <label class="control-label" for="type">Nueva fecha de pago a partir de:</label>
                        <input type="text" class="form-control new-date" name="new_date" data-msg="Nuevo día de pago (Del 1 al 28)">
                    </div>
                    <div class="form-group col-md-12">
                        <div class="alert alert-border-info  alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div class="icon">
                                    <i class="icon mdi mdi-alert-circle-outline"></i>
                                </div>
                                <div class="content">
                                    <strong>Nota:</strong> <br>
                                    - Sólo puede seleccionar fechas mayor al del próximo pago actual. <br>
                                    {{-- - Seleccionar un día entre el 1 y 28 de cada mes. --}}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        <div class="text-left payments-content">
                            <div class="col-md-12">
                                <ul class="list-group">
                                    <li class="list-group-item active">Cargos a modificar</li>
                                    <li class="list-group-item">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm new-date-installments">
                                                <thead>
                                                    <th class="align-middle">No.</th>
                                                    <th class="align-middle">Monto a pagar</th>
                                                    <th class="align-middle">Status</th>
                                                    <th class="align-middle">Fecha límite para pagar</th>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success change-pay-day-btn save" data-custom-function="changePayDay">Procesar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
