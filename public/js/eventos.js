function agregarFechaTabla(trData) {
    html = 
    '<tr class="fecha-tr-'+trData.id+'">'+
        '<td scope="row">'+ trData.id +'</td>'+
        '<td scope="row">'+ trData.precio +'</td>'+
        '<td scope="row">'+ trData.cupon +'</td>'+
        '<td scope="row">'+ trData.fecha +'</td>'+
        '<td scope="row">'+ trData.hora +'</td>'+
        '<td scope="row">'+
            '<button class="btn btn-danger btn-sm special-row delete-date-row" data-row-id="'+trData.id+'" data-toggle="tooltip" data-placement="top" title="Eliminar">'+
                '<i class="mdi mdi-trash-can"></i>'+
            '</button>'+
        '</td>'+
    '</tr>';

    return html;
}

function configurarTrFecha(response, config) {
    fecha = response.data.fecha;
    var hora = fecha.hora.substring(0, 5)+' hrs.';
    var isoMoneda = fecha.moneda ?  fecha.moneda.iso : null
    var precio = formatearPrecioFecha(fecha.gratis, fecha.precio_bajo, fecha.precio_alto, isoMoneda);

    var fechaTr = {
        'id'     : fecha.id,
        'precio' : precio,
        'cupon'  : ( fecha.cupon ? fecha.cupon : 'N/A'),
        'fecha'  : fecha.fecha,
        'hora'   : hora,
    };

    $("table.tabla-fechas tbody").append(
        agregarFechaTabla(fechaTr)
    );
}


// Formatea el precio de la fecha de un evento
function formatearPrecioFecha(gratis, precio_bajo = null, precio_alto = null, iso = null) {
    var precio = 'N/A';
    // Gratis
    if ( gratis == 1 ) {
        precio = 'Gratis';
    } 
    // Rango de precios
    else {
        if( precio_bajo ) {
            precio = '$'+precio_bajo + ( iso ?  iso : '');
        }

        if( precio_alto ) {
            precio = precio +' - '+ '$'+precio_alto + ( iso ?  iso : '');
        }
    }

    return precio;
}

function removeDom(response, config) {
    $(config.dom).remove();
}

function redirectPage(response, config) {
    $(config.dom).remove();
}

$('input[name="gratis"]').change(function() {
    // Gratis
    if( $(this).is(":checked") ) {
        $('input[name="precio_bajo"], input[name="precio_alto"], select[name="moneda_id"]').parent().addClass('d-none');
    } else {
        $('input[name="precio_bajo"], input[name="precio_alto"], select[name="moneda_id"]').parent().removeClass('d-none');
    }
});

//Send a request for a single delete
$('body').delegate('.delete-date-row','click', function() {
    var route = baseUrl.concat('/fechas/delete');
    var callback = 'removeDom';
    var ids_array = [];
    var evento_id = $('input[name="evento_id"]').val();
    var row_id = $(this).hasClass('special-row') ? $(this).data('row-id') : $(this).parent().siblings("td:nth-child(1)").text();
    ids_array.push(row_id);

    swal({
        title: 'Se dará de baja el registro con el ID '+row_id+', ¿Está seguro de continuar?',
        icon: 'warning',
        buttons:["Cancelar", "Aceptar"],
        dangerMode: true,
    }).then((accept) => {
        if (accept){
            config = {
                'route'     : route,
                'keepModal' : true,
                'evento_id' : evento_id,
                'ids'       : ids_array,
                'dom'       : ".fecha-tr-"+row_id,
                'callback'  : callback,
            }
            loadingMessage();
            ajaxSimple(config);
        }
    }).catch(swal.noop);
});