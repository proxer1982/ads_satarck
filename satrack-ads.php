<?php

/**
 * Plugin Name: ADS Satrack
 * Plugin URI: https://www.satrack.com/
 * Description: Aplicativos para la plataforma de Satrack
 * Version: 1.0.5.2
 * Author: Juan carlos Zorro
 * Author URI: http://satrack.com/
 * Text Domain: satrack-ads
 **/

if (!defined('ABSPATH')) die;


define('MY_SATRACK_ADS_VERSION', '1.0.4.3');

require_once plugin_dir_path(__FILE__) . 'ads/ads_users.php';
include_once plugin_dir_path(__FILE__) . 'ads/data.php';


require_once(plugin_dir_path(__FILE__) . 'ads/anuncios.php');
require_once(plugin_dir_path(__FILE__) . 'ads/editoriales.php');
require_once(plugin_dir_path(__FILE__) . 'ads/dias_festivos.php');
require_once(plugin_dir_path(__FILE__) . 'ads/anuncios_login.php');
require_once(plugin_dir_path(__FILE__) . 'ads/hoja_servicio.php');
require_once(plugin_dir_path(__FILE__) . 'ads/cotizador/init.php');
require_once(plugin_dir_path(__FILE__) . 'ads/e_cards_satrack.php');

$ads_user = new ADS_Users();
$ads_data = new Data();

class Ads_Satrack
{
    public array $ads;
    public $anuncios;
    public $editoriales;
    public $dias_festivos;
    public $anuncios_login;
    public $hoja_servicio;
    public $cotizador_web;

    public $ecards_satrack;

    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'cn_set_default_options'));
        register_activation_hook(__FILE__, array($this, 'add_roles_on_plugin_activation'));
        add_action('admin_post_guardar_options_ads', array($this, 'cn_guardar_options_ads'));

        add_action('show_user_profile', array($this, 'add_custom_fields_to_users'));
        add_action('edit_user_profile', array($this, 'add_custom_fields_to_users'));
        add_action('user_new_form', array($this, 'add_custom_fields_to_users'));

        add_action('personal_options_update', array($this, 'save_user_fields'));
        add_action('edit_user_profile_update', array($this, 'save_user_fields'));
        add_action('edit_user_created_user', array($this, 'save_user_fields'));

        $d = get_option('options_ads_satrack');


        $this->ads = (is_array($d))  ? $d : array();
        unset($d);

        if (in_array("anuncios", $this->ads)) {
            $this->anuncios = new Anuncios();
        }

        if (in_array("editoriales", $this->ads)) {
            $this->editoriales = new Editoriales();
        }

        if (in_array("anuncios_login", $this->ads)) {
            $this->anuncios_login = new AnunciosLogin();
        }

        if (in_array("dias_festivos", $this->ads)) {
            $this->dias_festivos = new DiaFestivo();
        }

        if (in_array("hoja_servicio", $this->ads)) {
            $this->hoja_servicio = new HojaServicio();
        }

        if (in_array("cotizador_web", $this->ads)) {
            $this->cotizador_web = new CotizadorWeb();
        }

        if (in_array("ecards_satrack", $this->ads)) {
            $this->ecards_satrack = new ECards_Str();
        }

        add_action('admin_menu', array($this, 'register_sub_menu'));

        //Quitar las query strings de recursos estaticos

        //add_filter('style_loader_src', array($this, 'remove_css_js_ver'), 10, 2);
        //add_filter('script_loader_src', array($this, 'remove_css_js_ver'), 10, 2);

        add_filter('init', array($this, 'flushRules'));
        add_filter('query_vars', array($this, 'wp_insertMyRewriteQueryVars'));
        //add_filter('rewrite_rules_array', array($this, 'wp_insertMyRewriteRules'));
    }

    public function remove_css_js_ver($src)
    {
        if (strpos($src, '?ver=')) {
            $src = remove_query_arg('ver', $src);
        }
        return $src;
    }
    public function flushRules()
    {
        add_rewrite_rule('cotizador-web/([^/]+)/?$', 'index.php?pagename=cotizador-web&pais_coti=$matches[1]', 'top');
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    // Adding a new rule
    public function wp_insertMyRewriteRules($rules)
    {
        $newrules = array();
        $newrules['cotizador-web/([^/]+)/?$'] = 'index.php?pagename=cotizador-web&pais_coti=$matches[1]';
        $finalrules = $newrules + $rules;
        foreach ($finalrules as $clave => $rules) {
            //echo $clave . ' => ' . $rules . '<br>';
        }

        return $finalrules;
    }

    // Adding the var so that WP recognizes it
    public function wp_insertMyRewriteQueryVars($vars)
    {
        $vars[] = 'pais_coti';
        return $vars;
    }
    public function cn_set_default_options()
    {
        // Revisar si ya se habia activado antes
        if (get_option('options_ads_satrack') === false) {
            add_option('options_ads_satrack', array('anuncios', 'editoriales'));
        }
    }

    public function add_roles_on_plugin_activation()
    {
        add_role('seller_role', 'Agente Comercial', array('read' => true, 'level_0' => false));
    }

    public function register_sub_menu()
    {
        add_menu_page(
            'ADS Satrack',
            'ADS Satrack',
            'manage_options',
            'ads_satrack',
            array($this, "render"),
            "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyNy4zLjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDUxMiA1MTI7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik00OTUuNSwxODUuMWw1LjEtMTVjMS43LTQuNywzLjktMTEuMiw0LjctMTQuNGMxLjktNy42LDMuOC0xNS41LTAuNC0yMS41Yy0zLjEtMy45LTcuNy02LjMtMTIuNy02LjRsLTQuMS0wLjMNCgljLTIuNSwwLTUuNy0wLjMtOS4xLTAuN2wtOS44LTAuNmMtMC4zLTItMC45LTMuOS0yLTUuNkM0MjMuNSw1Mi4yLDM0OS4zLDkuNCwyNjguMyw1LjdjLTIuMiwwLTQuNSwwLTYuOC0wLjJzLTMuNCwwLTUuMSwwaC0xLjYNCglDMTE2LjUsNS42LDQuNSwxMTcuOSw0LjUsMjU2LjJjMC4xLDEzOC4zLDExMi4zLDI1MC40LDI1MC43LDI1MC4zYzguNywwLDE3LjMtMC41LDI2LTEuNGwwLDBoMS4zaDEuMg0KCWMxMzcuNS0xNSwyMzYuOS0xMzguNiwyMjEuOS0yNzYuMmMtMS40LTEyLjktMy44LTI1LjYtNy4yLTM4LjFDNDk3LjksMTg4LjgsNDk2LjksMTg2LjgsNDk1LjUsMTg1LjF6IE0yNy4yLDMxMy45DQoJQy00LjcsMTg3LjEsNzIuMSw1OC40LDE5OC44LDI2LjVjMTguNi00LjcsMzcuOC03LjEsNTctNy4yYzIuMywwLDQuNiwwLDcsMGg0LjljNzUuOCw0LjIsMTQ1LjEsNDQuNSwxODYuMSwxMDguNWwtMTA1LjMtMTIuMg0KCUMzNDguNCw2MS4xLDI5Ni4zLDU3LDI5Ni4zLDU3aC01NC45djIzMi44YzAsMC42LTAuMSwxLjItMC4zLDEuOHYwLjVjLTAuMiwwLjQtMC40LDAuOC0wLjcsMS4xbC0wLjIsMC4zYy0wLjQsMC40LTAuOCwwLjgtMS4zLDEuMQ0KCWgtMC4zbC0xLjMsMC42aC0wLjVjLTAuNiwwLjItMS4xLDAuNC0xLjcsMC41bDAsMGwtMS45LDAuM2gtMC41aC0xLjVoLTUuOGMtNTEuOS00LjktOTQuNC01MC41LTk0LjQtNzUuOXYtMjEuMw0KCWMwLjItMS40LTAuOC0yLjgtMi4yLTNjLTAuMywwLTAuNSwwLTAuOCwwYy0xLjUtMC4yLTIuOSwwLjktMy4xLDIuNGMwLDAuMywwLDAuNiwwLDAuOWMtMC40LDEwLjMtOS45LDMyLjktMjIuMiw0OC4xDQoJYy03LjgsOS40LTE2LjIsMTguMy0yNS4xLDI2LjdsMCwwbC01MC4zLDQwLjhMMjcuMiwzMTMuOXogTTMxOC41LDExNi41YzE3LjMsMTEuNyw1LjQsMzUuNS0xNC4zLDI4LjgNCglDMjg2LjksMTMzLjUsMjk4LjgsMTA5LjgsMzE4LjUsMTE2LjVMMzE4LjUsMTE2LjV6IE0yODEuMiwzNjYuOGMwLTIuNSwwLTQuOCwwLTcuMWMwLTAuNywwLTEuNSwwLTIuMmMwLTEuNSwwLTMuMSwwLjItNC41DQoJYzAuMi0xLjUsMC0xLjcsMC0yLjVjMC0wLjgsMC0yLjUsMC4zLTMuOGMwLjMtMS4yLDAuMi0xLjcsMC4zLTIuNXMwLjItMi4zLDAuNC0zLjRjMC4yLTEuMSwwLjItMS42LDAuNC0yLjQNCgljMC4yLTAuOCwwLjMtMi4xLDAuNS0zLjFjMC4yLTEsMC4zLTEuNSwwLjUtMi4zczAuNC0xLjksMC42LTIuOGMwLjItMC45LDAuNC0xLjUsMC42LTIuMmMwLjItMC43LDAuNS0xLjcsMC43LTIuNg0KCWMwLjItMC44LDAuNS0xLjQsMC43LTIuMWMwLjItMC43LDAuNi0xLjYsMC45LTIuNGMwLjMtMC43LDAuNS0xLjMsMC44LTEuOWMwLjItMC42LDAuNi0xLjUsMS0yLjJsMC45LTEuOGMwLjMtMC43LDAuNy0xLjMsMS4xLTINCglsMS0xLjdsMS4yLTEuOGwxLjEtMS41bDEuMy0xLjdsMS4yLTEuNGwxLjQtMS41bDEuMy0xLjJsMS41LTEuNGwxLjQtMS4ybDEuNi0xLjJsMS41LTFsMS43LTEuMWwxLjUtMC45bDEuOC0xbDEuNi0wLjhsMS45LTAuOQ0KCWwxLjctMC43bDIuMS0wLjdsMS44LTAuNmwyLjItMC43bDEuOC0wLjVsMi4zLTAuNmwxLjktMC41bDIuNC0wLjVsMi0wLjRsMi42LTAuNGwwLDBsMS45LTAuM2wyLjctMC4zbDItMC4ybDMtMC4zaDJsNS4xLTAuNA0KCWw1LjMtMC4yaDUuNmg1LjdoMTJoMTBoMy4xbDMtMC40bDMtMC40bDIuOS0wLjZsMi45LTAuNmwyLjctMC43bDIuOS0wLjhsMi41LTAuOWwyLjgtMWwyLjMtMWwyLjgtMS4zbDIuMS0xLjFsMi45LTEuNWwxLjgtMS4xDQoJbDIuOS0xLjhsMS42LTEuMWMxLTAuNywzLjktMi45LDQuMS0zLjJjMC4zLTAuNCwxLjktMS41LDIuOS0yLjNsMS4xLTFjMS0wLjksMi0xLjcsMi45LTIuN2w0LjUtNC41YzEtMS4xLDItMi4yLDMtMy4zDQoJYzAsMCwyLjUtMi45LDMuNC00LjFsMC4yLTAuM2MxMi4yLTE1LjYsMjEuOS0zMywyOC42LTUxLjZjMzIuNCwxMjYuNy00NC4xLDI1NS43LTE3MC44LDI4OGMtMTAuNSwyLjctMjEuMiw0LjYtMzEuOSw1LjkNCglMMjgxLjIsMzY2Ljh6IE00ODcuMywxNDEuM2wzLjMsMC4yYzEuOSwwLDIuNywwLjUsMi44LDAuNWMtMC4xLDMuNS0wLjYsNy0xLjcsMTAuM2MtMC41LDIuMS0yLjIsNy4yLTMuOSwxMi4xDQoJYy0zLjItOC4yLTYuOC0xNi4xLTEwLjktMjMuOWgwLjlDNDgxLjIsMTQwLjksNDg0LjcsMTQxLjEsNDg3LjMsMTQxLjNMNDg3LjMsMTQxLjN6IE0yOS41LDMyOC4zYzIuNC0wLjQsNC44LTEuNCw2LjctMi45TDgwLDI4OS44DQoJYzE2LjMsODQuNyw5MC40LDE0NS45LDE3Ni42LDE0NS45YzMuNiwwLDcuMiwwLDEwLjctMC40djU1LjhsMCwwYzAsMC4zLDAsMC43LDAsMWMtNC4xLDAuMi04LjMsMC4zLTEyLjcsMC4zDQoJQzE1Miw0OTIuNCw2MSw0MjYuMSwyOS41LDMyOC4zeiIvPg0KPC9zdmc+DQo=",
            5
        );

        if (in_array("anuncios", $this->ads)) {
            add_submenu_page(
                'ads_satrack',
                'Anuncios blog',
                'Anuncios blog',
                'manage_options',
                'edit.php?post_type=satrack-ads'
            );
        }

        if (in_array("editoriales", $this->ads)) {
            add_submenu_page(
                'ads_satrack',
                'E! Satarck',
                'E! Satrack',
                'manage_options',
                'edit.php?post_type=ed-satrack'
            );
        }

        if (in_array("anuncios_login", $this->ads)) {
            add_submenu_page(
                'ads_satrack',
                'Anuncio Login',
                'Anuncio Login',
                'manage_options',
                'edit.php?post_type=login_announcement'
            );
        }



        if (in_array("ecards_satrack", $this->ads)) {
            /*add_submenu_page(
        'ecard-satrack',
        'E Cards Satrack',
        'E Cards Satrack',
        'manage_options',
        'edit.php?post_type=ecard-satrack'
      );*/
            add_submenu_page(
                'ads_satrack',
                'Ecards',
                'Ecards',
                'manage_options',
                'edit.php?post_type=ecard-satrack'
            );
        }
    }

    public function render()
    {
        $datos = array(
            ["txt" => "Anuncios blog", "key" => "anuncios"],
            ["txt" => "E! Satrack", "key" => "editoriales"],
            ["txt" => "Anuncio login", "key" => "anuncios_login"],
            ["txt" => "Días festivos Wolkvox", "key" => "dias_festivos"],
            ["txt" => "Hojas de procesos", "key" => "hoja_servicio"],
            ["txt" => "Cotizador web", "key" => "cotizador_web"],
            ["txt" => "E cards", "key" => "ecards_satrack"]
        );
?>
        <div class="wrap">
            <h1>Panel de administración <strong>ADS Satrack</strong></h1>
            <form method="post" action="admin-post.php">
                <input type="hidden" name="action" value="guardar_options_ads" />
                <!-- mejorar la seguridad -->
                <?php wp_nonce_field('token_ga'); ?>
                <table class="form-table">
                    <?php foreach ($datos as $item) : ?>
                        <tr>
                            <th><label><?= $item['txt']; ?></label></th>
                            <td><input type="checkbox" name="ads[]" value="<?= $item['key']; ?>" <?php if (in_array($item['key'], $this->ads)) {
                                                                                                        echo "checked='checked'";
                                                                                                    } ?> /></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <br>
                <input type="submit" value="Guardar" class="button-primary" />
            </form>
        </div>
<?php
    }

    public function cn_guardar_options_ads()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Not allowed');
        }
        // Revisar el token que creamos antes
        check_admin_referer('token_ga');
        //Limpiar valor, para prevenir problemas de seguridad

        $ads = $_POST['ads'];
        // Guardar en la base de datos
        update_option('options_ads_satrack', $ads);
        // Regresamos a la pagina de ajustes
        wp_redirect(add_query_arg(
            'page',
            'ads_satrack',
            admin_url('admin.php')
        ));
        exit;
    }

    public function add_custom_fields_to_users($user)
    {
        global $ads_user;


        $user = $ads_user->get_metas_userid($user);

        if (!$user) {
            $user = (object) [
                'stockist' => '',
                'pais' => [],
                'cargo' => '',
                'ciudad' => '',
                'direccion' => '',
                'mobile' => '',
                'tele' => '',
                'calendly' => '',
                'email' => '',
                'address' => '',
            ];
        }
        echo $this->create_form_user($user);
    }

    public function create_form_user($user)
    {
        global $ads_data;

        $opciones = $ads_data->get_options_gr();

        $texto = "<h3>Campos para los comerciales</h3>
        <table class='form-table'>
            <tr>
                <th><label for='user_stockist'>Distribuidor</label></th>
                <td>
                    <select name='user_stockist' id='user_stockist' class='regular-text' value='{$user->stockist}'>
                        <option value='' selected>Seleccione uno</option>";

        foreach ($opciones->distri as $clave => $distri) {
            $texto .= "<option value='{$clave}'";
            if ($user->stockist == $clave) $texto .= 'selected';
            $texto .= ">{$distri['nombre']}</option>";
        }
        $texto .= "</select>
                </td>
            </tr>";
        if (isset($opciones->list_paises) && count($opciones->list_paises) > 0) {
            $texto .=   "<tr>
                <th><label for='user_pais'>Paises</label></th>
                <td>";


            foreach ($opciones->list_paises as $pais) {
                $texto .= "<input type='checkbox' name='user_pais[]' id='user_pais' value='{$pais['ind']}' class='regular-text'";
                if (in_array($pais['ind'], $user->pais)) $texto .= ' checked ';
                $texto .= "> {$pais['name']} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            }

            $texto .= "</td>
            </tr>";
        }
        $texto .= "<tr>
                <th><label for='user_cargo'>Cargo</label></th>
                <td><input type='text' name='user_cargo' id='user_cargo' class='regular-text' value='{$user->cargo}' /></td>
            </tr>
            <tr>
                <th><label for='user_phone'>Celular</label></th>
                <td><input type='text' name='user_phone' id='user_phone' class='regular-text' value='{$user->mobile}' /></td>
            </tr>
            <tr>
                <th><label for='user_tele'>Teléfono</label></th>
                <td><input type='text' name='user_tele' id='user_tele' class='regular-text' value='{$user->tele}' /></td>
            </tr>
            <tr>
                <th><label for='user_calendly'>URL calendly</label></th>
                <td><input type='url' name='user_calendly' id='user_calendly' class='regular-text' value='{$user->calendly}' /></td>
            </tr>
            <tr>
                <th><label for='user_dir'>Dirección personalizada</label></th>
                <td><small>\"Direccion, Ciudad, Departamento, Pais, Código postal\"</small><br><input type='text' name='user_dir' id='user_dir' class='regular-text' value='{$user->address}' /></td>
            </tr>
        </table>";

        return $texto;
    }

    public function save_user_fields($user_id)
    {
        update_user_meta($user_id, 'user_firstname', sanitize_text_field($_POST['first_name']));
        update_user_meta($user_id, 'user_lastname', sanitize_text_field($_POST['last_name']));

        if (isset($_POST['user_stockist'])) {
            update_user_meta($user_id, 'user_stockist', sanitize_text_field($_POST['user_stockist']));
        }
        $celular = "";
        if (isset($_POST['user_phone'])) {
            $celular = sanitize_text_field($_POST['user_phone']);
            update_user_meta($user_id, 'user_phone', $celular);
        }

        if (isset($_POST['user_tele'])) {
            update_user_meta($user_id, 'user_tele', sanitize_text_field($_POST['user_tele']));
        }

        if (isset($_POST['user_calendly'])) {
            update_user_meta($user_id, 'user_calendly', sanitize_text_field($_POST['user_calendly']));
        }

        $direcc = (isset($_POST['user_dir'])) ? $_POST['user_dir'] : "";
        if ($direcc != "") {
            update_user_meta($user_id, 'user_dir', sanitize_text_field($_POST['user_dir']));
        }

        if (isset($_POST['user_cargo'])) {
            update_user_meta($user_id, 'user_cargo', sanitize_text_field($_POST['user_cargo']));
        }

        if (isset($_POST['user_pais'])) {
            update_user_meta($user_id, 'user_pais', rest_sanitize_array($_POST['user_pais']));
        } else {
            update_user_meta($user_id, 'user_pais', []);
        }

        if (isset($_POST['url']) && !empty($_POST['url'])) {
            $website = $_POST['url'];
        } elseif (isset($_POST['user_weburl']) && !empty($_POST['user_weburl'])) {
            $website = $_POST['user_weburl'];
        } else {
            $website = "https://satrack.com";
        }


        $direccion = "Carrera 35A # 15B-35";
        $ciudad = "Medellín";
        $dept = "Antioquia";
        $zipcode = "050021";
        $pais = "Colombia";

        if (!empty($direcc)) {
            $temp = explode(",", $direcc);
            $direccion = trim($temp[0]);
            $ciudad = (isset($temp[1])) ? trim($temp[1]) : "";
            $dept = (isset($temp[2])) ? trim($temp[2]) : "";
            $zipcode = (isset($temp[4])) ? trim($temp[4]) : "";
            $pais = (isset($temp[3])) ? trim($temp[3]) : "";
        }

        $nombre = sanitize_text_field($_POST['first_name']);
        $apellido = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $cargo = sanitize_text_field($_POST['user_cargo']);

        $url_qr = $this->generar_qr_user($user_id, $nombre, $apellido, $email, $celular, $website, $direccion, $cargo, $ciudad, $dept, $zipcode, $pais);

        update_user_meta($user_id, 'user_url_qr', $url_qr['qr']);
        update_user_meta($user_id, 'user_url_vcard', $url_qr['vcard']);
        return;
    }


    public function get_pais()
    {
        if (empty($this->id_pais)) {
            $this->load_pais();
            return $this->id_pais;
        } else {
            return false;
        }
    }

    private function generar_qr_user($id, $nombre, $apellido, $email, $celular = "", $url = "https://satrack.com", $direccion = "", $cargo = "", $ciudad = "Medellín", $dept = "Antioquia", $zipcode = "050021", $pais = "Colombia", $empresa = "Satrack")
    {
        require_once(plugin_dir_path(__FILE__) . 'ads/lib/phpqrcode/qrlib.php');

        $url_save = ABSPATH . 'wp-content/uploads/qr_ads';
        $name_file = sha1($id) . ".png";

        if (!file_exists($url_save)) {
            mkdir($url_save, 0777, true);
        }

        $celular2 = str_replace(' ', '', $celular);

        $celular2 = str_replace("-", "", $celular2);

        $celular2 = str_replace("+", "", $celular2);
        if (strlen($celular) > 10) {
            $celular2 = substr($celular2, -11, 10);
        }

        $celular        = '057' . $celular;

        $codeContents  = "BEGIN:VCARD\n";
        $codeContents .= "VERSION:2.1\n";
        $codeContents .= "N:{$apellido};{$nombre};;\n";
        $codeContents .= "FN:{$nombre} {$apellido}\n";
        $codeContents .= "TEL;WORK:{$celular}\n";
        $codeContents .= "EMAIL;WORK:{$email}\n";
        $codeContents .= "ADR;WORK:;;{$direccion};{$ciudad};{$dept};{$zipcode};{$pais}\n";
        $codeContents .= "ORG:{$empresa}\n";
        $codeContents .= "TITLE:{$cargo}\n";
        $codeContents .=  "URL:{$url}\n";
        $codeContents .= "END:VCARD";

        // generating
        $qr = QRcode::png($codeContents, $url_save . "/" . $name_file, QR_ECLEVEL_L, 3);

        ob_start();
        header("Content-type: text/vcard");
        echo $codeContents;
        $content = ob_get_clean();
        $name_vcf = $url_save . "/" . strtolower($nombre)  . "_" . strtolower($apellido) . ".vcf";
        $url_vcf = home_url() . "/wp-content/uploads/qr_ads/" . strtolower($nombre)  . "_" . strtolower($apellido) . ".vcf";

        file_put_contents($name_vcf, $content);

        $datos = ['qr' => sanitize_url(get_home_url() . "/wp-content/uploads/qr_ads/{$name_file}"), 'vcard' => sanitize_url($url_vcf)];
        return $datos;
    }
}

$satrack = new Ads_Satrack();
