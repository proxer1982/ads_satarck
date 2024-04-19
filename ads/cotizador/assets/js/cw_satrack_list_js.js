/*jQuery(document).ready(function($){
  

});*/

jQuery(function ($) {
    $nonce = datos_cw.NONCE;

    let minDate, maxDate;

    // Custom filtering function which will search data in column four between two values
    DataTable.ext.search.push(function (settings, data, dataIndex) {
        let min = minDate.val();
        let max = maxDate.val();
        let date = new Date(data[1]);

        if (
            (min === null && max === null) ||
            (min === null && date <= max) ||
            (min <= date && max === null) ||
            (min <= date && date <= max)
        ) {
            return true;
        }
        return false;
    });
    $meses = ['Enero', 'Febrero ', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    // Create date inputs
    minDate = new DateTime('#min', {
        format: 'YYYY/MM/DD',
        i18n: {
            months: $meses
        }
    });
    maxDate = new DateTime('#max', {
        format: 'YYYY/MM/DD',
        i18n: {
            months: $meses
        }
    });

    $tabla = $('#tabla_cotizaciones').DataTable({
        columnDefs: tablaDraw,
        language: {
            processing: 'Proceso en curso...',
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ elementos',
            info: 'Procesos del _START_ al _END_ de _TOTAL_ procesos',
            infoEmpty: 'No hay procesos',
            infoFiltered: '(filtro de _MAX_ procesos en total)',
            infoPostFix: '',
            loadingRecords: 'Cargando...',
            zeroRecords: 'No hay procesos para mostrar',
            emptyTable: 'No hay datos disponibles en la tabla',
            paginate: {
                first: 'Primero',
                previous: 'Anterior',
                next: 'Siguiente',
                last: 'Último'
            },
            aria: {
                sortAscending: ': activar para ordenar la columna en orden ascendente',
                sortDescending: ': habilitar para ordenar la columna en orden descendente'
            }
        },
        displayLength: 10,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All'],
        ]
    });

    new $.fn.dataTable.Buttons($tabla, {
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Descargar datos',
                className: 'btn-excel',
                messageTop: "",
                footer: false,
                title: null,
                exportOptions: {
                    orthogonal: 'export'
                }
            }
        ]
    });

    $tabla.buttons().container().appendTo($('#botones-tabla'));


    document.querySelectorAll('#min, #max').forEach((el) => {
        el.addEventListener('change', () => $tabla.draw());
    });

    $('.del-coti').on('click', function (event) {
        $boton = $(this);

        $id = $boton.data('id');
        $row = $boton.data('row');

        if (confirm('¿Seguro de eliminar esta cotización No. ' + $id + '?')) {
            $.ajax({
                method: 'POST',
                url: dcms_vars.ajaxurl,
                dataType: 'json',
                data: { action: 'save_ajax_cotizacion', nonce: $nonce, id: $id, status: 0 }
            })
                .done(function (data) {
                    console.log(data);
                    if (data.status == 200) {
                        $tabla.row($row).remove().draw(false);
                    }
                });
        }
    });

    $('.apro-coti').on('click', function (event) {
        $boton = $(this);

        $id = $boton.data('id');
        $row = $boton.data('row');
        $.ajax({
            method: 'POST',
            url: dcms_vars.ajaxurl,
            dataType: 'json',
            data: { action: 'save_ajax_cotizacion', nonce: $nonce, id: $id, status: 2 }
        })
            .done(function (data) {
                console.log(data);
                if (data.status == 200) {
                    $tabla.row($row).remove().draw(false);
                }
            });
    });

    $('.rest-coti').on('click', function (event) {
        $boton = $(this);

        $id = $boton.data('id');
        $row = $boton.data('row');
        $.ajax({
            method: 'POST',
            url: dcms_vars.ajaxurl,
            dataType: 'json',
            data: { action: 'save_ajax_cotizacion', nonce: $nonce, id: $id, status: 1 }
        })
            .done(function (data) {
                console.log(data);
                if (data.status == 200) {
                    $tabla.row($row).remove().draw(false);
                }
            });
    });

    window.onresize = function () {
        console.log("redibujar");
        $tabla.draw(false);
    }


});