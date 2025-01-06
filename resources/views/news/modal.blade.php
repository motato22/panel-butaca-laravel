<div class="modal fade data-fill" tabindex="-1" role="dialog" data-keyboard="false" aria-labelledby="label-title" id="view-documents">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="label-title">Información de identidad</h2>
            </div>
            <div class="modal-body">
                <div class="row text-left details-content">
                    <div class="col-md-12">
                        <ul class="list-group">
                            <li class="list-group-item active">Datos del usuario</li>
                            <li class="list-group-item text-center user-foto">
                                <img width="100px;" src="" id="user-photo">
                            </li>
                            <li class="list-group-item fill-container"><span class="label_show">Nombre completo: <span class="user_fullname"></span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Correo: <span class="user_email"></span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Teléfono: <span class="user_telefono"></span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Sexo: <span class="user_sexo"></span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Fecha de nacimiento: <span class="user_fecha_nacimiento"></span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">País: <span class="user_pais"></span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Tipo de documento: <span class="documentacion_identificacion"></span></span></li>
                            <li class="list-group-item text-center documentacion-foto">
                                <span>Documento</span><br>
                                <a href="" target="_blank" class="doc_foto_documento"> <img width="200px;" src=""> </a>
                            </li>
                            <li class="list-group-item text-center documentacion-foto">
                                <span>Selfie</span><br>
                                <a href="" target="_blank" class="doc_foto_personal"> <img width="200px;" src=""> </a>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-info verify-user-modal" data-row-id="" data-toggle="tooltip" data-placement="top" title="Verificar usuario"><i class="mdi mdi-check"></i> Verificar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade data-fill" tabindex="-1" role="dialog" data-keyboard="false" aria-labelledby="label-title" id="view-apikey-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="label-title">Información de usuario para apirest</h2>
            </div>
            <div class="modal-body">
                <div class="row text-left details-content">
                    <div class="col-md-12">
                        <ul class="list-group">
                            <li class="list-group-item active">Datos para la apirest</li>
                            <li class="list-group-item fill-container"><span class="label_show">Correo: <span class="user_email"></span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Token: <span class="token"></span></span></li>
                            {{-- <li class="list-group-item" style="font-weight: normal;"><span class="label_show">Contraseña: <span class="pass"></span></span>Misma que el usuario promotor</li> --}}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->