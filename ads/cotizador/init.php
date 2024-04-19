<?php

/**
 *  WpApiFeaturedImage
 *
 *  Adds featured images to the products endpoint
 *  using register_rest_field hook.
 *
 *  @version   1.0.2
 *  @author    Juan Zorro
 */

if (!defined('ABSPATH')) die;
include_once plugin_dir_path(__FILE__) . 'cw_render.php';
include_once plugin_dir_path(__FILE__) . 'class-cw-submenu.php';
require_once plugin_dir_path(__FILE__) . 'db/db_config.php';
include_once plugin_dir_path(__FILE__) . 'helper/helper_string.php';

define('URL_PLUGIN_CW', plugin_dir_url(__FILE__));

class CotizadorWeb
{
    private $options, $datos;

    function __construct()
    {
        add_action('wp_ajax_nopriv_save_ajax_cotizacion', array($this, 'save_ajax_cotizacion'));
        add_action('wp_ajax_save_ajax_cotizacion', array($this, 'save_ajax_cotizacion'));

        add_action('wp_enqueue_scripts', array($this, 'satrack_scripts_front'));

        add_action('parse_query', array($this, 'add_shortcodes'));

        if (isset($_GET['action']) && $_GET['action'] === 'pdf_cotizacion' && !empty($_GET['id_cot']) && is_numeric($_GET['id_cot'])) {
            require_once plugin_dir_path(__FILE__) . 'builder_pdf.php';

            global $ads_data;
            $datos_cot = $ads_data->get_cotizacion_id($_GET['id_cot']);

            $builderPDF = new BuilderPDF($datos_cot);
            $builderPDF->generar_pdf();
            die;
        }

        if (is_admin()) {
            add_action('plugins_loaded', array($this, 'satrack_cotizador_settings'));
        }

        if (isset($_GET['action']) && $_GET['action'] == 'save_cotizacion') {
            $this->save_cotizacion();
        }

        /*
        add_action('rest_api_init', function () {
            register_rest_route('satrack-zapier', "/data_quoter/", array(
                'methods' => 'POST',
                'callback' => array($this, 'actualizacion_data'),
                'permission_callback' => array($this, 'zapierAuth')
            ));
        });*/


        add_action('wp_login_failed', array($this, 'my_front_end_login_fail'));

        add_action('wp_logout', array($this, 'my_logout_redirect'), PHP_INT_MAX);
    }
    //}


    public function add_shortcodes()
    {
        add_shortcode('formulario-cotizador', array($this, 'frontend_form_cotizador'));
    }


    public function satrack_cotizador_settings()
    {
        $plugin = new CW_Submenu(new CW_Render());
        $plugin->init();
    }

    /**
     * Summary of frontend_form_cotizador
     * @return bool|string
     */
    public function frontend_form_cotizador()
    {
        global $ads_user, $ads_data;
        $ads_user->load_user();

        $pais = $ads_data->get_pais();

        if ($ads_user->is_logged() || is_admin()) {
            $page = new CW_Render();

            return $page->render($pais);
        } else {
            $url_pais = "";
            if ($pais)  $url_pais = "/" . strtolower($pais) . "/";
            $args = array(
                'echo'            => false,
                'redirect'        => get_permalink(get_the_ID()) . $url_pais,
                'remember'        => true,
                'value_remember'  => true,
            );

            $page = new CW_Render();
            return $page->form_login();
        }
    }

    public function acciones_no_logeo()
    {
    }
    public function my_front_end_login_fail($username)
    {
        $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
        // if there's a valid referrer, and it's not the default log-in screen
        if (!empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) {
            wp_redirect($referrer . '?login=failed');  // let's append some information (login=failed) to the URL for the theme to use
            exit;
        }
    }

    public function my_logout_redirect()
    {
        $logouturl = esc_attr($_SERVER['HTTP_REFERER']);
        wp_redirect($logouturl);
        die;
    }

    public function save_ajax_cotizacion()
    {
        //$page = new CW_Submenu_Page($this->options);
        global $ads_data;

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            $ads_data->actualizar_cotizacion($id);
        } else {
            $ads_data->guardar_cotizacion();
        }
        wp_die();
    }

    public function save_ajax_estado_cotizacion()
    {
        //$page = new CW_Submenu_Page($this->options);
        global $ads_data;

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            $ads_data->actualizar_estado_cotizacion($id, $estado);
        } else {
            echo json_encode(["status" => "error"]);
        }
        wp_die();
    }


    /* public function actualizacion_data($data)
    {
        $datos = $data->get_params();

        $bd_config = new DB_Config();


        $bd_config->set_data_cotizador($datos);

        echo json_encode(["status" => "200", "data" => $datos]);
    }*/

    public function zapierAuth()
    {
        global $headers;

        $headers = apache_request_headers();

        if (!$headers['User'] || !$headers['Password']) {
            return new WP_Error('rest_api_error', 'There is not authorization token in headers.', array('status' => 402));
        }

        $username = $headers['User'];
        $password = $headers['Password'];

        $user = wp_authenticate($username, $password);

        /** If the authentication fails return a error*/
        if (is_wp_error($user)) {
            $error_code = $user->get_error_code();
            return new WP_Error(
                $error_code,
                $user->get_error_message($error_code),
                array(
                    'status' => 403,
                )
            );
        }

        return true;
    }

    /**
     * Summary of satrack_scripts_front
     * @return void
     */
    public function satrack_scripts_front()
    {
        global $wp_query;
        global $post;

        if (isset($wp_query->post->post_title) && $wp_query->post->post_title == "Cotizador Web" && is_user_logged_in()) {

            if (isset($_GET['action']) && $_GET['action'] == 'new_cotizacion') {
                wp_enqueue_editor();
                wp_register_script('cw_satrack-js', URL_PLUGIN_CW . 'assets/js/cw_satrack_js.js', array('jquery', 'editor'), '1.0.4.11', true);
                wp_enqueue_script('cw_satrack-js');
                wp_scripts()->add_data('cw_satrack-js', 'type', 'module');
            } else {
                wp_register_script('cw_satrack-js', URL_PLUGIN_CW . 'assets/js/cw_satrack_list_js.js?v=3.0.0', array('jquery'), '1.0.2.5');
                wp_enqueue_script('cw_satrack-js');

                wp_register_style('data-table', 'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css', array(), '1.13.6');
                wp_enqueue_style('data-table');
                wp_register_style('data-table-buttons', 'https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css', array(), '1.13.6');
                wp_enqueue_style('data-table-buttons');
                wp_register_style('data-table-datetime', 'https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css', array(), '1.5.6');
                wp_enqueue_style('data-table-datetime');


                wp_register_script('data-table', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array('jquery'), '1.13.6');
                wp_enqueue_script('data-table');
                wp_register_script('data-table-buttons', 'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js', null, '1.13.6');
                wp_enqueue_script('data-table-buttons');
                wp_register_script('data-table-btnhtml5', 'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js', null, '1.13.7');
                wp_enqueue_script('data-table-btnhtml5');
                wp_register_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', null, '1.13.6');
                wp_enqueue_script('jszip');


                wp_register_script('data-table-moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js', null, '1.13.6');
                wp_enqueue_script('data-table-moment');
                wp_register_script('data-table-datetime', 'https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js', null, '1.13.6');
                wp_enqueue_script('data-table-datetime');



                wp_localize_script('cw_satrack-js', 'datos_cw', ['NONCE' => wp_create_nonce('cotizador_web_satrack')]);
            }



            wp_localize_script('cw_satrack-js', 'dcms_vars', ['ajaxurl' => admin_url('admin-ajax.php')]);
        }

        if (has_shortcode($post->post_content, 'formulario-cotizador')) {
            wp_register_script('jquery', 'https://code.jquery.com/jquery-3.7.0.js', null, '3.7.0');
            wp_enqueue_script('jquery');

            wp_register_style('cw_satrack-css', URL_PLUGIN_CW . 'assets/css/style_cw.css', array(), '3.1.4.2');
            wp_enqueue_style('cw_satrack-css');
            wp_register_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css', array(), '5.2.3');
        }
    }
}
