<?php
if (!defined('ABSPATH')) die;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class HS_My_Table extends WP_List_Table
{
    // define $table_data property
    private $table_data;

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => 'Proceso', //singular name of the listed records
            'plural' => 'Procesos', //plural name of the listed records
            'ajax' => false //should this table support ajax?

        ]);
    }

    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'nombre'          => 'Nombre',
            'descripcion'         => 'Descripción',
            'area'        => 'Área',
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
        $this->process_action();

        if (isset($_POST['s'])) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } elseif ($del) {
            $this->table_data = $this->get_table_data("", true);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'nombre';
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

        $this->items = $this->table_data;
    }

    // Get table data
    private function get_table_data($search = '', $del = false)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'hojas_servicio';

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE status > 0 AND ( nombre Like '%{$search}%' OR descripcion Like '%{$search}%')",
                ARRAY_A
            );
        } elseif ($del) {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE status = 0",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE status>0",
                ARRAY_A
            );
        }
    }

    function column_default($item, $column_name)
    {
        $areas = array(1 => "UEN E", 2 => "UEN T", 3 => "Club de Amigos");
        $estados = array(0 => "Eliminado", 1 => "Activo", 2 => "Terminado");

        switch ($column_name) {
            case 'nombre':
                $item[$column_name] = '<strong>' . $item[$column_name] . '</strong>';
                return $this->column_name($item);
                break;
            case 'descripcion':
                return $item[$column_name];
                break;
            case 'area':
                return $areas[$item[$column_name]];
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
            $item['id']
        );
    }

    protected function get_sortable_columns()
    {
        $sortable_columns = array(
            'nombre'  => array('nombre', true),
            'descripcion' => array('descripcion', false),
            'area' => array('area', true),
            'date_updated'   => array('date_updated', true),
            'estado'   => array('estado', true)
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'nombre';

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
                'delete'    => sprintf('<a href="?page=%s&estado=delete&action=%s&id=%s&nombre=%s">' . 'Quitar de la papelera' . '</a>', $_REQUEST['page'], 'delete', $item['id'], $item['nombre']),
                'rest'    => sprintf('<a href="?page=%s&estado=delete&action=%s&id=%s&nombre=%s">' . 'Restaurar' . '</a>', $_REQUEST['page'], 'rest', $item['id'], $item['nombre'])
            );
        } else {
            $actions = array(
                'quitar'    => sprintf('<a href="?page=%s&action=%s&id=%s&nombre=%s">' . 'Eliminar' . '</a>', $_REQUEST['page'], 'quitar', $item['id'], $item['nombre']),
                'edit'    => sprintf('<a href="?page=%s&action=%s&element=%s&nombre=%s">' . 'Editar' . '</a>', $_REQUEST['page'], 'edit', $item['id'], $item['nombre'])
            );
        }

        return sprintf('%1$s %2$s', $item['nombre'], $this->row_actions($actions));
    }

    public function process_action()
    {
        $request_ids["id"] = isset($_REQUEST['id']) ? wp_unslash($_REQUEST['id']) : "";
        $request_ids["nombre"] = isset($_REQUEST['nombre']) ? wp_unslash($_REQUEST['nombre']) : "";

        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {

            //$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($_POST['_wpnonce'], $action))
                wp_die('Nope! Security check failed!');
        }

        $action = $this->current_action();

        switch ($action) {

            case 'quitar':
                $this->quitar_proceso($request_ids);
                break;

            case 'edit':
                //wp_redirect(admin_url('/admin.php?page=hojas_servicio&action=nuevo_proceso', 'http'), 301);
                break;
            case 'delete':
                $this->del_proceso($request_ids);
                break;
            case 'rest':
                $this->rest_proceso($request_ids);
                break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;
    }


    private function quitar_proceso($item)
    {
        global $wpdb;
        $fecha_actual = new DateTime('', new DateTimeZone('	America/Bogota'));

        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $table = $wpdb->prefix . 'hojas_servicio';
        $data = ['status' => 0, 'date_updated' => $fecha_actual];
        $where = ['id' => $item['id']];

        $wpdb->update($table, $data, $where);
    }

    private function rest_proceso($item)
    {
        global $wpdb;

        $fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

        $fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

        $table = $wpdb->prefix . 'hojas_servicio';
        $data = ['status' => 1, 'date_updated' => $fecha_actual];
        $where = ['id' => $item['id']];

        $wpdb->update($table, $data, $where);
    }

    private function del_proceso($item)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'hojas_servicio';
        $where = ['id' => $item['id']];

        $wpdb->delete($table, $where);
    }
}
