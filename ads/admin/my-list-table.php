<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class My_Table extends WP_List_Table { 
    // define $table_data property
    private $table_data;

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
        'singular' => 'Registro', //singular name of the listed records
        'plural' => 'Registros', //plural name of the listed records
        'ajax' => false //should this table support ajax?
        
        ] );
    }

    function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
                'fecha'          => 'Fecha',
                'descripcion'         => 'Descripción',
                'id_user'        =>'Usuario',
                'date_updated'        => 'Fecha modificación',
			'recurrente'        => 'Recurrente'
        );
        return $columns;
    }


   // Bind table with columns, data and all
   // Bind table with columns, data and all
   function prepare_items($del=false)
   {
       //data
       $this->process_action();

       if ( isset($_POST['s']) ) {
        $this->table_data = $this->get_table_data($_POST['s']);
        }
	   elseif($del) {
		   $this->table_data = $this->get_table_data("", true);
	   } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $primary  = 'name';
        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        /* pagination */
        $per_page = 25;
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

    $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

    $this->set_pagination_args(array(
            'total_items' => $total_items, // total number of items
            'per_page'    => $per_page, // items to show on a page
            'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
    ));
    
    $this->items = $this->table_data;
   }

    // Get table data
    private function get_table_data( $search = '', $del=false ) {
        global $wpdb;

        $table = $wpdb->prefix . 'dias_festivos';

        if ( !empty($search) ) {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE status = 1 AND ( fecha Like '%{$search}%' OR descripcion Like '%{$search}%')",
                ARRAY_A
            );
        } elseif($del){
			return $wpdb->get_results(
                "SELECT * from {$table} WHERE status = 0",
                ARRAY_A
            );
		} else {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE status=1",
                ARRAY_A
            );
        }
    }

    function column_default($item, $column_name)
    {
		$dias = array(1=>"Lunes", 2 => "Martes", 3 => "Miércoles", 4 => "Jueves", 5 => "Viernes", 6 => "Sábado", 7 => "Domingo");
		$meses = array(1=>"Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio", 7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre");
		$fecha = "";
          switch ($column_name) {
                case 'fecha':
				  $fecha .= "<strong>" . $dias[date("N", strtotime($item[$column_name]) )] . "</strong>, ";
				  $fecha .= $meses[date("n", strtotime($item[$column_name]) )] . " ";
				  $fecha .= date("j \d\\e Y", strtotime($item[$column_name]) );
				  $item[$column_name] = $fecha;
				  return $this->column_name($item); break;
                case 'descripcion': return $item[$column_name]; break;
                case 'id_user': return get_user_by('id', $item[$column_name])->display_name;break;
                case 'date_updated': return $item[$column_name];break;
			  case 'recurrente': return $item[$column_name] ? "SI": "NO"; break;
                default:
                    return $item[$column_name];break;
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
            'fecha'  => array('fecha', true),
            'descripcion' => array('descripcion', false),
            'id_user' => array('id_user', true),
            'date_updated'   => array('date_updated', true),
		    'recurrente'   => array('recurrente', true)
      );
      return $sortable_columns;
}

// Sorting function
function usort_reorder($a, $b)
{
    // If no sort, default to user_login
    $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'date_updated';

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
    if(isset($_GET['estado']) && $_GET['estado']==="delete"){
		$actions = array(
            'delete'    => sprintf('<a href="?page=%s&estado=delete&action=%s&id=%s&fecha=%s">' . 'Quitar de la papelera' . '</a>', $_REQUEST['page'], 'delete', $item['id'], $item['fecha']),
			'rest'    => sprintf('<a href="?page=%s&estado=delete&action=%s&id=%s&fecha=%s">' . 'Restaurar' . '</a>', $_REQUEST['page'], 'rest', $item['id'], $item['fecha'])
    	);
	} else {
		$actions = array(
            'quitar'    => sprintf('<a href="?page=%s&action=%s&id=%s&fecha=%s">' . 'Eliminar' . '</a>', $_REQUEST['page'], 'quitar', $item['id'], $item['fecha']),
			'edit'    => sprintf('<a href="?page=%s&action=%s&element=%s&fecha=%s">' . 'Editar' . '</a>', $_REQUEST['page'], 'edit', $item['id'], $item['fecha'])
    	);
	}

    return sprintf('%1$s %2$s', $item['fecha'], $this->row_actions($actions));
}

public function process_action() {
    $request_ids["id"] = isset( $_REQUEST['id'] ) ? wp_unslash( $_REQUEST['id'] ) : "";
    $request_ids["fecha"] = isset( $_REQUEST['fecha'] ) ? wp_unslash( $_REQUEST['fecha'] ) : "";
    
    // security check!
    if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

        //$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
        $action = 'bulk-' . $this->_args['plural'];

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], $action ) )
            wp_die( 'Nope! Security check failed!' );

    }

    $action = $this->current_action();

    switch ( $action ) {

        case 'quitar':
            $this->quitar_day( $request_ids );
            break;
			
		case 'edit':
            //wp_redirect(admin_url('/options-general.php?page=custom-admin-page&action=nueva_fecha', 'http'), 301);
            break;
		case 'delete':
			$this->del_day( $request_ids );
			break;
		case 'rest':
			$this->rest_day( $request_ids );
			break;

        default:
            // do nothing or something else
            return;
            break;
    }

    return;
}


	private function quitar_day( $item ) {
		global $wpdb;
		$fecha_actual = new DateTime('', new DateTimeZone('America/Guayaquil'));

		$fecha_actual =$fecha_actual->format("Y-m-d H:i:s");

		$table = $wpdb->prefix . 'dias_festivos';
		$data = [ 'status' => 0, 'date_updated' => $fecha_actual ];
		$where = [ 'id' => $item['id'] ];

		$wpdb->update( $table, $data, $where );
	}
	
	private function rest_day( $item ) {
		global $wpdb;
		
		$fecha_actual = new DateTime('', new DateTimeZone('America/Guayaquil'));

		$fecha_actual =$fecha_actual->format("Y-m-d H:i:s");

		$table = $wpdb->prefix . 'dias_festivos';
		$data = [ 'status' => 1, 'date_updated' => $fecha_actual ];
		$where = [ 'id' => $item['id'] ];

		$wpdb->update( $table, $data, $where );
	}
	
	private function del_day( $item ) {
		global $wpdb;

		$table = $wpdb->prefix . 'dias_festivos';
		$where = [ 'id' => $item['id'] ];

		$wpdb->delete( $table, $where );
	}

} 