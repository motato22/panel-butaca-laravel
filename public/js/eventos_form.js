function configurarTrFecha(fecha) {
    fecha = fecha;
    var hora = fecha.hora+' hrs.';
    var isoMoneda = fecha.moneda_iso ?  fecha.moneda_iso : null
    var precio = formatearPrecioFecha(fecha.gratis, fecha.precio_bajo, fecha.precio_alto, isoMoneda);

    var fechaTr = {
        'id'            : fecha.id,
        'moneda_id'     : fecha.moneda_id,
        'gratis'        : fecha.gratis,
        'precio_bajo'   : ( fecha.gratis == 1 ? '' : fecha.precio_bajo ),
        'precio_alto'   : ( fecha.gratis == 1 ? '' : fecha.precio_alto ),
        'precio'        : precio,
        'cupon'         : fecha.cupon ? fecha.cupon : '',
        // 'cupon'         : ( fecha.cupon ? fecha.cupon : 'N/A' ),
        'fecha'         : fecha.fecha,
        'hora_formated' : hora,
        'hora'          : fecha.hora,
    };

    $("table.tabla-fechas tbody").append(
        agregarFechaTabla(fechaTr)
    );
}

function agregarFechaTabla(trData) {
    html = 
    '<tr class="fecha-tr-'+trData.id+'">'+
        '<td scope="row" class="d-none">'+ trData.id +'</td>'+
        '<td scope="row" class="d-none">'+ trData.moneda_id +'</td>'+
        '<td scope="row" class="d-none">'+ trData.gratis +'</td>'+
        '<td scope="row" class="d-none">'+ trData.precio_bajo +'</td>'+
        '<td scope="row" class="d-none">'+ trData.precio_alto +'</td>'+
        '<td scope="row">'+ trData.precio +'</td>'+
        '<td scope="row">'+ trData.cupon +'</td>'+
        '<td scope="row">'+ trData.fecha +'</td>'+
        '<td scope="row">'+ trData.hora_formated +'</td>'+
        '<td scope="row" class="d-none">'+ trData.hora +'</td>'+
        '<td scope="row">'+
            '<button type="button" class="btn btn-danger btn-sm delete-date-row" data-toggle="tooltip" data-placement="top" title="Eliminar">'+
                '<i class="mdi mdi-trash-can"></i>'+
            '</button>'+
        '</td>'+
    '</tr>';

    return html;
}

// Formatea el precio de la fecha de un evento
function formatearPrecioFecha(gratis, precio_bajo = null, precio_alto = null, iso = null) {
    var precio = 'N/A';
    // Gratis
    if ( gratis ) {
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

//Helper to fill a select
function configurarSelectSubcategorias(response, config = null) {
    items = response.data;
    target = config.select;
    textFirst = config.first_item;
    disabled = config.first_disabled;
    select2 = config.select_2;

    if ( select2 ) {
        $(target).select2('destroy');
    }

    $( target ).children('option').remove();

    $( target ).append('<option value="" '+(disabled ? 'disabled' : '')+'>'+(textFirst ? textFirst : 'Seleccione una opción')+'</option>');
    
    items.forEach(function ( option ) {
        $( target ).append(
            '<option value="'+option.id+'">'+option.nombre+'</option>'
        );
    });

    if ( select2 ) {
        if ( config.dropdownParent ) {
            $( target ).select2({
                dropdownParent: $( config.dropdownParent )
            });
        } else {
            $( target ).select2();
        }
    }

    unBlockElement(config.parent);
    //$('.counter').text(items.length);
}

// function removeDateDom() {
//     $(config.dom).remove();
// }

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
    var text_sw = $(this).hasClass('special-row') ? $(this).data('row-id') : $(this).parent().siblings("td:nth-child(8)").text();
    // ids_array.push(row_id);
    // console.log($(this).parent(), $(this).parent().siblings("td:nth-child(7)"), text_sw);

    swal({
        title: 'Se dará de baja el registro con la fecha '+text_sw+', ¿Está seguro de continuar?',
        icon: 'warning',
        buttons:["Cancelar", "Aceptar"],
        dangerMode: true,
    }).then((accept) => {
        if (accept) {
            $(this).parent().parent().remove();
        }
    }).catch(swal.noop);
});