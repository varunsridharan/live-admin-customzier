<?php
/**
 * Live Admin Customizer
 *
 * Create Beautifyl Admin Themes With
 *
 * @package Live_Admin_Customizer
 * @subpackage Live_Admin_Customizer_Scss_Builder
 */

/* Exit if accessed directly */
if ( ! defined( 'lac_path' ) ) exit;

class Live_Admin_Customizer_Scss_Builder {
 	public $scss_generated_variable;
 	public $scss_class;
 	public $defined_variables;
 	public $defined_functions;
 	public $defined_base_scss;
	public $defined_admin_bar_scss;
 	public $post_colors;
	/**
	 * Generates SCSS Code, Requires SCSS Complier Files
	 * @since 0.1
	 * @access public
	 */
	public function __construct() {
		

		# Include The Required SCSS Complier Files
		include(lac_path.'scss_complier/src/Colors.php');
		include(lac_path.'scss_complier/src/Compiler.php');
		include(lac_path.'scss_complier/src/Formatter.php');
		include(lac_path.'scss_complier/src/Formatter/Compressed.php');
		include(lac_path.'scss_complier/src/Formatter/Crunched.php');
		include(lac_path.'scss_complier/src/Formatter/Expanded.php');
		include(lac_path.'scss_complier/src/Formatter/Nested.php');
		include(lac_path.'scss_complier/src/Parser.php');
		include(lac_path.'scss_complier/src/Server.php');
		include(lac_path.'scss_complier/classmap.php');
		
		$this->scss_class = new scssc();
		$this->defined_variables = file_get_contents(lac_path.'scss_base/variables.scss');
		$this->defined_functions = file_get_contents(lac_path.'scss_base/mixins.scss');
		$this->defined_base_scss = file_get_contents(lac_path.'scss_base/base.scss');
		$this->defined_admin_bar_scss = file_get_contents(lac_path.'scss_base/admin_bar.scss');
	}
	
	/**
	 * Checks for empty values in $_REQUEST
	 * @since 0.1
	 * @access private
	 * @param string $name key to check empty in $_REQUEST
	 * @return value | NULL
	 */
	private function scss_check_empty($name){ 
		if(isset($_REQUEST['colors'][$name]) && !empty($_REQUEST['colors'][$name])){
			return $_REQUEST['colors'][$name];
		} else {
			return "null";
		}
	}
	
	
	/**
	 * Generates Color Array
	 * @since 0.1
	 * @access private
	 * @return multitype:string
	 */
	private function color_array(){
		return array("text-color","base-color","icon-color","highlight-color","notification-color","body-background","link",
				"link-focus","action-color","button-color", "form-checked","menu-text","menu-icon","menu-background",
				"menu-highlight-text","menu-highlight-icon","menu-highlight-background","menu-current-text", "menu-current-icon", 
				"menu-current-background", "menu-submenu-text", "menu-submenu-background", "menu-submenu-background-alt",
				"menu-submenu-focus-text", "menu-submenu-current-text", "menu-bubble-text", "menu-bubble-background", 
				"menu-bubble-current-text", "menu-bubble-current-background", "menu-collapse-text", "menu-collapse-icon", 
				"menu-collapse-focus-text", "menu-collapse-focus-icon", "adminbar-avatar-frame", "adminbar-input-background", 
				"menu-customizer-text","secondary-button-color");
	}
	
	/**
	 * Common Scss String Replace
	 * @since 0.1
	 * @access private
	 * @param string to replace $value
	 * @return string | boolean
	 */
	private function scss_replace($value){
		$color_search = array(" ","-","/","&");
		$color_replace = '_';	
		if($value)
			return str_replace($color_search,$color_replace,$value);
		else 
			return false;	
	}
	
	/**
	 * Creates Admin Style CSS Meta Data
	 * @param string $styleName Name of the style
	 * @param string $styleSlug Style Slug Used To Workaround In Wp
	 * @param string $createdUser Style Created By Name
	 * @return string
	 */
	private function create_metaData($styleName,$styleSlug,$createdUser){
		$meta_data = "/* 
Name: ".$_REQUEST['styleName']." 
Slug: ".$styleSlug."
CreatedTime: ".time()." 
CreatedBy: ".$createdUser." 
Listing: true
color: ".$this->post_colors."
*/\n";
 		return $meta_data;
		
	}
 
	/**
	 * Generates SCSS Variables With Given Values
	 * @since 0.1
	 * @access public
	 * @return string $this->scss_generated_variable
	 */
	public function scss_code_generate(){
		$colors = ' ';
		foreach($this->color_array() as $color){
			$replaced_color = $this->scss_replace($color);
			${$replaced_color} = $this->scss_check_empty($color);
			$colors .= "$$color".": ".${$replaced_color}.";\n";
		}
		$this->scss_generated_variable = $colors;
		$colors = array_filter($_REQUEST['colors']);
		$this->post_colors = implode(",",$colors);
	}
	
	/**
	 * Generates CSS Code From SCSS Code
	 * @since 0.1
	 * @access public
	 * @return CSS Code
	 */
	public function scss_compile_code(){
		return $this->scss_class->compile($this->scss_generated_variable.$this->defined_variables.$this->defined_functions.$this->defined_base_scss);
	}
	
	/**
	 * Generates CSS Code From SCSS Code For Admin Bar
	 * @since 0.3
	 * @access public
	 * @return CSS Code
	 */
	public function scss_admin_bar_compile_code(){
		return $this->scss_class->compile($this->scss_generated_variable.$this->defined_variables.$this->defined_functions.$this->defined_admin_bar_scss);
	}	
	/**
	 * Creates New Style In USER CSS Folder
	 * @since 0.1
	 * @access public
	 * @return file
	 */
	public function scss_create(){
		$styleName = $_REQUEST['styleName'];
		if(empty($styleName)){
			echo 'empty_name';
		} else {
			$styleSlug = $this->scss_replace($styleName);
			$createdUser = $_REQUEST['current_user'];
			$generated_scss = $this->scss_compile_code();
			$admin_generated_scss = $this->scss_admin_bar_compile_code();
			$metaData = $this->create_metaData($styleName,$styleSlug,$createdUser);
			$metaData .= $generated_scss;
			
			if(!file_exists(lac_style_path.$styleSlug)) {
				mkdir(lac_style_path.$styleSlug.'/', 0777);
			}
			
			$admin_create_css_file = fopen(lac_style_path.$styleSlug.'/'.$styleSlug.'_admin_bar.css', "w") or die("Unable to open file!");
			$create_css_file = fopen(lac_style_path.$styleSlug.'/'.$styleSlug.'.css', "w") or die("Unable to open file!");
			$create_scss_file = fopen(lac_style_path.$styleSlug.'/'.$styleSlug.'.scss', "w") or die("Unable to open file!");
			$css_write = fwrite($create_css_file, $metaData);
			$admin_css_write = fwrite($admin_create_css_file,$admin_generated_scss);
			$scss_write = fwrite($create_scss_file, $this->scss_generated_variable);
			fclose($create_css_file);
			fclose($admin_create_css_file);
			fclose($create_scss_file);
			
			if($css_write && $scss_write){ echo 'saved';}
			else {echo 'save failed';}			
		}		
	}
}

?>