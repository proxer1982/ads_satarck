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

foreach (glob(plugin_dir_path(__FILE__) . 'hs_str/*.php') as $file) {
    include_once $file;
}

class HojaServicio
{

    function __construct()
    {
        add_action('plugins_loaded', array($this, 'satrack_hojas_servicio_settings'));
        $this->insert_hs_table_into_db();

        add_action('admin_enqueue_scripts', array($this, 'satrack_scripts'));

        add_shortcode('formulario_ordenes', array($this, 'frontend_form_ordenes'));
    }

    public function satrack_scripts()
    {
        global $pagenow;

        if ($pagenow != 'hojas_servicio') {
            wp_register_script('hs_admin-js', plugin_dir_url(__FILE__) . 'assets/js/hs_admin_js.js', array('jquery'), '1.0.3.0');
            wp_enqueue_script('hs_admin-js');
            wp_register_script('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', array('jquery'), '1.13.2');
            wp_enqueue_script('jquery-ui');
        }
    }

    public function satrack_hojas_servicio_settings()
    {
        $plugin = new HS_Submenu(new HS_Submenu_Page());
        $plugin->init();
    }

    public function insert_hs_table_into_db()
    {
        global $wpdb;
        // set the default character set and collation for the table
        $charset_collate = $wpdb->get_charset_collate();
        // Check that the table does not already exist before continuing
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}hojas_servicio (
        id int(11) NOT NULL AUTO_INCREMENT,
        nombre varchar(155) NOT NULL,
		descripcion varchar(255) NOT NULL,
        fecha_inicio datetime DEFAULT NULL,
		fecha_fin datetime DEFAULT NULL,
        area tinyint(3) DEFAULT NULL,
        colaborador varchar(155) NOT NULL,
        paises varchar(155) DEFAULT NULL,
		recursos text NULL DEFAULT NULL,
		automatizaciones text NULL DEFAULT NULL,
		alm_datos text NULL DEFAULT NULL,
		observaciones varchar(255) NULL DEFAULT NULL,
        date_created timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        date_updated datetime DEFAULT CURRENT_TIMESTAMP,
		status tinyint(3) DEFAULT 1,
        user_wp int(11) DEFAULT NULL,
        PRIMARY KEY (id)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        $is_error = empty($wpdb->last_error);
        return $is_error;
    }

    public function frontend_form_ordenes()
    {
        wp_register_script('hs_satrack-js', plugin_dir_url(__FILE__) . 'assets/js/hs_satrack_js.js', array('jquery'), '1.0.4.0');
        wp_enqueue_script('hs_satrack-js');
        wp_register_script('data-table', 'https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js', array('jquery'), '1.13.4');
        wp_enqueue_script('data-table');
        wp_register_script('data-table-boot', 'https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js', array('jquery'), '1.13.4');
        wp_enqueue_script('data-table-boot');
        wp_register_style('data-table', 'https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css', array(), '1.13.4');
        wp_enqueue_style('data-table');
        wp_register_script('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', array('jquery'), '1.13.2');
        wp_enqueue_script('jquery-ui');

        $page = new HS_Submenu_Page();

        if (isset($_GET['action']) && !empty($_GET['action'])) {
            $cuerpo = '';

            switch ($_GET['action']) {
                case "new_proceso":
                    $cuerpo = $page->new_proceso_front();
                    break;
                case "edit_proceso":
                    $cuerpo = $page->edit_proceso_front();
                    break;
                case "view_proceso":
                    $cuerpo = $page->view_proceso_front();
                    break;
                default:
                    $cuerpo = $page->list_front();
                    break;
            }
        } else {
            $cuerpo = $page->list_front();
        }

        return $cuerpo;
    }
}
