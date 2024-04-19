<?php
if (!defined('ABSPATH')) die;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class CW_My_Table extends WP_List_Table
{
    // define $table_data property
    private $table_data;

    /** Class constructor */
    public function __construct()
    {
        parent::__construct([
            'singular' => 'Parametro', //singular name of the listed records
            'plural' => 'Parametros', //plural name of the listed records
            'ajax' => true //should this table support ajax?

        ]);
    }

    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'nombre_opcion'          => 'Nombre',
            'tipo_opcion'         => 'Tipo',
            'paises'        => 'Paises',
            'date_updated'        => 'Fecha actualización',
            'status'        => 'Estado'
        );
        return $columns;
    }


    // Bind table with columns, data and all
    // Bind table with columns, data and all
    function prepare_items($del = false)
    {
        //data
        $tipo = (isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : 'all');
        $this->process_action();

        if (isset($_POST['s'])) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } elseif ($del) {
            $this->table_data = $this->get_table_data("", true);
        } elseif (!empty($_GET['tipo'])) {
            $this->table_data = $this->get_table_data_by_type($tipo);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'nombre_opcion';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        /* pagination */
        $per_page = 50;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
            'total_pages' => ceil($total_items / $per_page) // use ceil to round up
        ));

        usort($this->table_data, array(&$this, 'usort_reorder'));
        $this->items = $this->table_data;
    }

    // Get table data
    private function get_table_data($search = '', $del = false)
    {
        $db_config = new DB_Config();
        $db_config->set_id_pais('all');

        if (!empty($search)) {
            return $db_config->get_datos($search, true);
            /*return $wpdb->get_results(
                "SELECT * from {$table} WHERE status > 0 AND ( nombre Like '%{$search}%' OR descripcion Like '%{$search}%')",
                ARRAY_A
            );*/
        } elseif ($del) {
            return $db_config->get_datos(null, true, true);
            /*return $wpdb->get_results(
                "SELECT * from {$table} WHERE status = 0",
                ARRAY_A
            );*/
        } else {
            return $db_config->get_datos(null, true);
            /*return $wpdb->get_results(
                "SELECT * from {$table} WHERE status>0",
                ARRAY_A
            );*/
        }
    }

    private function get_table_data_by_type($tipo)
    {
        $db_config = new DB_Config();
        $db_config->set_id_pais('all');

        return $db_config->get_datos_by_type($tipo, true);
    }

    function column_default($item, $column_name)
    {
        ///$areas = array(1 => "UEN E", 2 => "UEN T", 3 => "Club de Amigos");
        $estados = array(0 => "Eliminado", 1 => "Activo", 2 => "Terminado");
        switch ($column_name) {
            case 'nombre_opcion':
                $item[$column_name] = '<strong>' . $item[$column_name] . '</strong>';
                return $this->column_name($item);
                break;
            case 'tipo':
                return $item[$column_name];
                break;
            case 'paises':
                return $item[$column_name];
                break;
            case 'date_updated':
                return $item[$column_name];
                break;
            case 'status':
                return $estados[$item[$column_name]];
                break;
            default:
                return $item[$column_name];
                break;
        }
    }

    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="element[]" value="%s" />',
            $item['id_opcion']
        );
    }

    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'nombre_opcion'  => array('nombre_opcion', false),
            'tipo_opcion' => array('tipo_opcion', true),
            'paises' => array('paises', false),
            'date_updated'   => array('date_updated', false)
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'nombre_opcion';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Adding action links to column
    function column_name($item)
    {
        if (isset($_GET['estado']) && $_GET['estado'] === "delete") {
            $actions = array(
                'delete'    => sprintf('<a href="?page=%s&tab=%s&estado=delete&action=%s&id_opcion=%s&nombre_opcion=%s">' . 'Quitar de la papelera' . '</a>', $_REQUEST['page'], $_REQUEST['tab'], 'delete', $item['id_opcion'], $item['nombre_opcion']),
                'rest'    => sprintf('<a href="?page=%s&tab=%s&estado=delete&action=%s&id_opcion=%s&nombre_opcion=%s">' . 'Restaurar' . '</a>', $_REQUEST['page'], $_REQUEST['tab'], 'rest', $item['id_opcion'], $item['nombre_opcion'])
            );
        } else {
            $actions = array(
                'quitar'    => sprintf('<a href="?page=%s&tab=%s&action=%s&id_opcion=%s&nombre_opcion=%s">' . 'Eliminar' . '</a>', $_REQUEST['page'], $_REQUEST['tab'], 'quitar_opcion', $item['id_opcion'], $item['nombre_opcion']),
                'edit'    => sprintf('<a href="?page=%s&tab=%s&action=%s&id_opcion=%s&nombre_opcion=%s">' . 'Editar' . '</a>', $_REQUEST['page'], $_REQUEST['tab'], 'editar_opcion', $item['id_opcion'], $item['nombre_opcion'])
            );
        }

        return sprintf('%1$s %2$s', $item['nombre_opcion'], $this->row_actions($actions));
    }


    public function process_action()
    {
        $db_config = new DB_Config();

        $request_ids["id_opcion"] = isset($_REQUEST['id_opcion']) ? wp_unslash($_REQUEST['id_opcion']) : "";
        $request_ids["nombre_opcion"] = isset($_REQUEST['nombre_opcion']) ? wp_unslash($_REQUEST['nombre_opcion']) : "";

        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {

            //$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($_POST['_wpnonce'], $action))
                wp_die('Nope! Security check failed!');
        }

        $action = $this->current_action();

        switch ($action) {

            case 'quitar_opcion':
                $db_config->set_status_opcion($request_ids['id_opcion'], 0);
                //$this->quitar_proceso($request_ids);
                break;

            case 'editar_opcion':
                //wp_redirect(admin_url('/admin.php?page=hojas_servicio&action=nuevo_proceso', 'http'), 301);
                break;
            case 'delete':
                $db_config->delete_opcion($request_ids['id_opcion']);
                break;
            case 'rest':
                $db_config->set_status_opcion($request_ids['id_opcion'], 1);
                break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;
    }

    function get_views()
    {
        $db_config = new DB_Config();

        $views = array();
        if (!empty($_REQUEST['tipo'])) {
            $current = $_REQUEST['tipo'];
        } elseif (!empty($_REQUEST['estado'])) {
            $current = $_REQUEST['estado'];
        } else {
            $current = 'all';
        }

        //All link
        $class = ($current == 'all' ? ' class="current"' : '');
        $all_url = remove_query_arg(['tipo', 'action', 'id_opcion', 'nombre_opcion', 'estado']);
        $views['all'] = "<a href='{$all_url}' {$class} >Todos <span class=\"count\">(" . number_format($db_config->get_number_actions_tbl()) . ")</span></a>";

        //Equipos link
        $equipo_url = add_query_arg('tipo', 'equipo');
        $class = ($current == 'equipo' ? ' class="current"' : '');
        $views['equipo'] = "<a href='{$equipo_url}' {$class} >Equipos <span class=\"count\">(" . number_format($db_config->get_number_actions_tbl('equipo')) . ")</span></a>";

        //Accesorios link
        $acc_url = add_query_arg('tipo', 'accesorio');
        $class = ($current == 'accesorio' ? ' class="current"' : '');
        $views['accesorio'] = "<a href='{$acc_url}' {$class} >Accesorios <span class=\"count\">(" . number_format($db_config->get_number_actions_tbl('accesorio')) . ")</span></a>";

        //Planes link
        $plan_url = add_query_arg('tipo', 'plan');
        $class = ($current == 'plan' ? ' class="current"' : '');
        $views['plan'] = "<a href='{$plan_url}' {$class} >Planes <span class=\"count\">(" . number_format($db_config->get_number_actions_tbl('plan')) . ")</span></a>";

        //Planes instalacion link
        $inst_url = add_query_arg('tipo', 'instalacion');
        $class = ($current == 'instalacion' ? ' class="current"' : '');
        $views['instalacion'] = "<a href='{$inst_url}' {$class} >Planes de instalación <span class=\"count\">(" . number_format($db_config->get_number_actions_tbl('instalacion')) . ")</span></a>";

        //Planes link
        $trash_url = remove_query_arg(['tipo']);
        $trash_url = add_query_arg('estado', 'delete');
        $class = ($current == 'delete' ? ' class="current"' : '');
        $views['trash'] = "<a href='{$trash_url}' {$class} >Papelera <span class=\"count\">(" . number_format($db_config->get_number_actions_tbl('trash')) . ")</span></a>";

        return $views;
    }
}
