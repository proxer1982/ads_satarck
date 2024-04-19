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


class ECards_Str
{

    /**
     * The endpoints we want to target
     */
    public $target_endpoints = '', $colaborador, $qr, $usi = "<h2>din don din don</h2>";

    /**
     * Constructor
     * @uses rest_api_init
     */
    function __construct()
    {
        $this->target_endpoints = array('ecards_satrack');
        add_theme_support('title-tag');

        //add_action('init', [$this, 'unregister_cpt_tax']);
        add_action('init', array($this, 'ecards_satrack_post_type'));

        add_action('add_meta_boxes', [$this, 'my_fields_ecards_satrack_metabox']);
        add_action("wp_ajax_pgFunctionSearchUsers", [$this, "pgFunctionSearchUsers"]);

        add_action('admin_enqueue_scripts', [$this, 'custom_code_admin_cards_satrack']);
        add_action('wp_enqueue_scripts', [$this, 'custom_code_cards_satrack']);
        add_action('save_post', array($this, 'my_fields_ecards_satrack_save_data'));


        add_filter('get_header', [$this, 'modificar_header_cards_satrack']);

        //add_action('init', array($this, 'ob_function'));

        add_filter('the_content', [$this, 'agregar_content_ecards'], 1);


        add_filter('wp_title', array($this, 'filter_function_name'), 10, 3);

        add_shortcode('qr_ecards', [$this, 'qr_ecards_satrack_shortcode']);

        //add_action( 'pre_get_post', [$this, 'add_my_post_to_query'] )
    }


    public function unregister_cpt_tax()
    {
        unregister_post_type('ecard-satrack');
        unregister_taxonomy('ecard-satrack');
    }

    public function ecards_satrack_post_type()
    {
        /**
         * Post Type: Comunicados Login.
         */

        $labels = array(
            "name" => __("E Cards Satrack", "ecards_satrack"),
            "singular_name" => __("E Card", "ecards_satrack"),
            "menu_name" => __("E Cards", "ecards_satrack"),
            "all_items" => __("Todas las E Cards", "ecards_satrack"),
            "add_new" => __("Añadir nueva E Card", "ecards_satrack"),
            "add_new_item" => __("Añadir nueva E Card", "ecards_satrack"),
            "edit_item" => __("Editar E Card", "ecards_satrack"),
            "new_item" => __("Nueva E Card", "ecards_satrack"),
            "view_item" => __("Ver E Card", "ecards_satrack"),
            "view_items" => __("Ver E Cards", "ecards_satrack"),
            "search_items" => __("Buscar E Cards", "ecards_satrack"),
            "not_found" => __("No se han encontrado E Cards", "ecards_satrack"),
            "not_found_in_trash" => __("No hay E Cards en la papelera", "ecards_satrack"),
        );

        $args = array(
            'label'              => 'E card',
            'labels'             => $labels,
            'description'        => 'E Cards para colaboradores Satrack',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            /*'rewrite'            => ['slug' => 'ecard-satrack', 'with_front' => true, 'pages' => true, 'feeds' => true],*/
            'capability_type'    => 'page',
            'has_archive'        => false,
            'hierarchical'       => false,
            'exclude_from_search' => true,
            'menu_position'      => 5,
            'show_in_rest'       => false,
            "menu_icon" => "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IiB2aWV3Qm94PSIwIDAgMTIyLjg4IDEwMS4zNyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMTIyLjg4IDEwMS4zNyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBhdGggZD0iTTEyLjY0LDc3LjI3bDAuMzEtNTQuOTJoLTYuMnY2OS44OGM4LjUyLTIuMiwxNy4wNy0zLjYsMjUuNjgtMy42NmM3Ljk1LTAuMDUsMTUuOSwxLjA2LDIzLjg3LDMuNzYgYy00Ljk1LTQuMDEtMTAuNDctNi45Ni0xNi4zNi04Ljg4Yy03LjQyLTIuNDItMTUuNDQtMy4yMi0yMy42Ni0yLjUyYy0xLjg2LDAuMTUtMy40OC0xLjIzLTMuNjQtMy4wOCBDMTIuNjIsNzcuNjUsMTIuNjIsNzcuNDYsMTIuNjQsNzcuMjdMMTIuNjQsNzcuMjd6IE0xMDMuNjIsMTkuNDhjLTAuMDItMC4xNi0wLjA0LTAuMzMtMC4wNC0wLjUxYzAtMC4xNywwLjAxLTAuMzQsMC4wNC0wLjUxVjcuMzQgYy03LjgtMC43NC0xNS44NCwwLjEyLTIyLjg2LDIuNzhjLTYuNTYsMi40OS0xMi4yMiw2LjU4LTE1LjksMTIuNDRWODUuOWM1LjcyLTMuODIsMTEuNTctNi45NiwxNy41OC05LjEgYzYuODUtMi40NCwxMy44OS0zLjYsMjEuMTgtMy4wMlYxOS40OEwxMDMuNjIsMTkuNDh6IE0xMTAuMzcsMTUuNmg5LjE0YzEuODYsMCwzLjM3LDEuNTEsMy4zNywzLjM3djc3LjY2IGMwLDEuODYtMS41MSwzLjM3LTMuMzcsMy4zN2MtMC4zOCwwLTAuNzUtMC4wNi0xLjA5LTAuMThjLTkuNC0yLjY5LTE4Ljc0LTQuNDgtMjcuOTktNC41NGMtOS4wMi0wLjA2LTE4LjAzLDEuNTMtMjcuMDgsNS41MiBjLTAuNTYsMC4zNy0xLjIzLDAuNTctMS45MiwwLjU2Yy0wLjY4LDAuMDEtMS4zNS0wLjE5LTEuOTItMC41NmMtOS4wNC00LTE4LjA2LTUuNTgtMjcuMDgtNS41MmMtOS4yNSwwLjA2LTE4LjU4LDEuODUtMjcuOTksNC41NCBjLTAuMzQsMC4xMi0wLjcxLDAuMTgtMS4wOSwwLjE4QzEuNTEsMTAwLjAxLDAsOTguNSwwLDk2LjY0VjE4Ljk3YzAtMS44NiwxLjUxLTMuMzcsMy4zNy0zLjM3aDkuNjFsMC4wNi0xMS4yNiBjMC4wMS0xLjYyLDEuMTUtMi45NiwyLjY4LTMuMjhsMCwwYzguODctMS44NSwxOS42NS0xLjM5LDI5LjEsMi4yM2M2LjUzLDIuNSwxMi40Niw2LjQ5LDE2Ljc5LDEyLjI1IGM0LjM3LTUuMzcsMTAuMjEtOS4yMywxNi43OC0xMS43MmM4Ljk4LTMuNDEsMTkuMzQtNC4yMywyOS4wOS0yLjhjMS42OCwwLjI0LDIuODgsMS42OSwyLjg4LDMuMzNoMFYxNS42TDExMC4zNywxNS42eiBNNjguMTMsOTEuODJjNy40NS0yLjM0LDE0Ljg5LTMuMywyMi4zMy0zLjI2YzguNjEsMC4wNSwxNy4xNiwxLjQ2LDI1LjY4LDMuNjZWMjIuMzVoLTUuNzd2NTUuMjJjMCwxLjg2LTEuNTEsMy4zNy0zLjM3LDMuMzcgYy0wLjI3LDAtMC41My0wLjAzLTAuNzgtMC4wOWMtNy4zOC0xLjE2LTE0LjUzLTAuMi0yMS41MSwyLjI5Qzc5LjA5LDg1LjE1LDczLjU3LDg4LjE1LDY4LjEzLDkxLjgyTDY4LjEzLDkxLjgyeiBNNTguMTIsODUuMjUgVjIyLjQ2Yy0zLjUzLTYuMjMtOS4yNC0xMC40LTE1LjY5LTEyLjg3Yy03LjMxLTIuOC0xNS41Mi0zLjQzLTIyLjY4LTIuNDFsLTAuMzgsNjYuODFjNy44MS0wLjI4LDE1LjQ1LDAuNzEsMjIuNjQsMy4wNiBDNDcuNzMsNzguOTEsNTMuMTUsODEuNjQsNTguMTIsODUuMjVMNTguMTIsODUuMjV6Ii8+PC9nPjwvc3ZnPg==",
            "supports" => array('title', 'author', 'thumbnail', "revisions")
        );

        register_post_type("ecard-satrack", $args);

        add_filter('single_template', [$this, 'ecards_satrack_templates']);
    }


    function my_fields_ecards_satrack_metabox()
    {
        add_meta_box('custom-fields-metabox-ecards', 'Datos colaborador', array($this, 'my_fields_ecards_satrack'), array('ecard-satrack'), 'normal', 'high');
    }

    public function my_fields_ecards_satrack($post)
    {
        $this->colaborador = get_post_meta($post->ID, 'field_id_user', true);

        // Se añade un campo nonce para probarlo más adelante cuando validemos
        wp_nonce_field('fields_metabox_ecards_satrack', 'fields_metabox_ecards_satrack_nonce'); ?>

        <table width="100%" cellpadding="1" id="meta-box-cards_satrack" cellspacing="1" border="0">
            <tr>
                <td width="100%">
                    <div class="postbox" style="padding:20px;">
                        <table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <tr>
                                <td colspan="2">
                                    <h4>Colaborador</h4>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: bottom;">
                                    <div id="caja-post-search"><input type="text" id="name_user" name="name_user" style="width:100%;" placeholder="Seleccione un usuario" value="<?php if (!empty($this->colaborador['name']))   echo $this->colaborador['name'] ?>">
                                        <input type="text" id="id_user" name="id_user" value="<?php if (!empty($this->colaborador['id']))   echo $this->colaborador['id'] ?>">
                                        <div id="resultados-search" data-open="false"></div>
                                    </div>

                                </td>

                            </tr>
                        </table>
                    </div>
                </td>
            </tr>

        </table>
<?php
    }

    public function my_fields_ecards_satrack_save_data($post_id)
    {
        // Comprobamos si se ha definido el nonce.
        if (!isset($_POST['fields_metabox_ecards_satrack_nonce'])) {
            return $post_id;
        }
        $nonce = $_POST['fields_metabox_ecards_satrack_nonce'];

        // Verificamos que el nonce es válido.
        if (!wp_verify_nonce($nonce, 'fields_metabox_ecards_satrack')) {
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
        $old_field_id_user = get_post_meta($post_id, 'field_id_user', true);
        $field_id_user = [];
        $field_id_user["id"] = (int) sanitize_text_field($_POST['id_user']);
        $field_id_user['name'] = sanitize_text_field($_POST['name_user']);

        // Actualizamos el campo meta en la base de datos.
        update_post_meta($post_id, 'field_id_user', $field_id_user, $old_field_id_user);
    }

    public function pgFunctionSearchUsers()
    {
        $salida = array();

        if (isset($_POST['busqueda']) && !empty($_POST['texto'])) {
            $buscar = sanitize_text_field($_POST['texto']);

            $args = array(
                'search'         => "*" . $buscar . "*",
                'search_columns' => array('user_login', 'user_email'),
                'fields' => ['ID', 'display_name']
            );
            $consulta = new WP_User_Query($args);

            foreach ($consulta->results as $user) {
                $salida[] = array(
                    "id" => $user->ID,
                    "name" => $user->display_name
                );
            }
        }
        //return json_encode($salida);
        wp_send_json($salida);
    }

    function custom_code_admin_cards_satrack()
    {
        global $post_type;
        if ($post_type == 'ecard-satrack') {
            //wp_enqueue_media();
            wp_register_script('e_cards_satrack', plugin_dir_url(__FILE__) . 'assets/js/ecard_admin_satrack.js', array('jquery'), MY_SATRACK_ADS_VERSION, true);
            wp_enqueue_script('e_cards_satrack');
            wp_localize_script('e_cards_satrack', 'pg', array('ajaxurl' => admin_url('admin-ajax.php')));

            wp_enqueue_style('e_cards_satrack', plugin_dir_url(__FILE__) . 'assets/css/e_cards_satrack_admin.css', null, MY_SATRACK_ADS_VERSION);
        }
    }

    function custom_code_cards_satrack()
    {
        global $post_type;

        if ($post_type == 'ecard-satrack') {
            //wp_enqueue_style('e_cards_satrack', plugin_dir_url(__FILE__) . 'assets/css/e_cards_satrack.css', null, MY_SATRACK_ADS_VERSION);
            wp_enqueue_style('e_cards_satrack', plugin_dir_url(__FILE__) . 'assets/css/e_cards_satrack.css', null, MY_SATRACK_ADS_VERSION);

            wp_register_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', array(), '5.2.3');



            wp_register_script('popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js', array('jquery'), '5.2.3');
            wp_enqueue_script('popper');
            wp_enqueue_style('bootstrap');
            wp_register_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js', array('jquery'), '5.2.3');
            wp_enqueue_script('bootstrap');
            wp_register_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css', array(), '5.9.0');
        }
    }

    function modificar_header_cards_satrack($headers)
    {

        // var_dump($headers); #=> if you want to see the current headers...  

        global $post_type;
        if ($post_type == 'ecard-satrack') {
            print_r($headers);
        }

        return $headers;
    }

    public function ecards_satrack_templates($template)
    {
        global $post_type;
        if ($post_type == 'ecard-satrack') {
            $template = plugin_dir_path(__FILE__) . 'templates/single_ecards.php';
        }

        return $template;
    }

    public function qr_ecards_satrack_shortcode()
    {
        return "<img src='data:image/png;base64,{$this->qr}' />";
    }

    public function agregar_content_ecards($contenido)
    {
        global $post;

        if ($post->post_type == 'ecard-satrack') {
            $this->load_colaborador($post->ID);

            $web = str_replace('https://', "", $this->colaborador->weburl);
            $phone = str_replace(' ', "", $this->colaborador->phone);
            $log = wp_upload_dir('2023/08');
            $log = $log['url'] . "/logo_con_texto.svg";

            $contenido .= "
            <div class='card'>
                <div class='card-header d-flex flex-column w-100'>
                    <div class='caja_imagen_col'>
                        <span class='loader'></span>
                        <div class='imagen_col'>
                            <img src='" . get_the_post_thumbnail_url() . "' />
                        </div>
                    </div>
                    
                </div>
                <div class='card-body d-flex flex-column align-items-center'>
                    <div class='card-title d-flex flex-column'>
                        <h1 class='text-center nombre_colaborador'>{$this->colaborador->name}</h1>
                        <h2 class='text-center cargo_colaborador'>{$this->colaborador->title}</h2>
                    </div>
                    <div class='item-datos d-flex align-items-center'>
                        <div class='text-center'><i class='icono far fa-envelope'></i></div>
                        <div class='flex-fill text-center'>
                            <a href='mailto:{$this->colaborador->email}?subject=Quiero%20conocer%20m%C3%A1s%20de%20Satrack&body=Hola%20estimad@%20{$this->colaborador->first_name}%2C%0D%0A%0D%0AMe%20gustar%C3%ADa%20conocer%20c%C3%B3mo%20Satrack%20y%20su%20tecnolog%C3%ADa%20le%20ayudar%C3%ADa%20a%20mi%20negocio%20a%3A%0D%0A%0D%0A-%20Tener%20mayor%20control%20de%20las%20flotas.%0D%0A-%20Aumentar%20la%20seguridad%2C%20evitando%20hurtos.%0D%0A-%20Facilitar%20toda%20la%20gesti%C3%B3n%20operativa%20a%20trav%C3%A9s%20del%20an%C3%A1lisis%20de%20datos.%0D%0A%0D%0AQuedo%20pendiente%20a%20su%20respuesta.%0D%0AMuchas%20gracias.%0D%0A%0D%0A' target='_blank'>{$this->colaborador->email}</a>
                        </div>
                    </div>
                    <div class='item-datos d-flex align-items-center'>
                        <div class='text-center'><i class='icono fab fa-whatsapp'></i></div>
                        <div class='flex-fill text-center'>
                            <a href='https://api.whatsapp.com/send?phone=57{$phone}&text=Hola%20{$this->colaborador->first_name}%2C%20me%20gustar%C3%ADa%20conocer%20m%C3%A1s%20de%20Satrack%20para%20mi%20negocio.' target='_blank'>+57 {$this->colaborador->phone}</a>
                        </div>
                    </div>
                    <div class='item-datos d-flex align-items-center'>
                        <div class='text-center'><i class='icono fas fa-globe'></i></div>
                        <div class='flex-fill text-center'><a href='{$this->colaborador->weburl}' target='_blank'>{$web}</a></div>
                    </div>
                    <div class='card-qr mt-4 d-flex justify-content-center align-items-center'>
                    <div class='aviso'>Para agregar<br>a tus contactos<br><i class='fas fa-camera'></i> &nbsp; <i class='far fa-hand-point-up'></i></div>
                        <a href='{$this->colaborador->url_vcard}'><img src='{$this->colaborador->url_qr}?V=2.1' /></a>
                    </div></div>
                
                <div class='card-footer d-flex flex-column align-items-center'>
                <div class='redes d-flex justify-content-center p-2'>
                <a href='https://www.instagram.com/satrack.oficial/?hl=es' target='_blank'><i class='icono2 fab fa-instagram mx-3'></i></a>
                <a href='https://www.facebook.com/satrack' target='_blank'><i class='icono2 fab fa-facebook-f mx-3'></i></a>
                <a href='https://co.linkedin.com/company/satracksas' target='_blank'><i class='icono2 fab fa-linkedin-in mx-3'></i></a>
                </div><div class='logo_satrack'><img  src='" .  $log . "' class='logo_satrack' /></div></div></div>";
        }
        return $contenido;
    }

    private function get_colaborador()
    {
        return $this->colaborador;
    }

    private function load_colaborador($id_post)
    {
        $use = get_post_meta($id_post, 'field_id_user', true);

        $args = array(
            'search'         => $use['id'],
            'search_columns' => array('ID'),
            //'fields' => ['ID', 'display_name', 'user_email'],
        );
        $consulta = new WP_User_Query($args);

        foreach ($consulta->results as $user) {
            $this->colaborador = (object) array(
                "id" => $user->ID,
                "name" => $user->display_name,
                "email" => $user->user_email,
                "weburl" => $user->user_url
            );
        }

        $this->colaborador->phone = get_user_meta($this->colaborador->id, 'user_phone', true);
        $this->colaborador->tele = get_user_meta($this->colaborador->id, 'user_tele', true);
        $this->colaborador->calendly = get_user_meta($this->colaborador->id, 'user_calendly', true);
        $this->colaborador->address = get_user_meta($this->colaborador->id, 'user_dir', true);
        $this->colaborador->title = get_user_meta($this->colaborador->id, 'user_cargo', true);
        $this->colaborador->pais = get_user_meta($this->colaborador->id, 'user_pais', true);
        $this->colaborador->url_qr = get_user_meta($this->colaborador->id, 'user_url_qr', true);
        $this->colaborador->url_vcard = get_user_meta($this->colaborador->id, 'user_url_vcard', true);

        $this->colaborador->first_name = get_user_meta($this->colaborador->id, 'user_firstname', true);
        $this->colaborador->last_name = get_user_meta($this->colaborador->id, 'user_lastname', true);
    }
}
