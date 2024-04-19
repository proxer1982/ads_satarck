jQuery(document).ready(function ($) {
    var box_id_user = $('#id_user'), box_name_user = $('#name_user'), resultados = $("#resultados-search");

    box_name_user.keyup(function () {
        $buscar = $(this).val();
        //console.log($buscar);
        if ($buscar.length > 2) {
            $.ajax({
                url: pg.ajaxurl,
                method: "POST",
                //dataType: "html",
                data: {
                    "action": "pgFunctionSearchUsers",
                    "texto": $buscar,
                    "busqueda": 1
                },
                beforeSend: function () {
                    resultados.html("Buscando");
                    resultados.attr("data-open", "false");
                },
                success: function (data) {
                    $texto = "";
                    resultados.attr("data-open", "true");


                    console.log(data);
                    data.forEach(function (item) {
                        console.log(item);
                        $texto += "<div class='item-search' data-id='" + item['id'] + "'><span class='texto'>" + item['name'] + "</span></div>"
                    });
                    resultados.html($texto);
                },
                error: function (er) {
                    console.log(er);
                }
            });
        }
    });

    $('#resultados-search').on('click', ".item-search", function () {
        let id_item = $(this).data('id');
        let name_item = $(this).text();
        $("#name_user").val(name_item);
        $("#id_user").val(id_item);

        resultados.html("");
        resultados.attr("data-open", "false");
    })
});