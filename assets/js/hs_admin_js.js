jQuery(function($){
    $('#agregar_recurso').click(function(){
        $('.row_recurso').clone().appendTo('#tbl_recursos');
    });
});