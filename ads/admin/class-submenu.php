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
class Submenu {
        /** 
* A reference the class responsible for rendering the submenu page. 
* 
* @var Submenu_Page 
* @access private 
*/
	private $submenu_page;
	/** 
* Initializes all of the partial classes. 
* 
* @param Submenu_Page $submenu_page A reference to the class that renders the 
* page for the plugin. 
*/
	public function __construct( $submenu_page ) {
		$this->submenu_page = $submenu_page;
	}
	/** 
* Adds a submenu for this plugin to the 'Tools' menu. 
*/
	public function init() {
		 add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}
	/** 
* Creates the submenu item and calls on the Submenu Page object to render 
* the actual contents of the page. 
*/
	public function add_options_page() {
		$accion = "render";
		if(isset($_GET['action']) && !empty($_GET['action'])){
			
			switch($_GET['action']){
				case "nueva_fecha": $accion = "new_fecha"; break;
				case "edit": $accion = "edit_fecha"; break;
				default: break;
			}
		}
		
  add_submenu_page( 'ads_satrack', 'Días festivos Wolkvox', 'Días festivos Wolkvox',
  'manage_options', 'dias_festivos', array( $this->submenu_page, $accion ));
	}
}