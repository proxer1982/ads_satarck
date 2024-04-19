jQuery(document).ready(function ($) {
  var frame, metaBox = $('#meta-box-satrack'), imgIdInput, imgCont, objeto, btn_delete, resultados = $("#resultados-search");

  $('.tipo-url').change(function () {
    let tipo = $(this).val();
    if (tipo == "id") {
      $('#field_url_anuncio').attr('type', 'hidden');
      $('#field_url_anuncio').data('url', $('#field_url_anuncio').val());

      $('#field_url_anuncio').val($('#field_url_anuncio').data('id'));
      $('#aux-url').removeClass('hidden');

    } else if (tipo == "text") {
      $('#field_url_anuncio').attr('type', 'url');
      $('#aux-url').addClass('hidden');
      $('#field_url_anuncio').data('id', $('#field_url_anuncio').val());
      $('#field_url_anuncio').val($('#field_url_anuncio').data('url'));
      resultados.html("");
    }
  });

  $("#aux-url").keyup(function () {
    $buscar = $(this).val();
    if ($buscar.length > 2) {
      $.ajax({
        url: pg.ajaxurl,
        method: "POST",
        data: {
          "action": "pgFunctionSearchPost",
          "texto": $buscar
        },
        beforeSend: function () {
          resultados.html("Buscando");
        },
        success: function (data) {
          $texto = "";
          resultados.data("open", "true");

          data.forEach(function (item) {
            console.log(item);
            $texto += "<div class='item-search' data-url='" + item['url'] + "' data-id='" + item['id'] + "'><span class='texto'>" + item['titulo'] + "</span> <span class='type-post'>" + item['type'] + "</span></div>"
          });
          resultados.html($texto);
        },
        error: function (er) {
          console.log(er);
        }
      });
    }
  });

  $("#field_url_anuncio").on('change', function () {
    if ($(this).attr('type') != "hidden") {
      let valor = parseInt($(this).val());

      if (Number.isInteger(valor) && $(this).attr('type') == "url") {
        $(this).attr('type', 'number');
      } else if (!Number.isInteger(valor) && $(this).attr('type') == "number") {
        $(this).attr('type', 'url');
      }
    }
  });


  $('.upload-button').click(function (e) {
    objeto = $(this).data("imagen");

    imgIdInput = metaBox.find("#field_" + objeto);
    imgCont = metaBox.find("#cont_" + objeto);

    btn_upload = metaBox.find('#add_' + objeto);
    btn_delete = metaBox.find('#del_' + objeto);

    e.preventDefault();
    // If the uploader object has already been created, reopen the dialog
    if (frame) {
      frame.open();
      return;
    }

    // Extend the wp.media object
    frame = wp.media({
      title: 'SSeleccione una imagen',
      button: {
        text: 'Use esta imagen'
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });


    // When an image is selected in the media frame...
    frame.on('select', function () {
      // Get media attachment details from the frame state
      var attachment = frame.state().get('selection').first().toJSON();

      // Send the attachment URL to our custom image input field.
      imgCont.append('<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>');

      // Send the attachment id to our hidden input
      imgIdInput.val(attachment.id);
      // Hide the add image link
      btn_upload.addClass('hidden');

      // Unhide the remove image link
      btn_delete.removeClass('hidden');
    });

    // Finally, open the modal on click
    frame.open();
  });

  // DELETE IMAGE LINK
  $('.delimg-button').on('click', function (event) {
    objeto = $(this).data("imagen");

    imgIdInput = metaBox.find("#field_" + objeto);
    imgCont = metaBox.find("#cont_" + objeto);

    event.preventDefault();

    imgCont.html('');
    $('#add_' + objeto).removeClass('hidden');

    $('#del_' + objeto).addClass('hidden');

    imgIdInput.val('');
  });


  $('#resultados-search').on('click', ".item-search", function () {
    let id_item = $(this).data('id');
    $("#field_url_anuncio").val(id_item);
    $("#aux-url").val($(this).children('.texto').text());
    $("#field_url_anuncio").data('url', $(this).data('url'));
    $("#field_url_anuncio").data('id', id_item);
    resultados.html("");
    resultados.data("open", "false");
  })

  resultados.hover(function(){
    $(this).data("hover", "true");
  });

  resultados.mouseout(function(){
    $(this).data("hover", "false");
  });

  $('#aux-url').blur(function () {
    setTimeout(function(){
      if (resultados.data("open") == "true" && resultados.data("hover") == "false") {
        resultados.html("");
        $('#aux-url').val(" ");
        resultados.data("open", "false");
      }
    }, 220);
    
  });

});