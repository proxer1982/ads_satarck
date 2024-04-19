jQuery(function ($) {
    $('#agregar_regla').click(function () {
        $item = $('#tbl_reglas').data('items') + 1;

        $('#tbl_reglas').data('items', $item);

        $("<div class='row-items row_regla_" + $item + "'><div class='d-flex align-items-center'><span class='dashicons dashicons-ellipsis handle'></span></div><div><input name='regla-name[]' type='text' class='regular-text' required value=''></div><div><input name='regla[]' type='text' class='regular-text' required value=''></div><div><input name='regla-text[]' type='text' class='regular-text' required value='' /></div><div><input name='regla-meses[]' type='text' class='input-number' required value='' /></div><div class='del_campo text-center'><a class='text-danger button btn-default btn-eliminar-regla mx-auto' style='display:inline-flex; align-items:center;' data-item='.row_regla_" + $item + "'><span class='dashicons dashicons-no'></span></a></div></div>").appendTo($('#tbl_reglas'));

        /*$("<tr class='row_regla_" + $item + "'><td><span class='dashicons dashicons-ellipsis'></span></td><td><input name='regla-name[]' type='text' class='regular-text' required value=''></td><td><input name='regla[]' type='text' class='regular-text' required value=''></td><td><input name='regla-text[]' type='text' class='regular-text' required value=''></td><td><input name='regla-meses[]' type='number' class='regular-number' required value='' /></td><td class='del_campo' style='width:40px;'><a class='text-danger button btn-default btn-eliminar-regla' style='display:inline-flex; align-items:center;' data-item='.row_regla_" + $item + "'><span class='dashicons dashicons-no'></span></a></td></tr>").appendTo($('#tbl_reglas tbody'));*/
    });

    $('#tbl_reglas').on('click', '.btn-eliminar-regla', function () {

        let item = $(this).data('item');
        console.log("se elimino el item: " + item);
        $(item).remove();
    });

    $('#decima').change(function () {
        $('.value-range').text($(this).val());
    });

    $('.sortable').sortable({
        cancel: ".item-head",
        axis: "y",
        dropOnEmpty: false,
        handle: ".handle",
        cursor: "move",
        beforeStop: function (event, ui) {
            if (ui.item.index() == 0) {
                event.preventDefault();
            }
        }
    });

    $('.radio-url').click(function () {
        if ($(this).val() == 1) {
            $('#url_datos').attr('type', 'hidden');
        } else {
            $('#url_datos').attr('type', 'url');
        }
    });

    $('.select_pais').change(function () {
        $inputs = $('#row_' + $(this).val()).find('input:not(.select_pais)');
        if ($(this).is(':checked')) {
            $inputs.attr('disabled', false);
            $inputs.each(function (i) {
                if ($(this).attr('type') === 'checkbox') {
                    $(this).attr('checked', true);
                } else {
                    $(this).val($(this).data('valor'));
                    $(this).data('valor', '');
                    console.log($(this).data('valor'));
                }

            });
        } else {
            $inputs.attr('disabled', true);
            $inputs.each(function (i) {
                if ($(this).attr('type') === 'checkbox') {
                    $(this).attr('checked', false);
                } else {
                    $(this).data('valor', $(this).val());
                    $(this).val('');
                    console.log($(this).data('valor'));
                }
            });
        }
    });

    $inputs = $('.input-number');

    $.each($inputs, function (i) {
        $(this).keydown(function (e) {
            var code = (e.which) ? e.which : e.keyCode;
            if (e.ctrlKey == true && (code == 118 || code == 86)) {
                return;
            }

            if (e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 188 || e.keyCode == 190) { // backspace, tab, coma , y punto
                return true;
            } else if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) { // is a number.
                return true;
            } else { // other keys.
                e.preventDefault();
                return false;
            }
        })
    })

    $('.input-number').bind("paste", function (evt) {
        var pastedData = parseInt(evt.originalEvent.clipboardData.getData('text'));
        if (Number.isInteger(pastedData)) {
            return
        } else {
            evt.preventDefault();
        }
    });

    /*inputs.forEach(element => {
        element.addEventListener('keydown', evt => {
            var code = (evt.which) ? evt.which : evt.keyCode;
            console.log(code);
            
        })
    });*/
});