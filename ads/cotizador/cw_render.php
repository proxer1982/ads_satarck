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
class CW_Render
{
	/** 
	 * This function renders the contents of the page associated with the Submenu 
	 * that invokes the render method. In the context of this plugin, this is the 
	 * Submenu class. 
	 */
	private $opciones, $action, $impuesto, $imp_mes, $dir_company, $phone_company, $valor_imp, $ceros, $paises, $distri, $pasos_num, $equipos, $ind, $decima, $valor_inst_disp, $planes, $reglas, $acc, $menu, $datos, $lista_distri, $db, $html, $html_admin, $db_cot;

	public function __construct()
	{
		//global $ads_data;
		include_once plugin_dir_path(__FILE__) . 'html/html.php';
		include_once plugin_dir_path(__FILE__) . 'html/html_admin.php';
		include_once plugin_dir_path(__FILE__) . 'db/db_cotizaciones.php';

		$this->db_cot = new DB_Cotizaciones();

		if (is_admin()) $this->html_admin = new Html_Admin();

		$this->html = new Html();


		$this->get_action_page();
	}

	public function render($pais, $print = false)
	{
		global $ads_user;
		global $pagenow;
		$user = $ads_user->get_user();

		$user_pais = $ads_user->get_list_paises();
		$codigo = '';

		if (is_admin()) {
			$codigo .= $this->html->raw_selector_pais();
		} else {
			$num_pais = sizeof($user_pais);

			if (!empty($this->action) && $this->action === 'edit_user') {
				/*Cargamos el formualrio para editar el usuario */
				if (isset($_POST["update_user"]) && !empty($_POST["update_user"])) {
					$this->guardar_usuario();
					die;
				}

				$codigo .= $this->html->edit_user_front($user);
			} else {
				if ((!empty($pais) || $pais !== null) && in_array($pais, $user_pais)) {

					switch ($this->action) {
						case "new_cotizacion":
							$codigo .= $this->html->new_cotizacion_front($pais);
							break;
						case "edit_cotizacion":
							$codigo .= $this->html->edit_cotizacion_front();
							break;
						case "view_cotizacion":
							$codigo .= $this->html->view_cotizacion_front();
							break;
						case "pdf_cotizacion":
							$codigo .= $this->html->view_cotizacion_pdf();
							break;
						case "edit_user":
							if (isset($_POST["update_user"]) && !empty($_POST["update_user"])) {
								$this->guardar_usuario();
								die;
							}
							$codigo .= $this->html->edit_user_front($user, $pais);
							break;
						case "cotizaciones_aprobadas":
							$cotizaciones = $this->db_cot->getAllCotizacionesActivas($user,  $pais, 2,);
							$codigo .= $this->html->list_cotizacion($cotizaciones, "Cotizaciones Aprobadas", $pais);
							break;
						default:
							$cotizaciones = $this->db_cot->getAllCotizacionesActivas($user, $pais);
							$codigo .= $this->html->list_cotizacion($cotizaciones, "Cotizaciones", $pais);
							break;
					}
				} elseif ((empty($pais) || $pais === null) && $num_pais > 1) {
					$codigo .= $this->html->raw_selector_pais();
				} elseif ((empty($pais) || $pais === null) && $num_pais === 1) {
					/*Validamos si el usuario solo tiene asignado un pais */
					global $post;
					$url = get_permalink($post->post_parent) . strtolower($user_pais[0]) . '/';

					$codigo .= "<script>window.location.href = '{$url}?v=1';</script>";
				} else {
					$codigo .= "<h2 class='text-center'>No tienes privilegios para pdeor ver esta página</h2>";
				}
			}
		}

		if ($print) {
			echo $codigo;
		} else {
			return $codigo;
		}

		/*
		

			
		
			return "selector";
			//
		

			if ($this->action === 'edit_user') {
				echo "Editar usuario";
				
				$codigo .= $this->html->edit_user_front($user);
			} else {
				
			}
		


		*/
	}

	public function options()
	{
		if (isset($_POST['options']) && !empty($_POST['options'])) {
			global $ads_data;

			$ads_data->guardar_options();
			return;
		}

		if (isset($_POST['actualizar_opcion']) && !empty($_POST['actualizar_opcion'])) {
			$this->actualizar_opcion();
			return;
		}

		if (isset($_POST['new_opcion']) && !empty($_POST['new_opcion'])) {
			$this->nueva_opcion();
			return;
		}

		$this->html_admin->create_form_options();
	}

	public function form_login()
	{
		global $pagenow;
		return $this->html->form_login($pagenow);
	}




	public function guardar_usuario()
	{

		$nonce = $_POST['update_user'];

		if (!wp_verify_nonce($nonce, '/cotizador-web/?action=edit_user')) {
			/*echo "<script>window.location.href = '" . get_permalink(get_the_ID()) . "';</script>";*/
			die;
		}

		$userid = get_current_user_id();
		$user = get_userdata($userid);

		wp_update_user([
			'ID' => $userid, // this is the ID of the user you want to update.
			'first_name' => sanitize_text_field($_POST['user_nombres']),
			'last_name' => sanitize_text_field($_POST['user_apellidos']),
			'user_url' => sanitize_url($_POST['user_url']),
		]);


		if (isset($_POST['user_phone'])) {
			update_user_meta($userid, 'user_phone', sanitize_text_field($_POST['user_phone']));
		}

		if (isset($_POST['user_tele'])) {
			update_user_meta($userid, 'user_tele', sanitize_text_field($_POST['user_tele']));
		}

		if (isset($_POST['user_calendly'])) {
			update_user_meta($userid, 'user_calendly', sanitize_text_field($_POST['user_calendly']));
		}

		if (isset($_POST['user_dir'])) {
			update_user_meta($userid, 'user_dir', sanitize_text_field($_POST['user_dir']));
		}

		if (isset($_POST['user_cargo'])) {
			update_user_meta($userid, 'user_cargo', sanitize_text_field($_POST['user_cargo']));
		}

		echo "<script>window.location.href = '" . get_permalink(get_the_ID()) . "';</script>";
	}

	public function edit_cotizacion_front()
	{
		/*if(isset($_POST['nombre']) && !empty($_POST['nombre'])){
			$this->actualizar_proceso($_GET['id']);
			echo "<script>window.location.href = '" . get_site_url() . "/procesos-satrack/';</script>";
			die;
		}
		
		$datos = array();
		if(isset($_GET['id']) && !empty($_GET['id'])){
			$id = $_GET['id'];
			$datos = $this->get_proceso_id($id);
		} else {
			echo "<script>window.location.href = '" . get_site_url() . "/procesos-satrack/';</script>";
			die;
		}
		
		$cuerpo = "<div class='caja_form_procesos'>";
		$cuerpo .= $this->form_render->form_open('edit_proceso', false, "mi_form");
		$cuerpo .= wp_nonce_field( 'admin.php?page=hojas_servicio&action=edit', 'edit_proceso', true, false );

		$cuerpo .= "<div class='row mb-5'>
		<div class='col-md-6 d-flex justify-content-start align-items-center'>
		<label for='fecha_inicio' style='min-width:160px;'>Fecha de inicio</label>";
		$cuerpo .= $this->form_render->input("date", "fecha_inicio", true, $datos->fecha_inicio, null, "form-control", false)."</div>";
		$cuerpo .= "<div class='col-md-6 d-flex justify-content-start align-items-center'><label style='min-width:160px;' for='fecha_fin'>Fecha de finalización</label>";
		$cuerpo .= $this->form_render->input("date", "fecha_fin", false, $datos->fecha_fin, null, "form-control", false)."</div></div>";
		$cuerpo .="<div class='row mb-3'><div class='col-md-4'><label for='nombre'>Nombre del proceso</label></div><div class='col-md-8'>";
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
		$cuerpo .=  $this->form_render->select("colaboradores", true, $datos->colaborador, false, 'colaborador')."</div></div>";
		$cuerpo .= "<div class='row mb-3'><div class='col-md-4'><label for='colaborador'>Paises afectados</label></div>";
		$cuerpo .= $this->form_render->radio_paises($datos->paises, false)."</div>
		<!--terminan los divs-->";
		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";

		$cuerpo .= $this->form_render->multirecursos($datos->recursos, false)."</div></div>";
		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";
		$cuerpo .= $this->form_render->multiautomatiza($datos->automatizaciones, false)."</div></div>";
		$cuerpo .= "<div class='row my-3'><div class='col-12 p-3 border border-dark rounded'>";
		$cuerpo .= $this->form_render->multidatos($datos->alm_datos, false)."</div></div>";
		$cuerpo .= "<div class='row'><div class='col-md-12'><label for='observaciones'>Observaciones</label></div></div>
		<div class='row mb-3'><div class='col-md-12'><textarea class='form-control' name='observaciones' id='observaciones'>{$datos->observaciones}</textarea></div></div>
		<div class='row mb-3'><div class='col-auto submit'><input type='submit' name='submit' id='submit' class='btn btn-primary button button-primary' value='Guardar cambios'></div></div>
    	</form></div>";

		$cuerpo .= "<script>
        jQuery( function($) {
          $( '.sortable' ).sortable();
        } );
        </script>";

		return $cuerpo;*/
	}

	private function actualizar_opcion()
	{
		$nonce = $_POST['actualizar_opcion'];

		if (!wp_verify_nonce($nonce, 'admin.php?page=cotizador_web&action=options')) {
			/*echo "<script>window.location.href = '" . get_permalink(get_the_ID()) . "';</script>";*/
			die;
		}

		$id = $_GET['id_opcion'];
		$datos = [
			'nombre_opcion' => $_POST['nombre_opcion'],
			'paises' => rest_sanitize_array($_POST['paises']),
		];
		foreach ($_POST['paises'] as $pais) {
			switch ($_POST['tipo_opcion']) {
				case 'accesorio':
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) str_replace(',', '.', $_POST["datos_{$pais}"]['valor']);
					$datos['datos_opcion'][$pais]['valor_inst'] = ($_POST["datos_{$pais}"]['valor_inst'] === '') ? 0 : (float) str_replace(',', '.', $_POST["datos_{$pais}"]['valor_inst']);
					break;
				case 'equipo':
					$datos['datos_opcion']['planes'] = $_POST['planes'];
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) str_replace(',', '.', $_POST["datos_{$pais}"]['valor']);
					$datos['datos_opcion'][$pais]['instalacion'] = (isset($_POST["datos_{$pais}"]['instalacion']) && $_POST["datos_{$pais}"]['instalacion'] === 'true') ? 1 : 0;
					break;
				case 'plan':
					$datos['datos_opcion']['pag'] = $_POST['pag'];
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) str_replace(',', '.', $_POST["datos_{$pais}"]['valor']);
					$datos['datos_opcion'][$pais]['valor_comodato'] = ($_POST["datos_{$pais}"]['valor_comodato'] === '') ? 0 : (float) str_replace(',', '.', $_POST["datos_{$pais}"]['valor_comodato']);
					break;
				case 'instalacion':
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) str_replace(',', '.', $_POST["datos_{$pais}"]['valor']);
					break;
			}
		}

		$datos['paises'] = implode(', ', $datos['paises']);
		$datos['datos_opcion'] = json_encode($datos['datos_opcion']);
		$db_config = new DB_Config();
		$db_config->update_opcion($id, $datos);
		//echo admin_url('admin.php?page=cotizador_web&tab=datos&action=editar_opcion&id_opcion=' . $id);
		echo "<script>window.location.href = '" . admin_url('admin.php?page=cotizador_web&tab=datos&action=editar_opcion&id_opcion=' . $id) . "';</script>";
		die;
	}

	private function nueva_opcion()
	{
		$nonce = $_POST['new_opcion'];

		if (!wp_verify_nonce($nonce, 'admin.php?page=cotizador_web&action=options')) {
			echo "<script>window.location.href = '" . get_permalink(get_the_ID()) . "';</script>";
			die;
		}

		$datos = [
			'nombre_opcion' => $_POST['nombre_opcion'],
			'tipo_opcion' => $_POST['tipo_opcion'],
			'paises' => rest_sanitize_array($_POST['paises'])
		];
		foreach ($_POST['paises'] as $pais) {
			switch ($_POST['tipo_opcion']) {
				case 'accesorio':
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) $_POST["datos_{$pais}"]['valor'];
					$datos['datos_opcion'][$pais]['valor_inst'] = ($_POST["datos_{$pais}"]['valor_inst'] === '') ? 0 : (float) $_POST["datos_{$pais}"]['valor_inst'];
					break;
				case 'equipo':
					$datos['datos_opcion']['planes'] = $_POST['planes'];
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) $_POST["datos_{$pais}"]['valor'];
					$datos['datos_opcion'][$pais]['instalacion'] = (isset($_POST["datos_{$pais}"]['instalacion']) && $_POST["datos_{$pais}"]['instalacion'] === 'true') ? 1 : 0;
					break;
				case 'plan':
					$datos['datos_opcion']['pag'] = $_POST['pag'];
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) $_POST["datos_{$pais}"]['valor'];
					$datos['datos_opcion'][$pais]['valor_comodato'] = ($_POST["datos_{$pais}"]['valor_comodato'] === '') ? 0 : (float) $_POST["datos_{$pais}"]['valor_comodato'];
					break;
				case 'instalacion':
					$datos['datos_opcion'][$pais]['valor'] = ($_POST["datos_{$pais}"]['valor'] === '') ? 0 : (float) $_POST["datos_{$pais}"]['valor'];
					break;
			}
		}

		$datos['paises'] = implode(', ', $datos['paises']);
		$datos['datos_opcion'] = json_encode($datos['datos_opcion']);

		$db_config = new DB_Config();
		$db_config->insert_opcion($datos);
		//echo admin_url('admin.php?page=cotizador_web&tab=datos&action=editar_opcion&id_opcion=' . $id);
		echo "<script>window.location.href = '" . admin_url('admin.php?page=cotizador_web&tab=datos&tipo=' . $_POST['tipo_opcion']) . "';</script>";
		die;
	}

	private function get_action_page()
	{
		if (isset($_GET['action']) && !empty($_GET['action'])) {
			$this->action = $_GET['action'];
		} else {
			$this->action = false;
		}
	}
}
