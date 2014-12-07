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
    public $scss_output;
    
    function __construct() {
        $this->scss_output = array();
    }
    
    public function scss_val($key){
        if(is_array($this->scss_output) && isset($this->scss_output[$key])){
            return $this->scss_output[$key];
        } else {
            return '';
        }

    }
    
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