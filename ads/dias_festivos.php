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
foreach (glob(plugin_dir_path(__FILE__) . 'admin/*.php') as $file) {
	include_once $file;
}

class DiaFestivo
{
	private $festivos;
	private $fecha_end_holliday;

	function __construct()
	{
		add_action('plugins_loaded', array($this, 'satrack_festivos_admin_settings'));
		$this->insert_demo_table_into_db();

		$this->festivos = $this->get_festivos();
		$this->fecha_end_holliday = "";
	}

	public function satrack_festivos_admin_settings()
	{
		$plugin = new Submenu(new Submenu_Page());
		$plugin->init();
	}

	public function insert_demo_table_into_db()
	{
		global $wpdb;
		// set the default character set and collation for the table
		$charset_collate = $wpdb->get_charset_collate();
		// Check that the table does not already exist before continuing
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}dias_festivos (
        id int(11) NOT NULL AUTO_INCREMENT,
        fecha date NOT NULL,
        descripcion varchar(155) DEFAULT NULL,
        status tinyint(2) DEFAULT '1',
        id_user int(255) NOT NULL,
        date_created timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        date_updated datetime DEFAULT CURRENT_TIMESTAMP,
        recurrente tinyint(4) DEFAULT NULL,
        PRIMARY KEY (id)
        ) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
		$is_error = empty($wpdb->last_error);
		return $is_error;
	}

	public function is_holiday()
	{
		$valida = false;
		$hoy = new DateTime('now', new DateTimeZone('America/Guayaquil'));

		foreach ($this->festivos as $holiday) {
			$timeMin = 6;
			$timeMax = 6;
			if ($holiday->eslunes) {
				$timeMin += 24;
			}

			$fecha_min = strtotime('-' . $timeMin . ' hour', strtotime($holiday->fecha . " 00:00:00"));
			$fecha_max = strtotime('+' . $timeMax . ' hour', strtotime($holiday->fecha . " 24:00:00"));

			if ($hoy->getTimestamp() > $fecha_min && $hoy->getTimestamp() < $fecha_max) {
				$valida = true;
				$this->fecha_end_holliday = $fecha_max;
			}
		}

		return $valida;
	}

	public function llamar()
	{
		return $this->fecha_end_holliday;
	}

	private function get_festivos()
	{
		global $wpdb;

		$datos = $wpdb->get_results("SELECT * from {$wpdb->prefix}dias_festivos WHERE status = 1", OBJECT);

		foreach ($datos as $key => $val) {
			$nomdia = date("N", strtotime($val->fecha));

			if ($val->recurrente) {
				$datos[$key]->fecha = date("Y", time()) . date("-m-d", strtotime($val->fecha));
			}

			if ($nomdia == 1) {
				$datos[$key]->eslunes = 1;
			} else {
				$datos[$key]->eslunes = 0;
			}
		}

		return $datos;
	}
}
