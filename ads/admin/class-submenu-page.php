<?php
/** 
* Creates the submenu page for the plugin. 
* 
* @package Custom_Admin_Settings 
*/
/** 
* Creates the submenu page for the plugin. 
* 
* Provides the functionality necessary for rendering the page corresponding 
* to the submenu with which this page is associated. 
* 
* @package Custom_Admin_Settings 
*/
class Submenu_Page {
        /** 
* This function renders the contents of the page associated with the Submenu 
* that invokes the render method. In the context of this plugin, this is the 
* Submenu class. 
*/
	public function render() {
		$table = new My_Table();
		
		$datos = $this->get_all_days();
		
		echo "<div class='wrap'><h2>Días Festivos para Wolkvox</h2><br><a href='admin.php?page=dias_festivos&action=nueva_fecha' class='page-title-action' >Nueva fecha</a><br>";
		echo '<ul class="subsubsub"><li class="all"><a href="admin.php?page=dias_festivos" class="current" aria-current="page">Todos</a> |&nbsp;</li><li class="papelera"><a href="options-general.php?page=dias_festivos&estado=delete"> Papelera </a></li></ul>';
	  	echo '<form method="post">';
      	// Prepare table
		(isset($_GET['estado']) && $_GET['estado']==="delete") ? $table->prepare_items(true):$table->prepare_items();
      	// Search form
      	$table->search_box('search', 'search_id');
      	// Display table
      	$table->display();
      	echo '</form></div>';
	}
	
	public function new_fecha() {
		if(isset($_POST['fecha']) && !empty($_POST['fecha'])){
			$this->guardar_fecha();
		}
		
		echo "<div class='wrap'><h2>Días Festivos para Wolkvox</h2><br><a href='".get_admin_url()."admin.php?page=dias_festivos' class='page-title-action' >Regresar</a>";
	  	echo '<form method="post">';
		wp_nonce_field( 'dias_festivos&action=nueva_fecha', 'new_holiday' );
		echo '<table class="form-table" role="presentation"><tbody><tr>';
		echo '<th scope="row"><label for="fecha">Fecha del día festivo</label></th><td><input type="date" name="fecha" id="fecha" placeholder="Ingrese la fecha" required></td>';
		echo '</tr><tr>';
		echo '<th scope="row"><label for="descripcion">Descripción</label></th><td><input type="text" name="descripcion" id="descripcion" placeholder="¿Qué dia es?" class="regular-text"><p class="description" id="tagline-description">En pocas palabras, explica el dia colocado.</p></td>';
		echo '</tr><tr>';
		echo '<th scope="row"><label for="recurrente">Es recurrente</label></th><td>SI <input type="radio" name="recurrente" id="recurrente" value="1"> &nbsp;&nbsp;NO <input type="radio" name="recurrente" checked id="recurrente" value="0"><p class="description" id="tagline-description">Esta fecha se repite cada año</p></td>';
		
		echo '</tr></tbody></table>';
		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar cambios"></p>';
      	
      	echo '</form></div>';
	}
	
	public function edit_fecha() {
		if(isset($_POST['fecha']) && !empty($_POST['fecha'])){
			$this->actualizar_fecha($_GET['element']);
		}
		
		$datos = array();
		if(isset($_GET['element']) && !empty($_GET['element'])){
			$id = $_GET['element'];
			$datos = $this->get_day_id($id);
		} else {
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=dias_festivos';</script>";
			die;
		}
		
		echo "<div class='wrap'><h2>Días Festivos para Wolkvox</h2><br><a href='".get_admin_url()."admin.php?page=dias_festivos' class='page-title-action' >Regresar</a>";
	  	echo '<form method="post">';
		wp_nonce_field( 'dias_festivos&action=nueva_fecha', 'new_holiday' );
		echo '<table class="form-table" role="presentation"><tbody><tr>';
		echo '<th scope="row"><label for="fecha">Fecha del día festivo</label></th><td><input type="date" name="fecha" id="fecha" value="'. $datos->fecha . '" placeholder="Ingrese la fecha" required></td>';
		echo '</tr><tr>';
		echo '<th scope="row"><label for="descripcion">Descripción</label></th><td><input type="text" name="descripcion" id="descripcion" value="' . $datos->descripcion . '" placeholder="¿Qué dia es?" class="regular-text"><p class="description" id="tagline-description">En pocas palabras, explica el dia colocado.</p></td>';
		echo '</tr><tr>';
		echo '<th scope="row"><label for="recurrente">Es recurrente</label></th><td>SI <input type="radio" name="recurrente" id="recurrente" ' . ($datos->recurrente ? 'checked':'') . ' value="1"> &nbsp;&nbsp;NO <input type="radio" name="recurrente" ' . ($datos->recurrente ? '':'checked') . ' id="recurrente" value="0"><p class="description" id="tagline-description">Esta fecha se repite cada año</p></td>';
		
		echo '</tr></tbody></table>';
		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar cambios"></p>';
      	
      	echo '</form></div>';
	}
	
	
	private function get_all_days(){
		global $wpdb;
		$datos = null;
		$sql = "SELECT * FROM {$wpdb->prefix}dias_festivos WHERE status = 1";
		
		$results = $wpdb->get_results( $sql, OBJECT );
		
		return $results;
	}
	
	private function get_day_id($id){
		global $wpdb;
		$datos = null;
		$sql = "SELECT * FROM {$wpdb->prefix}dias_festivos WHERE id = {$id}";
		
		$results = $wpdb->get_row( $sql, OBJECT );
		
		return $results;
	}
	
	private function guardar_fecha(){
		$nonce = $_POST['new_holiday'];
		
		if(!wp_verify_nonce($nonce, 'dias_festivos&action=nueva_fecha')){
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=dias_festivos&action=nueva_fecha';</script>";
			die;
		}
		global $wpdb;
		$fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

		$fecha_actual =$fecha_actual->format("Y-m-d H:i:s");
		
		$datos = array();
		$datos['fecha'] = sanitize_text_field($_POST['fecha']);
		$datos['descripcion'] = sanitize_text_field($_POST['descripcion']);
		$datos['id_user'] = sanitize_user(get_current_user_id());
		$datos['recurrente'] = $_POST['recurrente'];
		
		$wpdb->query( $wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}dias_festivos (fecha, descripcion, id_user, recurrente, date_updated) VALUES (%s, %s, %d, %d, %s)",
				$datos['fecha'], $datos['descripcion'], $datos['id_user'], $datos['recurrente'], $fecha_actual	) );
		
		echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=dias_festivos';</script>";
		die;
	}
	
	private function actualizar_fecha($id){
		$nonce = $_POST['new_holiday'];
		
		if(!wp_verify_nonce($nonce, 'dias_festivos&action=nueva_fecha')){
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=dias_festivos&action=nueva_fecha';</script>";
			die;
		}
		global $wpdb;
		$fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

		$fecha_actual =$fecha_actual->format("Y-m-d H:i:s");
		
		$datos = array();
		$datos['fecha'] = sanitize_text_field($_POST['fecha']);
		$datos['descripcion'] = sanitize_text_field($_POST['descripcion']);
		$datos['id_user'] = sanitize_user(get_current_user_id());
		$datos['recurrente'] = $_POST['recurrente'];
		
		$wpdb->query( $wpdb->prepare(
				"UPDATE {$wpdb->prefix}dias_festivos  SET fecha=%s, descripcion=%s, id_user=%d, recurrente=%d, date_updated=%s WHERE id=%d",
				$datos['fecha'], $datos['descripcion'], $datos['id_user'], $datos['recurrente'], $fecha_actual, $id ));
		
		echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=dias_festivos';</script>";
		die;
	}

}