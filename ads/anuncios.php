<?php

/**
 *  WpApiFeaturedImage
 *
 *  Adds featured images to the products endpoint
 *  using register_rest_field hook.
 *
 *  @version   1.0
 *  @author    Juan Zorro
 */

if (!defined('ABSPATH')) die;


class Anuncios
{

  /**
   * The endpoints we want to target
   */
  public $target_endpoints = '';

  /**
   * Constructor
   * @uses rest_api_init
   */
  function __construct()
  {
    $this->target_endpoints = array('satrack-ads');

    add_action('init', array($this, 'ads_satrack_post_type'));

    add_action("wp_ajax_pgFunctionSearchPost", array($this, "pgFunctionSearchPost"));
    add_filter('the_content', array($this, 'filter_the_content_satrack'), 1);

    add_action('add_meta_boxes', array($this, 'my_fields_ads_satrack_metabox'));
    add_action('save_post', array($this, 'my_fields_ads_satrack_save_data'));
    add_action('admin_enqueue_scripts', array($this, 'custom_jquery_image'));

    add_action('add_meta_boxes', array($this, 'my_fields_post_satrack_metabox'));
    add_action('save_post', array($this, 'my_fields_post_satrack_save_data'));
    add_action('wp_enqueue_scripts', array($this, 'misrecursosADSSatrack'));
  }

  /* Funcion de busqueda de los*/

  public function ads_satrack_post_type()
  {
    /**
     * Post Type: Comunicados Login.
     */

    $labels = array(
      "name" => __("Anuncios blog", "satrack-ads"),
      "singular_name" => __("Anuncio blog", "satrack-ads"),
      "menu_name" => __("ADS Satrack", "satrack-ads"),
      "all_items" => __("Todos los anuncios", "satrack-ads"),
      "add_new" => __("Añadir nuevo", "satrack-ads"),
      "add_new_item" => __("Añadir nuevo anuncio", "satrack-ads"),
      "edit_item" => __("Editar anuncio", "satrack-ads"),
      "new_item" => __("Nuevo anuncio", "satrack-ads"),
      "view_item" => __("Ver anuncio", "satrack-ads"),
      "view_items" => __("Ver anuncios", "satrack-ads"),
      "search_items" => __("Buscar anuncios", "satrack-ads"),
      "not_found" => __("No se han encontrado anuncios", "satrack-ads"),
      "not_found_in_trash" => __("No hay anuncios en la papelera", "satrack-ads"),
    );

    $args = array(
      "label" => __("Satrack ADS", "satrack-ads"),
      "labels" => $labels,
      "description" => "Anuncios que se podran visualizar en el blog o tras partes del sitio web",
      "public" => true,
      "publicly_queryable" => false,
      "show_ui" => true,
      "delete_with_user" => false,
      "show_in_rest" => true,
      "rest_base" => "ads_satrack",
      "rest_controller_class" => "WP_REST_Posts_Controller",
      "menu_position" => 5,
      "has_archive" => false,
      "show_in_menu" => false,
      "show_in_nav_menus" => false,
      "exclude_from_search" => true,
      "capability_type" => "post",
      "map_meta_cap" => true,
      "hierarchical" => false,
      "rewrite" => array("slug" => "satrack-ads", "with_front" => true),
      "query_var" => false,
      "menu_icon" => "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNS40LjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxnPg0KCTxwYXRoIGQ9Ik00MzcuNCwyNDQuNWMtNS4yLDAtOS41LDQuMy05LjUsOS41djExMS4yYzAsMTMtMTAuNiwyMy42LTIzLjYsMjMuNkg3Mi42Yy0xMy4yLDAtMjQtMTAuNy0yNC0yNFYxNjEuOA0KCQljMC0xMy4yLDEwLjctMjQsMjQtMjRINDA0YzEzLjIsMCwyNCwxMC43LDI0LDI0djEyLjVjMCw1LjIsNC4zLDkuNSw5LjUsOS41czkuNS00LjMsOS41LTkuNXYtMTIuNWMwLTIzLjctMTkuMy00My00My00M0g3Mi42DQoJCWMtMjMuNywwLTQzLDE5LjMtNDMsNDN2MjAzLjFjMCwyMy43LDE5LjMsNDMsNDMsNDNoMzMxLjdjMjMuNSwwLDQyLjYtMTkuMSw0Mi42LTQyLjZWMjU0QzQ0Ni45LDI0OC44LDQ0Mi43LDI0NC41LDQzNy40LDI0NC41eiIvPg0KCTxwYXRoIGQ9Ik00NjkuMiwyMDguNmMtNi4yLDAtMTEuOCwyLjQtMTYuMSw2LjJMNDEzLjYsMTg3YzEuMi0yLjgsMS44LTUuOSwxLjgtOS4xYzAtMTMuMi0xMC44LTI0LTI0LTI0Yy0xMy4yLDAtMjQsMTAuOC0yNCwyNA0KCQljMCw2LjcsMi44LDEyLjgsNy4zLDE3LjJsLTU0LjEsOTEuNGMtMi4yLTAuNy00LjYtMS03LTFjLTYuOCwwLTEyLjksMi44LTE3LjMsNy40bC0zNy44LTIzLjRjMC44LTIuNSwxLjMtNS4xLDEuMy03LjgNCgkJYzAtMTMuMi0xMC44LTI0LTI0LTI0cy0yNCwxMC44LTI0LDI0YzAsMTMuMiwxMC44LDI0LDI0LDI0YzYuNSwwLDEyLjMtMi42LDE2LjctNi44bDM4LjIsMjMuNmMtMC43LDIuMi0xLDQuNi0xLDcNCgkJYzAsMTMuMiwxMC44LDI0LDI0LDI0YzEzLjIsMCwyNC0xMC44LDI0LTI0YzAtNi44LTIuOC0xMi45LTcuNC0xNy4zbDU0LTkxLjNjMi4zLDAuNyw0LjcsMS4xLDcuMSwxLjFjNiwwLDExLjQtMi4yLDE1LjYtNS44DQoJCWwzOS43LDI3LjljLTEsMi43LTEuNiw1LjUtMS42LDguNWMwLDEzLjIsMTAuOCwyNCwyNCwyNGMxMy4yLDAsMjQtMTAuOCwyNC0yNEM0OTMuMiwyMTkuMyw0ODIuNCwyMDguNiw0NjkuMiwyMDguNnoiLz4NCgk8cGF0aCBkPSJNMjYwLjksMzA5LjljMi45LDAsNS4yLTIuMyw1LjItNS4ycy0yLjMtNS4yLTUuMi01LjJIODAuN2MtMi45LDAtNS4yLDIuMy01LjIsNS4ydjYyLjVjMCwyLjksMi4zLDUuMiw1LjIsNS4ySDM5Ng0KCQljMi45LDAsNS4yLTIuMyw1LjItNS4ydi02Mi41YzAtMi45LTIuMy01LjItNS4yLTUuMmgtMjUuMWMtMi45LDAtNS4yLDIuMy01LjIsNS4yczIuMyw1LjIsNS4yLDUuMmgxOS45VjM2Mkg4NS45di01Mi4xSDI2MC45eiIvPg0KCTxwYXRoIGQ9Ik0xNzUuNSwyNTAuN0g4MC43Yy0yLjksMC01LjMsMi40LTUuMyw1LjNzMi40LDUuMyw1LjMsNS4zaDk0LjhjMi45LDAsNS4zLTIuNCw1LjMtNS4zUzE3OC40LDI1MC43LDE3NS41LDI1MC43eiIvPg0KCTxwYXRoIGQ9Ik0zMTcuMiwyMTIuOGMwLTIuOS0yLjQtNS4zLTUuMy01LjNIODAuN2MtMi45LDAtNS4zLDIuNC01LjMsNS4zczIuNCw1LjMsNS4zLDUuM2gyMzEuMg0KCQlDMzE0LjgsMjE4LjIsMzE3LjIsMjE1LjgsMzE3LjIsMjEyLjh6Ii8+DQoJPHBhdGggZD0iTTM1NS43LDE2OS43YzAtMi45LTIuNC01LjMtNS4zLTUuM0g4MC43Yy0yLjksMC01LjMsMi40LTUuMyw1LjNzMi40LDUuMyw1LjMsNS4zaDI2OS43QzM1My4zLDE3NSwzNTUuNywxNzIuNiwzNTUuNywxNjkuNw0KCQl6Ii8+DQo8L2c+DQo8L3N2Zz4NCg==",
      "supports" => array("title", "revisions")
    );

    register_post_type("satrack-ads", $args);
  }

  function pgFunctionSearchPost()
  {
    $salida = array();

    if (isset($_POST['texto']) && !empty($_POST['texto'])) {
      $args = array(
        'post_type' => array('post', 'page', 'e-landing-page', 'ed-satrack'),
        'post_status' => array('publish'),
        'post_per_page' => 10,
        's' => $_POST['texto'],
        'exact' => false,
        'sentance' => false,
        'tax_query' => array(
          array(
            'taxonomy' => 'post_format',
            'field' => 'slug',
            'terms' => array('satrack-ads'),
            'operator' => 'NOT IN'
          )
        )
      );


      $entradas = new WP_Query($args);

      if ($entradas->have_posts()) {
        while ($entradas->have_posts()) {
          $entradas->the_post();
          $tipo = "";
          switch (get_post_type()) {
            case "post":
              $tipo = "Entrada";
              break;
            case "page":
              $tipo = "Página";
              break;
            default:
              break;
          }
          $salida[] = array(
            'titulo' => get_the_title(),
            'id' => get_the_id(),
            'url' => get_permalink(),
            'type' => $tipo
          );
        }
      }
    }
    wp_send_json($salida);
  }

  function filter_the_content_satrack($content)
  {
    $custom_fields = get_post_custom();

    // Check if we're inside the main loop in a single Post.
    if (is_singular() && is_main_query() && (isset($custom_fields['field_id_anuncio_satrack']) && !empty($custom_fields['field_id_anuncio_satrack'][0]))) {
      $id_anuncio = $custom_fields['field_id_anuncio_satrack'][0];

      $datos_Aviso = get_post_custom($id_anuncio);
      $titulo = get_the_title($id_anuncio);
      $img_desk = wp_get_attachment_image($datos_Aviso['field_imagen_desktop'][0], 'full', false, array("alt" => $titulo));
      $img_mobile = wp_get_attachment_image($datos_Aviso['field_imagen_mobile'][0], 'full', false, array("alt" => $titulo));

      if (empty($img_mobile)) {
        $img_mobile = wp_get_attachment_image($datos_Aviso['field_imagen_desktop'][0], 'medium', false, array("alt" => $titulo));
      }

      if (filter_var($datos_Aviso['field_url_anuncio'][0], FILTER_VALIDATE_URL)) {
        $url_anuncio = $datos_Aviso['field_url_anuncio'][0];
      } else {
        $url_anuncio = get_permalink($datos_Aviso['field_url_anuncio'][0]);
      }

      switch ($datos_Aviso['field_ventana_anuncio'][0]) {
        case "SI":
          $target = "_self";
          break;
        case "NO":
          $target = "_blank";
          break;
        default:
          $target = "_self";
          break;
      }

      $anuncio = "<div class='caja-anuncios_satrack'>
        <div class='imagen-desktop'>
          <a href='" . $url_anuncio . "' target='" . $target . "' rel='" . get_the_title($datos_Aviso['field_url_anuncio'][0]) . "'>" . $img_desk . "</a>
        </div>
        <div class='imagen-mobile'>
        <a href='" . $url_anuncio . "' target='' rel='" . get_the_title($datos_Aviso['field_url_anuncio'][0]) . "'>" . $img_mobile . "</a>
        </div>
      </div>";

      return $content . $anuncio;
    }

    return $content;
  }

  function my_fields_post_satrack_metabox()
  {
    add_meta_box('custom-fields-metabox2', 'ADS Satrack', array($this, 'my_fields_post_satrack'), 'post', 'side', 'high');
  }



  function my_fields_post_satrack($post)
  {
    $id_anuncio = get_post_meta($post->ID, 'field_id_anuncio_satrack', true);
    $datos = array();
    $args = array(
      'post_type' => 'satrack-ads',
      'post_status' => 'publish'
    );

    $adss = new WP_Query($args);
    if ($adss->have_posts()) {
      while ($adss->have_posts()) {
        $anuncio = $adss->the_post();
        $datos[] = array(
          "titulo" => get_the_title(),
          "id" => get_the_id()
        );
      }
    }
    wp_reset_query();

?>
    <table cellpadding="1" id="meta-box-satrack" cellspacing="1" border="0">
      <tr>
        <td width="100%">
          <div class="postbox">
            <?php wp_nonce_field('custom_fields_metabox2', 'custom_fields_metabox2_nonce'); ?>
            <select name="field_id_anuncio_satrack" id="field_id_anuncio_satrack">
              <option value="0">Seleccione un anuncio</option>
              <?php foreach ($datos as $dato) : ?>
                <option value="<?= $dato['id']; ?>" <?php if ($dato['id'] == $id_anuncio) {
                                                      echo "selected";
                                                    } ?>><?=
                                                          $dato['titulo']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </td>
      </tr>

    </table>
  <?php
  }

  function my_fields_post_satrack_save_data($post_id)
  {
    // Comprobamos si se ha definido el nonce.
    /*if ( ! isset( $_POST['custom_fields_metabox2_nonce'] ) ) {
    return $post_id;
    }
    $nonce = $_POST['custom_fields_metabox2_nonce'];
    
    // Verificamos que el nonce es válido.
    if ( !wp_verify_nonce( $nonce, 'fcustom_fields_metabox2' ) ) {
    return $post_id;
    }
    */
    // Si es un autoguardado nuestro formulario no se enviará, ya que aún no queremos hacer nada.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    // Comprobamos los permisos de usuario.
    if (isset($_POST['post_type']) && $_POST['post_type'] == 'post') {
      if (!current_user_can('edit_page', $post_id))
        return $post_id;
    } else {
      if (!current_user_can('edit_post', $post_id))
        return $post_id;
    }
    $field_id_anuncio_satrack = "";
    // Vale, ya es seguro que guardemos los datos.
    $old_field_id_anuncio_satrack = get_post_meta($post_id, 'field_id_anuncio_satrack', true);
    if (isset($_POST['field_id_anuncio_satrack']) && !empty($_POST['field_id_anuncio_satrack'])) {
      $field_id_anuncio_satrack = $_POST['field_id_anuncio_satrack'];
    }


    if (($old_field_id_anuncio_satrack && $field_id_anuncio_satrack == 0) || ($field_id_anuncio_satrack != 0)) {
      // Actualizamos el campo meta en la base de datos.
      update_post_meta($post_id, 'field_id_anuncio_satrack', $field_id_anuncio_satrack, $old_field_id_anuncio_satrack);
    }
  }



  function my_fields_ads_satrack_metabox()
  {
    add_meta_box('custom-fields-metabox', 'Configuración del anuncio', array($this, 'my_fields_ads_satrack'), array('satrack-ads'), 'normal', 'high');
  }


  function my_fields_ads_satrack($post)
  {
    $your_url_anuncio = get_post_meta($post->ID, 'field_url_anuncio', true);
    $tipo_url = filter_var($your_url_anuncio, FILTER_VALIDATE_URL);
    $your_ventana_anuncio = get_post_meta($post->ID, 'field_ventana_anuncio', true);

    if (empty($your_ventana_anuncio)) {
      $your_ventana_anuncio = "SI";
    }

    $your_img_desk_id = get_post_meta($post->ID, 'field_imagen_desktop', true);
    $your_img_desk_src = wp_get_attachment_url($your_img_desk_id);

    $your_img_mobile_id = get_post_meta($post->ID, 'field_imagen_mobile', true);
    $your_img_mobile_src = wp_get_attachment_url($your_img_mobile_id);

    // For convenience, see if the array is valid
    if (strlen($your_img_desk_src)) {
      $you_have_img_desk = true;
    } else {
      $you_have_img_desk = false;
    };

    if (strlen($your_img_mobile_src)) {
      $you_have_img_mobile = true;
    } else {
      $you_have_img_mobile = false;
    }


    // Se añade un campo nonce para probarlo más adelante cuando validemos
    wp_nonce_field('fields_metabox_ads_satrack', 'fields_metabox_ads_satrack_nonce'); ?>

    <table width="100%" cellpadding="1" id="meta-box-satrack" cellspacing="1" border="0">
      <tr>
        <td width="100%">
          <div class="postbox" style="padding:20px;">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td colspan="2">
                  <h4>URL del anuncio</h4>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <label><input type="radio" name="tipo-url" <?php if (!$tipo_url) {
                                                                echo "checked";
                                                              } ?> class="tipo-url" value="id"> Contenido de la página</label> &nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="tipo-url" class="tipo-url" <?php if ($tipo_url) {
                                                                                                                                                                                                                      echo "checked";
                                                                                                                                                                                                                    } ?> value="text"> URL
                    externa</label>
                </td>
              </tr>
              <tr>
                <td style="vertical-align: bottom;">
                  <div id="caja-post-search"><input type="text" id="aux-url" class="<?php if ($tipo_url) {
                                                                                      echo "hidden";
                                                                                    } ?>" style="width:100%;" placeholder="Seleccione su destino" value="<?php if (!$tipo_url) {
                                                                                                                                                            echo get_the_title($your_url_anuncio);
                                                                                                                                                          } ?>">
                    <div id="resultados-search"></div>
                  </div>

                  <?php wp_nonce_field('custom_fields_metabox', 'custom_fields_metabox_nonce'); ?>
                  <input placeholder="https://" id="field_url_anuncio" name="field_url_anuncio" <?php if (!$tipo_url) {
                                                                                                  echo "type='hidden'";
                                                                                                } else {
                                                                                                  echo "type='url'";
                                                                                                } ?> value="<?php echo esc_attr($your_url_anuncio); ?>" style="width:100%;" data-url="<?php if (!$tipo_url) {
                                                                                                                                                                                        echo get_permalink($your_url_anuncio);
                                                                                                                                                                                      } else {
                                                                                                                                                                                        echo $your_url_anuncio;
                                                                                                                                                                                      } ?>" data-id="<?php if (!$tipo_url) {
                                                                                                                          echo $your_url_anuncio;
                                                                                                                        } ?>">
                </td>
                <td width="220" style="width:220px; text-align:center;">
                  <h4>Abrir en al misma ventana</h4>
                  <label>SI &nbsp;<input name="field_ventana_anuncio" type="radio" <?php
                                                                                    if ($your_ventana_anuncio == "SI") {
                                                                                      echo "checked='checked'";
                                                                                    } ?> value="SI"></label>
                  &nbsp;&nbsp;&nbsp;&nbsp;<label for="">NO &nbsp;
                    <input name="field_ventana_anuncio" type="radio" <?php if ($your_ventana_anuncio == "NO") {
                                                                        echo "checked='checked'";
                                                                      } ?> value="NO"></label>
                </td>
              </tr>
            </table>
          </div>
        </td>
      </tr>
      <tr>
        <td width="100%">
          <div class="postbox meta-box-image_desk" style="padding:20px;">
            <h3 style="padding:10px 0;">Imagen para escritorio</h3>
            <!-- Your image container, which can be manipulated with js -->
            <div class="custom-img-container" id="cont_imagen_desktop">
              <?php if ($you_have_img_desk) : ?>
                <img width="100%" src="<?= $your_img_desk_src; ?>" />
              <?php endif; ?>
            </div>

            <!-- Your add & remove image links -->
            <p class="hide-if-no-js">
              <buttom id="add_imagen_desktop" class="button button-primary button-large upload-button <?php if ($you_have_img_desk) {
                                                                                                        echo 'hidden';
                                                                                                      } ?>" data-imagen="imagen_desktop">
                <?php _e('Seleccione imagen para escritorio') ?>
              </buttom>
              <buttom id="del_imagen_desktop" class="button button-primary button-large delimg-button <?php if (!$you_have_img_desk) {
                                                                                                        echo 'hidden';
                                                                                                      } ?>" data-imagen="imagen_desktop">
                <?php _e('Quitar imagen para escritorio') ?>
              </buttom>
            </p>

            <!-- A hidden input to set and post the chosen image id -->
            <input id="field_imagen_desktop" name="field_imagen_desktop" type="hidden" value="<?php echo esc_attr($your_img_desk_id); ?>">
          </div>
        </td>
      </tr>

      <tr>
        <td width="100%">
          <div class="postbox meta-box-image_mobile" style="padding:20px;">
            <h3 style="padding:10px 0;">Imagen para celular</h3>
            <!-- Your image container, which can be manipulated with js -->
            <div class="custom-img-container" id="cont_imagen_mobile">
              <?php if ($you_have_img_mobile) : ?>
                <img style="max-width:100%;" src="<?= $your_img_mobile_src; ?>" />
              <?php endif; ?>
            </div>

            <!-- Your add & remove image links -->
            <p class="hide-if-no-js">
              <buttom id="add_imagen_mobile" class="button button-primary button-large upload-button <?php if ($you_have_img_mobile) {
                                                                                                        echo 'hidden';
                                                                                                      } ?>" data-imagen="imagen_mobile">
                <?php _e('Seleccione imagen para celular') ?>
              </buttom>
              <buttom id="del_imagen_mobile" class="button button-primary button-large delimg-button <?php if (!$you_have_img_mobile) {
                                                                                                        echo 'hidden';
                                                                                                      } ?>" data-imagen="imagen_mobile">
                <?php _e('Quitar imagen para celular') ?>
              </buttom>
            </p>

            <!-- A hidden input to set and post the chosen image id -->
            <input id="field_imagen_mobile" name="field_imagen_mobile" type="hidden" value="<?php echo esc_attr($your_img_mobile_id); ?>">
          </div>
        </td>
      </tr>
    </table>
<?php }

  function my_fields_ads_satrack_save_data($post_id)
  {
    // Comprobamos si se ha definido el nonce.
    if (!isset($_POST['fields_metabox_ads_satrack_nonce'])) {
      return $post_id;
    }
    $nonce = $_POST['fields_metabox_ads_satrack_nonce'];

    // Verificamos que el nonce es válido.
    if (!wp_verify_nonce($nonce, 'fields_metabox_ads_satrack')) {
      return $post_id;
    }

    // Si es un autoguardado nuestro formulario no se enviará, ya que aún no queremos hacer nada.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }

    // Comprobamos los permisos de usuario.
    if ($_POST['post_type'] == 'post') {
      if (!current_user_can('edit_page', $post_id))
        return $post_id;
    } else {
      if (!current_user_can('edit_post', $post_id))
        return $post_id;
    }

    // Vale, ya es seguro que guardemos los datos.
    $old_field_url_anuncio = get_post_meta($post_id, 'field_url_anuncio', true);
    $field_url_anuncio = $_POST['field_url_anuncio'];
    if (filter_var($field_url_anuncio, FILTER_VALIDATE_URL)) {
      $field_url_anuncio = sanitize_url($field_url_anuncio);
    } else {
      $field_url_anuncio = intval($_POST['field_url_anuncio']);
    }

    $old_field_ventana_anuncio = get_post_meta($post_id, 'field_ventana_anuncio', true);
    $field_ventana_anuncio = $_POST['field_ventana_anuncio'];

    $old_field_imagen_desktop = get_post_meta($post_id, 'field_imagen_desktop', true);
    $field_imagen_desktop = $_POST['field_imagen_desktop'];

    $old_field_imagen_mobile = get_post_meta($post_id, 'field_imagen_mobile', true);
    $field_imagen_mobile = $_POST['field_imagen_mobile'];

    // Actualizamos el campo meta en la base de datos.
    update_post_meta($post_id, 'field_url_anuncio', $field_url_anuncio, $old_field_url_anuncio);
    update_post_meta($post_id, 'field_ventana_anuncio', $field_ventana_anuncio, $old_field_ventana_anuncio);
    update_post_meta($post_id, 'field_imagen_desktop', $field_imagen_desktop, $old_field_imagen_desktop);
    update_post_meta($post_id, 'field_imagen_mobile', $field_imagen_mobile, $old_field_imagen_mobile);
  }


  function custom_jquery_image()
  {

    global $post_type;
    if ($post_type == 'satrack-ads') {
      wp_enqueue_media();
      wp_register_script('manejo_img', plugin_dir_url(__FILE__) . '/assets/js/acciones.js', array('jquery'), MY_SATRACK_ADS_VERSION, true);
      wp_enqueue_script('manejo_img');
      wp_localize_script('manejo_img', 'pg', array('ajaxurl' => admin_url('admin-ajax.php')));

      wp_enqueue_style('manejo_img-style', plugin_dir_url(__FILE__) . 'assets/css/manejo_img.css', null, MY_SATRACK_ADS_VERSION);
    } elseif ($post_type == 'post' || $post_type == 'page') {
      wp_enqueue_style('manejo_img-style', plugin_dir_url(__FILE__) . 'assets/css/manejo_img.css', null, MY_SATRACK_ADS_VERSION);
    }
  }


  function misrecursosADSSatrack()
  {
    wp_register_style('ads-satrack', plugin_dir_url(__FILE__) . 'assets/css/ads-satrack.css', array(), "1.0.1");
    wp_enqueue_style('ads-satrack');
  }
}
