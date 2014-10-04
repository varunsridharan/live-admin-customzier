<?php
/**
 * Live Admin Customizer
 *
 * Create Beautifyl Admin Themes With
 *
 * @package Live_Admin_Customizer
 * @subpackage Live_Admin_Customizer_Page
 */

/* Exit if accessed directly */
if ( ! defined( 'lac_path' ) ) exit;

class Live_Admin_Customizer_Page {
 
	public function lac_page(){
		$current_user = wp_get_current_user();
		$pageName = "Create New Theme";
		require_once(lac_path.'views/header.php');
		require_once(lac_path.'views/lac_page.php');
		require_once(lac_path.'views/footer.php');
	}
	
	public function lac_vex_page(){
		$pageName = "View Existing Theme";
		require_once(lac_path.'views/header.php'); 
		require_once(lac_path.'views/lac_vex_page.php');
		require_once(lac_path.'views/footer.php');		
	}
} 

?>