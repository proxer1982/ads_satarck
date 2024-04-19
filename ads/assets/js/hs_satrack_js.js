jQuery(function ($) {
    $campo = $('.row_recurso_1');
    $('#agregar_recurso').click(function () {
        $item = $('#tbl_recursos').data('items') + 1;
        $campo_nuevo = $campo.clone();
        $campo_nuevo.removeClass('row_recurso_1');
        $campo_nuevo.addClass('row_recurso_' + $item);
        $('#tbl_recursos').data('items', $item);
        $campo_nuevo.find('.del_campo').html('<a class="text-danger button btn-default btn-eliminar-recurso" data-item=".row_recurso_' + $item + '"><i class="fas fa-times"></i></a>');
        //$campo_nuevo.find('input').val('');
        //$campo_nuevo.find('select').val('');
        $campo_nuevo.appendTo('#tbl_recursos');
    });

    $('#tbl_recursos').on('click', '.btn-eliminar-recurso', function () {
        let item = $(this).data('item');
        $($(this).data('item')).remove();
    });


    $campo2 = $('.row_auto_1');
    $('#agregar_auto').click(function () {
        $item = $('#tbl_auto').data('items') + 1;
        $campo_nuevo = $campo2.clone();
        $campo_nuevo.removeClass('row_auto_1');
        $campo_nuevo.addClass('row_auto_' + $item);
        $('#tbl_auto').data('items', $item);
        $campo_nuevo.find('.del_campo').html('<a class="text-danger button btn-default btn-eliminar-auto" data-item=".row_auto_' + $item + '"><i class="fas fa-times"></i></a>');
        $campo_nuevo.find('input').val('');
        $campo_nuevo.find('select').val('');
        $campo_nuevo.appendTo('#tbl_auto');
    });

    $('#tbl_auto').on('click', '.btn-eliminar-auto', function () {
        let item = $(this).data('item');
        $($(this).data('item')).remove();
    });



    $campo3 = $('.row_datos_1');
    $('#agregar_datos').click(function () {
        $item = $('#tbl_datos').data('items') + 1;
        $campo_nuevo = $campo3.clone();
        $campo_nuevo.removeClass('row_datos_1');
        $campo_nuevo.addClass('row_datos_' + $item);
        $('#tbl_datos').data('items', $item);
        $campo_nuevo.find('.del_campo').html('<a class="text-danger button btn-default btn-eliminar-datos" data-item=".row_datos_' + $item + '"><i class="fas fa-times"></i></a>');
        $campo_nuevo.find('input').val('');
        $campo_nuevo.find('select').val('');
        $campo_nuevo.appendTo('#tbl_datos');
    });

    $('#tbl_datos').on('click', '.btn-eliminar-datos', function () {
        let item = $(this).data('item');
        $($(this).data('item')).remove();
    });


    $('.sortable').sortable();
});