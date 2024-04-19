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
if (!defined('ABSPATH')) die;
class HS_Submenu_Page
{
	/** 
	 * This function renders the contents of the page associated with the Submenu 
	 * that invokes the render method. In the context of this plugin, this is the 
	 * Submenu class. 
	 */
	private $form_render;
	private $dias;
	private $meses;
	private $meses_cortos;
	private $users;

	public function __construct()
	{
		$this->dias = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
		$this->meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$this->meses_cortos = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
		$this->form_render = new FormRender();

		if (get_option('users_ads_satrack') === false ||  get_option('users_ads_satrack') === "") {
			$this->users = array();
		} else {
			$this->users = get_option('users_ads_satrack');
		}
	}

	public function render()
	{
		$table = new HS_My_Table();

		//$datos = $this-> get_all_service();

		echo "<div class='wrap'><h2>Hojas de procesos Satrack</h2><br><a href='admin.php?page=hojas_servicio&action=new_proceso' class='page-title-action' >Nuevo proceso</a><a href='admin.php?page=hojas_servicio&action=users' class='page-title-action' >Usuarios</a><br>";
		echo '<ul class="subsubsub"><li class="all"><a href="admin.php?page=hojas_servicio" class="current" aria-current="page">Todos</a> |&nbsp;</li><li class="papelera"><a href="admin.php?page=hojas_servicio&estado=delete"> Papelera </a></li></ul>';
		echo '<form method="post">';
		// Prepare table
		(isset($_GET['estado']) && $_GET['estado'] === "delete") ? $table->prepare_items(true) : $table->prepare_items();
		// Search form
		$table->search_box('search', 'search_id');
		// Display table
		$table->display();
		echo '</form></div>';
	}

	public function usuarios()
	{
		if (isset($_POST['save']) && $_POST['save'] == 'true') {
			$this->guardar_user();
		}

		echo "<div class='wrap'><h2>Usuarios Satrack</h2><br>
		<a href='admin.php?page=hojas_servicio' class='page-title-action' >Todos los procesos</a>
		<a href='admin.php?page=hojas_servicio&action=users' class='page-title-action' >Todos los usuarios</a>
		<a href='admin.php?page=hojas_servicio&action=new_proceso' class='page-title-action' >Nuevo proceso</a><br>";
		echo '<form method="post">';
		wp_nonce_field('admin.php?page=hojas_servicio&action=users', 'new_user');
		echo "<input type='hidden' name='save' value='true'><table class='form-table' role='presentation'><tbody><tr>";
		echo "<th scope='row'><label for='name-user'>Nombre del usuario</label></th><td><input name='name-user' type='text' class='regular-text' required></td></tr>
		<tr><th scope='row'><label for='name-user'>Correo del usuario</label></th><td><input name='email-user' type='email' class='regular-text' required></td></tr><tr><td><input type='submit' value='Guardar' class='button button-primary'></td><td></td></tr></table>
		</form>";
		echo "<h3>Todos los usuarios</h3>";
		if (sizeof($this->users) > 0) {
			echo "<table class='wp-list-table widefat fixed striped table-view-list procesos'>
		<thead>
		<tr>
			<th scope='col' id='id-user' style='width:60px;' class='manage-column column-area sortable asc'><a href='https://www.satrack.com.co/wp-admin/admin.php?page=hojas_servicio&amp;orderby=area&amp;order=desc'><span>ID</span><span class='sorting-indicator'></span></a></th>
			<th scope='col' id='nombre' class='manage-column column-nombre column-primary sortable asc'><a href='https://www.satrack.com.co/wp-admin/admin.php?page=hojas_servicio&amp;orderby=nombre&amp;order=desc'><span>Nombre</span><span class='sorting-indicator'></span></a></th>
			<th scope='col' id='email-user' class='manage-column column-descripcion sortable desc'><a href='https://www.satrack.com.co/wp-admin/admin.php?page=hojas_servicio&amp;orderby=descripcion&amp;order=asc'><span>Email</span><span class='sorting-indicator'></span></a></th>
			</tr>
		</thead>";
			echo "<tbody id='the-list' data-wp-lists='list:proceso'>";

			foreach ($this->users as $user) {
				echo "<tr>
			<td class='descripcion column-descripcion' style='width:60px;' data-colname='ID'>{$user['id']}</td>
			<td class='nombre column-nombre has-row-actions column-primary' data-colname='Nombre'><strong>{$user['nombre']}</strong>
			<div class='row-actions'>
			<span class='edit'><a href='?page=hojas_servicio&amp;action=user_edit&amp;element={$user['id']}&amp;nombre={$user['nombre']}'>Editar</a></span></div><button type='button' class='toggle-row'><span class='screen-reader-text'>Mostrar más detalles</span></button><button type='button' class='toggle-row'><span class='screen-reader-text'>Mostrar más detalles</span></button></td>
			
			<td class='area column-area' data-colname='Email'>{$user['email']}</td></tr>";
			}
			echo "</tbody></table>";
		} else {
			echo "No hay registros";
		}
		echo "</div>";
	}

	public function edit_user()
	{
		if (isset($_POST['update']) && $_POST['update'] == 'true') {
			$this->update_user();
		}

		$key = $this->get_id_user($_GET['element']);

		echo "<div class='wrap'><h2>Editar Usuario Satrack</h2><br>
		<a href='admin.php?page=hojas_servicio' class='page-title-action' >Todos los procesos</a>
		<a href='admin.php?page=hojas_servicio&action=users' class='page-title-action' >Regresar</a><br>";
		echo '<form method="post">';
		wp_nonce_field('admin.php?page=hojas_servicio&action=users', 'update_user');
		echo "<input type='hidden' name='update' value='true'><table class='form-table' role='presentation'><tbody><tr>";
		echo "<th scope='row'><label for='name-user'>Nombre del usuario</label></th><td><input name='name-user' type='text' value='{$this->users[$key]['nombre']}' class='regular-text' required></td></tr>
		<tr><th scope='row'><label for='name-user'>Correo del usuario</label></th><td><input name='email-user' type='email' value='{$this->users[$key]['email']}' class='regular-text' required></td></tr><tr><td><input name='key' value='{$key}' type='hidden'><input type='submit' value='Guardar' class='button button-primary'></td><td></td></tr></table>
		</form>";
		echo "</div>";
	}

	public function new_proceso()
	{
		if (isset($_POST['action']) && $_POST['action'] === "new_proceso") {
			$this->guardar_proceso();
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio';</script>";
			die;
		}

		echo "<div class='wrap'><h2>Nuevo proceso Satarck</h2><br><a href='" . get_admin_url() . "admin.php?page=hojas_servicio' class='page-title-action' >Regresar</a>";
		$this->form_render->form_open('new_proceso');
		wp_nonce_field('admin.php?page=hojas_servicio&action=new_proceso', 'new_proceso');
		echo '<table class="form-table" role="presentation"><tbody><tr>';
		echo '<th scope="row"><label for="nombre">Nombre del proceso</label></th><td>';
		$this->form_render->input("text", "nombre", true, "", "Ingrese el nombre del proceso", "regular-text");
		echo '</td></tr><tr>';
		echo '<th scope="row"><label for="descripion">Descripción</label></th><td>';
		$this->form_render->input("text", "descripcion", false, "", "Descripción del proceso", "regular-text");
		echo '<p class="description" id="tagline-description">En pocas palabras, explica el proceso.</p></td>';
		echo '</tr><tr>';
		echo '<th scope="row"><label for="fecha_inicio">Fecha de inicio</label></th><td>';
		$this->form_render->input("date", "fecha_inicio", true);
		echo '</td></tr><tr>';
		echo '<th scope="row"><label for="fecha_fin">Fecha de finalización</label></th><td>';
		$this->form_render->input("date", "fecha_fin", false);
		echo "</td></tr><tr>";
		echo "<th scope='row'><label for='fecha_fin'>Área</label></th><td>";
		$this->form_render->select("areas", true);
		echo '</td></tr><tr><hr>';
		echo '<th scope="row"><label for="colaborador">Nombre del colaborador</label></th><td>';
		$this->form_render->select("colaboradores", true, null, true, 'colaborador');
		echo "</td></tr>
		<tr><th scope='row'><label for='colaborador'>Paises afectados</label></th><td>";
		echo $this->form_render->radio_paises(null, false) . '</td></tr>';
		echo "</tbody></table></br><br>";
		$this->form_render->multirecursos();
		echo '<br><br><hr>';
		$this->form_render->multiautomatiza();
		echo '<br><br><hr>';
		$this->form_render->multidatos();
		echo "<br><hr><label for='observaciones'>Observaciones</label><br><textarea style='width:100%;max-width:900px;' name='observaciones' id='observaciones'></textarea>";
		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar cambios"></p>';

		echo '</form></div>';
	}

	public function edit_proceso()
	{
		if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
			$this->actualizar_proceso($_GET['element']);
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio';</script>";
			die;
		}

		$datos = array();
		if (isset($_GET['element']) && !empty($_GET['element'])) {
			$id = $_GET['element'];
			$datos = $this->get_proceso_id($id);
		} else {
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio';</script>";
			die;
		}

		echo "<div class='wrap'><h2>Edición de procesos Satarck</h2><br><a href='" . get_admin_url() . "admin.php?page=hojas_servicio' class='page-title-action' >Regresar</a>";
		$this->form_render->form_open('edit_proceso');
		wp_nonce_field('admin.php?page=hojas_servicio&action=edit', 'edit_proceso');
		echo '<table class="form-table" role="presentation"><tbody><tr>';
		echo '<th scope="row"><label for="nombre">Nombre del proceso</label></th><td>';
		$this->form_render->input("text", "nombre", true, $datos->nombre, "Ingrese el nombre del proceso", "regular-text");
		echo '</td></tr><tr>';
		echo '<th scope="row"><label for="descripcion">Descripción</label></th><td>';
		$this->form_render->input("text", "descripcion", false, $datos->descripcion, "Descripción del proceso", "regular-text");
		echo '<p class="description" id="tagline-description">En pocas palabras, explica el proceso.</p></td>';
		echo '</tr><tr>';
		echo '<th scope="row"><label for="fecha_inicio">Fecha de inicio</label></th><td>';
		$this->form_render->input("date", "fecha_inicio", true, $datos->fecha_inicio);
		echo '</td></tr><tr>';
		echo '<th scope="row"><label for="fecha_fin">Fecha de finalización</label></th><td>';
		$this->form_render->input("date", "fecha_fin", false, $datos->fecha_fin);
		echo "</td></tr><tr>";
		echo "<th scope='row'><label for='fecha_fin'>Área</label></th><td>";
		$this->form_render->select("areas", true, $datos->area);
		echo "</td>";
		echo '</tr><tr>';
		echo "<th scope='row'><label for='colaborador'>Nombre del colaborador</label></th><td>";
		$this->form_render->select("colaboradores", true, $datos->colaborador, true, 'colaborador');
		echo "</td></tr>
		<tr><th scope='row'><label for='colaborador'>Paises afectados</label></th><td>";
		echo $this->form_render->radio_paises($datos->paises, false) . '</td></tr>';
		echo "</tbody></table></br><br>";

		$this->form_render->multirecursos($datos->recursos);
		echo '<br><br><hr>';
		$this->form_render->multiautomatiza($datos->automatizaciones);
		echo '<br><br><hr>';
		$this->form_render->multidatos($datos->alm_datos);
		echo "<br><br>";
		echo "<label for='observaciones'>Observaciones</label><br><textarea style='width:100%;max-width:900px;' name='observaciones' id='observaciones'>{$datos->observaciones}</textarea>";
		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar cambios"></p>';

		echo '</form></div>';

		echo "<script>
		  jQuery( function($) {
			$( '.sortable' ).sortable();
		  } );
		  </script>";
	}


	private function get_all_service($page)
	{
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}hojas_servicio WHERE status > 0";


		$results = $wpdb->get_results($sql, OBJECT);

		return $results;
	}

	private function get_proceso_id($id)
	{
		global $wpdb;
		$datos = null;
		$sql = "SELECT * FROM {$wpdb->prefix}hojas_servicio WHERE id = " . $id;

		$results = $wpdb->get_row($sql, OBJECT);
		$results->paises = json_decode($results->paises);
		$results->recursos = json_decode($results->recursos);
		$results->automatizaciones = json_decode($results->automatizaciones);
		$results->alm_datos = json_decode($results->alm_datos);
		return $results;
	}


	private function guardar_proceso()
	{
		$nonce = $_POST['new_proceso'];

		if (!wp_verify_nonce($nonce, 'admin.php?page=hojas_servicio&action=new_proceso')) {
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio&action=new_proceso&msj=dallo_envio;</script>";
			die;
		}
		global $wpdb;

		$fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

		$fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

		$datos = array();
		$datos['nombre'] = sanitize_text_field($_POST['nombre']);
		$datos['descripcion'] = sanitize_text_field($_POST['descripcion']);

		$datos['fecha_inicio'] = date("Y-m-d H:i:s", strtotime($_POST['fecha_inicio']));
		if (!empty($_POST['fecha_fin'])) {
			$datos['fecha_fin'] = date("Y-m-d H:i:s", strtotime($_POST['fecha_fin']));
		} else {
			$datos['fecha_fin'] = 0;
		}

		$datos['area'] =  $_POST['areas'][0];
		$datos['paises'] =  json_encode($_POST['paises']);
		$datos['colaborador'] =  $_POST['colaborador'];
		$datos['recursos'] = $this->form_render->datos_json(['tipo_rec', 'URL', 'resp_recurso']);
		$datos['automatizaciones'] =  $this->form_render->datos_json(['nombre_auto', 'app_auto', 'url_auto', 'acciones_auto']);
		$datos['alm_datos'] =  $this->form_render->datos_json(['pl_datos', 'URL_datos']);
		$datos['observaciones'] =  sanitize_text_field($_POST['observaciones']);

		$sql =  $wpdb->prepare(
			"INSERT INTO {$wpdb->prefix}hojas_servicio (nombre, descripcion, fecha_inicio, fecha_fin, area, colaborador, paises, recursos, automatizaciones, alm_datos, observaciones, date_updated, user_wp) VALUES (%s, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s, %s, %d)",
			$datos['nombre'],
			$datos['descripcion'],
			$datos['fecha_inicio'],
			$datos['fecha_fin'],
			$datos['area'],
			$datos['colaborador'],
			$datos['paises'],
			$datos['recursos'],
			$datos['automatizaciones'],
			$datos['alm_datos'],
			$datos['observaciones'],
			$fecha_actual,
			get_current_user_id()
		);
		//echo $sql; die;
		$wpdb->query($sql);
	}

	private function actualizar_proceso($id)
	{
		$nonce = $_POST['edit_proceso'];

		if (!wp_verify_nonce($nonce, 'admin.php?page=hojas_servicio&action=edit')) {
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio&action=edit&element={$id}&msj=fallo_envio';</script>";
			die;
		}
		global $wpdb;

		$fecha_actual = new DateTime('', new DateTimeZone('America/Bogota'));

		$fecha_actual = $fecha_actual->format("Y-m-d H:i:s");

		$datos = array();
		$datos['nombre'] = sanitize_text_field($_POST['nombre']);
		$datos['descripcion'] = sanitize_text_field($_POST['descripcion']);

		$datos['fecha_inicio'] = date("Y-m-d H:i:s", strtotime($_POST['fecha_inicio']));
		if (!empty($_POST['fecha_fin'])) {
			$datos['fecha_fin'] = date("Y-m-d H:i:s", strtotime($_POST['fecha_fin']));
		} else {
			$datos['fecha_fin'] = 0;
		}

		$datos['area'] =  $_POST['areas'][0];
		$datos['colaborador'] =  $_POST['colaborador'];
		$datos['paises'] =  json_encode($_POST['paises']);
		$datos['recursos'] = $this->form_render->datos_json(['tipo_rec', 'URL', 'resp_recurso']);
		$datos['automatizaciones'] =  $this->form_render->datos_json(['nombre_auto', 'app_auto', 'url_auto', 'acciones_auto']);
		$datos['alm_datos'] =  $this->form_render->datos_json(['pl_datos', 'URL_datos']);
		$datos['observaciones'] =  $_POST['observaciones'];

		$wpdb->query($wpdb->prepare(
			"UPDATE {$wpdb->prefix}hojas_servicio SET nombre=%s, descripcion=%s, fecha_inicio=%s, fecha_fin=%s, area=%d, colaborador=%s, paises=%s, recursos=%s, automatizaciones=%s, alm_datos=%s, observaciones=%s, date_updated=%s, user_wp=%d WHERE id=%d",
			$datos['nombre'],
			$datos['descripcion'],
			$datos['fecha_inicio'],
			$datos['fecha_fin'],
			$datos['area'],
			$datos['colaborador'],
			$datos['paises'],
			$datos['recursos'],
			$datos['automatizaciones'],
			$datos['alm_datos'],
			$datos['observaciones'],
			$fecha_actual,
			get_current_user_id(),
			$id
		));
	}


	public function list_front()
	{
		$page = 1;
		if (isset($_GET['pag']) && !empty($_GET['pag'])) {
			$page = $_GET['pag'];
		}

		if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
			$buscar = $_GET['buscar'];
		} else {
			$buscar = '';
		}

		$datos = $this->get_all_service($page);

		$cuerpo = "<div class='lista_procesos'>";
		$cuerpo .= "<table class='table' id='tabla_procesos' data-order='[[ 1, \"desc\" ]]' style='font-size:.9rem;'>";
		$cuerpo .= "<thead><tr><th>ID</th><th>Proceso</th><th>Área</th><th>Fecha cre.</th><th>Fecha fin.</th><th>Acciones</th></tr></thead><tbody>";
		foreach ($datos as $proc) {
			$cuerpo .= "<tr>
			<td>AT-" . str_pad($proc->id, 3, "0", STR_PAD_LEFT) . "</td>
			<td><a href='" .  get_site_url() . "/procesos-satrack/?action=view_proceso&id={$proc->id}' class='text-secondary'><b>{$proc->nombre}</b><a></td>
			<td data-search='" . str_replace(" ", "-",  $this->form_render->get_datos("areas", $proc->area)) . "'>" .  $this->form_render->get_datos("areas", $proc->area) . "</td>
			<td>" .  $this->fecha($proc->fecha_inicio, 'd/m/A') . "</td>
			<td>" .  $this->fecha($proc->fecha_fin, 'd/m/A') . "</td>
			<td class='text-center' data-search='des: {$proc->descripcion}, user: " .  $this->get_user_by_id($proc->colaborador)['nombre'] . ", obs: {$proc->observaciones}, paises: " . implode(", ", json_decode($proc->paises)) . "'>";
			$cuerpo .= "<a href='" .  get_site_url() . "/procesos-satrack/?action=view_proceso&id={$proc->id}' class='text-info mx-2'><i class='fas fa-eye'></i></a>";
			if (get_current_user_id() > 0) : $cuerpo .= "| <a href='" .  get_site_url() . "/procesos-satrack/?action=edit_proceso&id={$proc->id}' class='text-primary mx-2'><i class='fas fa-edit'></i></a> | <a class='text-danger mx-2'><i class='fas fa-times'></i></a>";
			endif;
			$cuerpo .= "</td></tr>";
		}
		$cuerpo .= '</tbody></table></div>';

		return $cuerpo;
	}


	public function new_proceso_front()
	{
		if (isset($_POST['action']) && $_POST['action'] === "new_proceso") {
			$this->guardar_proceso();
			echo "<script>window.location.href = '" . get_site_url() . "/procesos-satrack/';</script>";
			die;
		}

		$cuerpo = "Probandoooo";
		ob_start();
?>
		<div class='caja_form_procesos'>
			<?= $this->form_render->form_open('new_proceso', false) ?>
			<?= wp_nonce_field('admin.php?page=hojas_servicio&action=new_proceso', 'new_proceso', true, false); ?>

			<div class='row mb-5'>
				<div class='col-md-6 d-flex justify-content-start align-items-center'>
					<label for='fecha_inicio' style='min-width:160px;'>Fecha de inicio</label>
					<?= $this->form_render->input("date", "fecha_inicio", true, null, null, "form-control", false) ?>
				</div>
				<div class='col-md-6 d-flex justify-content-start align-items-center'>
					<label style='min-width:160px;' for='fecha_fin'>Fecha de finalización</label>
					<?= $this->form_render->input("date", "fecha_fin", false, null, null, "form-control", false) ?>
				</div>
			</div>
			<div class='row mb-3'>
				<div class='col-md-4'>
					<label for='nombre'>Nombre del proceso</label>
				</div>
				<div class='col-md-8'>
					<?= $this->form_render->input("text", "nombre", true, null, "Ingrese el nombre del proceso", "form-control", false); ?>
				</div>
			</div>
			<div class='row mb-3'>
				<div class='col-md-4'><label for='descripcion'>Descripción</label></div>
				<div class='col-md-8'>
					<?= $this->form_render->input("text", "descripcion", false, null, "Descripción del proceso", "form-control", false); ?>
					<small class='description' id='tagline-description'>En pocas palabras, explica el proceso.</small>
				</div>
			</div>

			<div class='row mb-3'>
				<div class='col-md-4'><label for='fecha_fin'>Área</label></div>
				<div class='col-md-8'>
					<?= $this->form_render->select("areas", true, null, false); ?>
				</div>
			</div>
			<div class='row mb-3'>
				<div class='col-md-4'><label for='colaborador'>Nombre del colaborador</label></div>
				<div class='col-md-8'>
					<?= $this->form_render->select("colaboradores", true, null, false, 'colaborador') ?>
				</div>
			</div>
			<div class='row mb-3'>
				<div class='col-md-4'><label for='colaborador'>Paises afectados</label></div>
				<?= $this->form_render->radio_paises(null, false) ?>
			</div>
			<div class='row my-3'>
				<div class='col-12 p-3 border border-dark rounded'>
					<?= $this->form_render->multirecursos(null, false) ?>
				</div>
			</div>
			<div class='row my-3'>
				<div class='col-12 p-3 border border-dark rounded'>
					<?= $this->form_render->multiautomatiza(null, false) ?>
				</div>
			</div>
			<div class='row my-3'>
				<div class='col-12 p-3 border border-dark rounded'>
					<?= $this->form_render->multidatos(null, false) ?>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-12'><label for='observaciones'>Observaciones</label></div>
			</div>
			<div class='row mb-3'>
				<div class='col-md-12'><textarea class='form-control' name='observaciones' id='observaciones'></textarea></div>
			</div>
			<div class='row mb-3'>
				<div class='col-auto submit'><input type='submit' name='submit' id='submit' class='btn btn-primary button button-primary' value='Guardar cambios'></div>
			</div>
			</form>
		</div>
		<script>
			jQuery(function($) {
				$('.sortable').sortable();
			});
		</script>
<?php
		$cuerpo = ob_get_clean();

		return $cuerpo;
	}


	public function edit_proceso_front()
	{
		if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
			$this->actualizar_proceso($_GET['id']);
			echo "<script>window.location.href = '" . get_site_url() . "/procesos-satrack/';</script>";
			die;
		}

		$datos = array();
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = $_GET['id'];
			$datos = $this->get_proceso_id($id);
		} else {
			echo "<script>window.location.href = '" . get_site_url() . "/procesos-satrack/';</script>";
			die;
		}

		$cuerpo = "<div class='caja_form_procesos'>";
		$cuerpo .= $this->form_render->form_open('edit_proceso', false, "mi_form");
		$cuerpo .= wp_nonce_field('admin.php?page=hojas_servicio&action=edit', 'edit_proceso', true, false);

		$cuerpo .= "<div class='row mb-5'>
		<div class='col-md-6 d-flex justify-content-start align-items-center'>
		<label for='fecha_inicio' style='min-width:160px;'>Fecha de inicio</label>";
		$cuerpo .= $this->form_render->input("date", "fecha_inicio", true, $datos->fecha_inicio, null, "form-control", false) . "</div>";
		$cuerpo .= "<div class='col-md-6 d-flex justify-content-start align-items-center'><label style='min-width:160px;' for='fecha_fin'>Fecha de finalización</label>";
		$cuerpo .= $this->form_render->input("date", "fecha_fin", false, $datos->fecha_fin, null, "form-control", false) . "</div></div>";
		$cuerpo .= "<div class='row mb-3'><div class='col-md-4'><label for='nombre'>Nombre del proceso</label></div><div class='col-md-8'>";
		$cuerpo .= $this->form_render->input("text", "nombre", true, $datos->nombre, "Ingrese el nombre del proceso", "form-control", false);
		$cuerpo .=  "</div></div>
			<div class='row mb-3'><div class='col-md-4'><label for='descripcion'>Descripción</label></div><div class='col-md-8'>";
		$cuerpo .= $this->form_render->input("text", "descripcion", false, $datos->descripcion, "Descripción del proceso", "form-control", false);
		$cuerpo .= "<small class='description' id='tagline-description'>En pocas palabras, explica el proceso.</small>
			</div></div>";

		$cuerpo .= "<div class='row mb-3'><div class='col-md-4'><label for='fecha_fin'>Área</label></div><div class='col-md-8'>";
		$cuerpo .= $this->form_render->select("areas", true, $datos->area, false);
		$cuerpo .= "</div></div>
		<div class='row mb-3'><div class='col-md-4'><label for='colaborador'>Nombre del colaborador</label></div><div class='col-md-8'>";
		$cuerpo .=  $this->form_render->select("colaboradores", true, $datos->colaborador, false, 'colaborador') . "</div></div>";
		$cuerpo .= "<div class='row mb-3'><div class='col-md-4'><label for='colaborador'>Paises afectados</label></div>";
		$cuerpo .= $this->form_render->radio_paises($datos->paises, false) . "</div>
		<!--terminan los divs-->";
		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";

		$cuerpo .= $this->form_render->multirecursos($datos->recursos, false) . "</div></div>";
		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";
		$cuerpo .= $this->form_render->multiautomatiza($datos->automatizaciones, false) . "</div></div>";
		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";
		$cuerpo .= $this->form_render->multidatos($datos->alm_datos, false) . "</div></div>";
		$cuerpo .= "<div class='row'><div class='col-md-12'><label for='observaciones'>Observaciones</label></div></div>
		<div class='row mb-3'><div class='col-md-12'><textarea class='form-control' name='observaciones' id='observaciones'>{$datos->observaciones}</textarea></div></div>
		<div class='row mb-3'><div class='col-auto submit'><input type='submit' name='submit' id='submit' class='btn btn-primary button button-primary' value='Guardar cambios'></div></div>
    	</form></div>";

		$cuerpo .= "<script>
        jQuery( function($) {
          $( '.sortable' ).sortable();
        } );
        </script>";

		return $cuerpo;
	}

	public function view_proceso_front()
	{
		$datos = array();
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$id = $_GET['id'];
			$datos = $this->get_proceso_id($id);
		} else {
			echo "<script>window.location.href = '" . get_site_url() . "/procesos-satrack/';</script>";
			die;
		}

		$cuerpo = "<div class='caja_view_proceso'>";
		$cuerpo .= "<h2>{$datos->nombre}</h2>
		<p><b>ID: </b>AT-" . str_pad($id, 3, "0", STR_PAD_LEFT) . "</p>
		<p><i>{$datos->descripcion}</i></p><hr>
		<table class='tabla-sin'><tr><td>fecha de inicio:</td><td><strong>" . $this->fecha($datos->fecha_inicio) . "</strong></td></tr>
		<tr><td>Fecha de finalización:</td><td><strong>" . $this->fecha($datos->fecha_fin) . "</strong></td></tr>
		<tr><td>Área:</td><td><strong>" . $this->form_render->get_datos("areas", $datos->area) . "</strong></td></tr>
		<tr><td>Responsable:</td><td><strong>" . $this->get_user_by_id($datos->colaborador)['nombre'] . "</strong></td></tr>
		<tr><td>Paises:</td><td><strong>";
		foreach ($datos->paises as $ki => $pas) {
			if ($ki > 0) {
				$cuerpo .= ", ";
			}
			$cuerpo .= $this->form_render->get_datos('paises', $pas);
		}
		$cuerpo .= "</strong></td></tr></table>";

		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";
		$cuerpo .= "<div class='row'><div class='col-sm-8'><h3>Recursos</h3></div></div>";
		$cuerpo .= "<table class='table table-hover'><thead><tr>";
		$cuerpo .= "<th scope='col'>Tipo de recurso</th><th scope='col'>URL</th><th scope='col'>Responsable</th>";
		$cuerpo .= "</tr></thead><tbody>";
		foreach ($datos->recursos as $recurso) {
			$cuerpo .= "<tr scope='row'>
			<td><a target='_blank' href='{$recurso->URL}'>" . $this->form_render->get_datos("tipo_rec", $recurso->tipo_rec) . "</a></td>
			<td><input disabled class='form-control' value='{$recurso->URL}'></td>
            <td>" . $this->get_user_by_id($recurso->resp_recurso)['nombre'] . "</td>
            </tr>";
		}
		$cuerpo .= "</tbody></table></div></div>";

		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";
		$cuerpo .= "<div class='row'><div class='col-sm-8'><h3>Automatizaciones</h3></div></div>";
		$cuerpo .= "<table class='table table-hover'><thead><tr>";
		$cuerpo .= "<th scope='col'>Nombre</th><th scope='col'>Aplicación</th>
		<th style='min-width:200px;'>URL</th><th scope='col'>Descripción</th>";
		$cuerpo .= "</tr></thead><tbody>";
		foreach ($datos->automatizaciones as $recurso) {
			$cuerpo .= "<tr scope='row'>
			<td>{$recurso->nombre_auto}</td>
            <td>" . $this->form_render->get_datos("app_auto", $recurso->app_auto) . "</td>
			<td><input disabled class='form-control' value='{$recurso->url_auto}'></td>
            <td>{$recurso->acciones_auto}</td>
            </tr>";
		}
		$cuerpo .= "</tbody></table></div></div>";

		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";
		$cuerpo .= "<div class='row'><div class='col-sm-8'><h3>Datos</h3></div></div>";
		$cuerpo .= "<table class='table table-hover'><thead><tr>";
		$cuerpo .= "<th scope='col' style='min-width:150px;'>Origen de los datos</th><th scope='col'>Ubicación</th>";
		$cuerpo .= "</tr></thead><tbody>";
		foreach ($datos->alm_datos as $recurso) {
			$cuerpo .= "<tr scope='row'>
            <td>" . $this->form_render->get_datos("pl_datos", $recurso->pl_datos) . "</td>
            <td><input disabled class='form-control' value='{$recurso->URL_datos}'></td>
            </tr>";
		}
		$cuerpo .= "</tbody></table></div></div>";

		$cuerpo .= "<div class='row'><div class='col-md-12'><p><b>Observaciones</label></b></p></div>
		<div class='row mb-5'><div class='col-md-12'><textarea class='form-control' disabled name='observaciones' id='observaciones'>{$datos->observaciones}</textarea></div></div>
    	</div>";

		return $cuerpo;
	}

	private function guardar_user()
	{
		if (!current_user_can('manage_options')) {
			wp_die('Not allowed');
		}

		$nonce = $_POST['new_user'];

		if (!wp_verify_nonce($nonce, 'admin.php?page=hojas_servicio&action=users')) {
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio&msj=fallo_envio;</script>";
			die;
		}
		$id = sizeof($this->users) + 1;
		$user = array("id" => $id, "nombre" => sanitize_text_field($_POST['name-user']), "email" => sanitize_text_field($_POST['email-user']));
		$this->users[] = $user;
		update_option('users_ads_satrack', $this->users);
		// Regresamos a la pagina de ajustes
		echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio&action=users';</script>";
		die;
	}

	private function update_user()
	{
		if (!current_user_can('manage_options')) {
			wp_die('Not allowed');
		}

		$nonce = $_POST['update_user'];

		if (!wp_verify_nonce($nonce, 'admin.php?page=hojas_servicio&action=users')) {
			echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio&msj=fallo_envio;</script>";
			die;
		}
		$id = $_POST['key'];

		$user = array("id" => $_GET['element'], "nombre" => sanitize_text_field($_POST['name-user']), "email" => sanitize_text_field($_POST['email-user']));
		$this->users[$id] = $user;

		update_option('users_ads_satrack', $this->users);
		// Regresamos a la pagina de ajustes
		echo "<script>window.location.href = '" . get_admin_url() . "admin.php?page=hojas_servicio&action=users';</script>";
		die;
	}

	public function get_id_user($id)
	{
		$key = null;
		foreach ($this->users as $k => $user) {
			if ($user['id'] == $id) {
				$key = $k;
			}
		}
		return $key;
	}

	public function get_user_by_id($id)
	{
		$user = array();
		foreach ($this->users as $use) {
			if ($use['id'] == $id) {
				$user = $use;
			}
		}
		return $user;
	}

	private function fecha($fecha, $tipo = 'full')
	{
		if ($fecha == "0000-00-00 00:00:00") {
			return 'N/A';
		} else {
			$fecha = strtotime($fecha);

			$resp = "";
			switch ($tipo) {
				case 'full':
					$resp = $this->dias[date("N", $fecha) - 1] . ", " . date("j", $fecha) . " de " . $this->meses[date("n", $fecha) - 1] . " de " . date("Y", $fecha) . ".";
					break;
				case 'd/m/A':
					$resp = date("d", $fecha) . "-" . $this->meses_cortos[date("n", $fecha) - 1] . "-" . date("Y", $fecha);
					break;
				default:
					break;
			}
			return $resp;
		}
	}
}
