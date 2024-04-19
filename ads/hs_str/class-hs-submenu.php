<?php

/** 
 * Creates the submenu item for the plugin. 
 * 
 * @package Custom_Admin_Settings 
 */
/** 
 * Creates the submenu item for the plugin. 
 * 
 * Registers a new menu item under 'Tools' and uses the dependency passed into 
 * the constructor in order to display the page corresponding to this menu item. 
 * 
 * @package Custom_Admin_Settings 
 */
if (!defined('ABSPATH')) die;
class HS_Submenu
{
	/** 
	 * A reference the class responsible for rendering the submenu page. 
	 * 
	 * @var HS_Submenu_Page 
	 * @access private 
	 */
	private $submenu_page;
	/** 
	 * Initializes all of the partial classes. 
	 * 
	 * @param HS_Submenu_Page $submenu_page A reference to the class that renders the 
	 * page for the plugin. 
	 */
	public function __construct($submenu_page)
	{
		$this->submenu_page = $submenu_page;
	}
	/** 
	 * Adds a submenu for this plugin to the 'Tools' menu. 
	 */
	public function init()
	{
		add_action('admin_menu', array($this, 'add_options_page'));
	}
	/** 
	 * Creates the submenu item and calls on the Submenu Page object to render 
	 * the actual contents of the page. 
	 */
	public function add_options_page()
	{
		$accion = "render";
		if (isset($_GET['action']) && !empty($_GET['action'])) {

			switch ($_GET['action']) {
				case "new_proceso":
					$accion = "new_proceso";
					break;
				case "edit":
					$accion = "edit_proceso";
					break;
				case "users":
					$accion = "usuarios";
					break;
				case "user_edit":
					$accion = "edit_user";
					break;
				default:
					break;
			}
		}

		add_submenu_page(
			'ads_satrack',
			'Ordenes de Proceso',
			'Ordenes de proceso',
			'manage_options',
			'hojas_servicio',
			array($this->submenu_page, $accion)
		);
	}
}
