<?php
/**
 * Live Admin Customizer
 *
 * Create Beautifyl Admin Themes With Live Admin Customizer
 *
 * @package Live_Admin_Customizer
 * @subpackage Main
 */

/**
 * Plugin Name: Live Admin Customizer
 * Plugin URI:  http://varunsridharan.in/
 * Description: Create Beautifyl Admin Themes With Live Admin Customizer
 * Author:      Varun Sridharan
 * Author URI:  http://varunsridharan.in/
 * Version:     0.2
 * License:     GPL
 */


/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

# Define Required Plugin Variable
define('lac_url',plugins_url('',__FILE__).'/');
define('lac_path',plugin_dir_path( __FILE__ ));
define('lac_style_url',content_url().'/live_admin_customizer/user_style/');
define('lac_style_path',ABSPATH. 'wp-content/live_admin_customizer/user_style/');


class Live_Admin_Customizer {
	
	public $lac_v;
	public $scss_complier;
	public $lac_pages;
	public $css_files;
	public $scss_output;
	
	/**
	 * @since 0.1
	 * @access public
	 */
	public function __construct() {
		$this->lac_v = '0.1';
		register_activation_hook( __FILE__, array($this ,'_activate') ); 
		add_action('admin_menu', array($this,'add_menu'));
        
		# Include Required Files
		require_once(lac_path."inc/class_lac_page.php");
		require_once(lac_path."inc/class_scss_builder.php");
		$this->scss_complier = new Live_Admin_Customizer_Scss_Builder() ;
		$this->lac_pages = new Live_Admin_Customizer_Page() ;
        $this->edit_exSCSS();
		# Add Custom Styles To Profile View 
		add_action( 'admin_init' ,array($this,'enque_user_styles'));
		
		# Register Post Actions
		add_action( 'admin_post_lac_scss_compile',array($this,'scss_compile') );
		add_action( 'admin_post_lac_scss_create', array($this,'scss_create') );
		add_action( 'admin_post_lac_scss_delete', array($this,'scss_delete') );
 	}

	
	/**
	* Plugin Activation 
	* @since 0.1
	* @access public
	* @return None
 	*/ 
	public function _activate(){
		if(!file_exists(lac_style_path)) {
			mkdir(lac_style_path, 0777,true);
		} 
	}

	
	/**
	 * Adds A Seperate Menu
	 * @since 0.1
	 * @access public
	 * @return Adds Menu Array In WP-ADMIN Menu Array
	 */
	public function add_menu(){
		$page1 = add_menu_page('Live Admin Customizer', 'Live Admin Customizer', 'administrator','live-admin-customizer', array($this->lac_pages,'lac_page') );
		$page2 = add_submenu_page('live-admin-customizer', 'Create New', 'Create New', 'administrator', 'live-admin-customizer',array($this->lac_pages,'lac_page') );
		$page3 = add_submenu_page('live-admin-customizer', 'View Themes', 'View Themes', 'administrator', 'lac-view-existing-page', array($this->lac_pages,'lac_vex_page'));
		# Register Style & Script
		$this->register_script_style(); 
		add_action( 'admin_print_styles-' . $page1, array($this,'enqueue_script_style') );
		add_action( 'admin_print_styles-' . $page2, array($this,'enqueue_script_style') );
		add_action( 'admin_print_styles-' . $page3, array($this,'enqueue_script_style') );
 	}
	
 	/**
 	 * Register All Needed Scripts & Styles
 	 * @since 0.1
 	 * @access public
  	 */ 
	public function register_script_style(){
		wp_register_script( 'lac_script', lac_url.'js/script.js', array( 'jquery' ), $this->lac_v, false );
		wp_register_script( 'lac_colorpicker', lac_url.'js/colpick.js', array( 'jquery' ), $this->lac_v, false );
		wp_register_style( 'lac_style', lac_url.'css/style.css', false,$this->lac_v, 'all' );
		wp_register_style( 'lac_colorpicker', lac_url.'css/colpick.css', false,$this->lac_v, 'all' );
	}

	/**
	 * Enqueue All Needed Scripts
	 * @since 0.1
	 * @access public
	 */
	public function enqueue_script_style() {
		wp_enqueue_script( 'lac_script' );
		wp_enqueue_script( 'lac_colorpicker' );
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style( 'lac_style' );
		wp_enqueue_style( 'lac_colorpicker' );
		wp_enqueue_style("wp-jquery-ui-dialog");
	}

	/**
	 * Compile Scss Code
	 * @since 0.1
	 * @access public
	 */
	public function scss_compile() {
		$this->scss_complier->scss_code_generate();
		echo $this->scss_complier->scss_compile_code();
		exit();
	}
	
	/**
	 * Compiles & Save SCSS As CSS
	 * @since 0.1
	 * @access public
	 */
    public function scss_create() {
    	$this->scss_complier->scss_code_generate();
    	$this->scss_complier->scss_create();
    	exit();
    }

	/**
	 * Deletes The Given Style Name
	 * @since 0.1
	 * @access public 
	 */
    public function scss_delete(){
    	unlink(lac_style_path.$_REQUEST['slug'].'/'.$_REQUEST['slug'].'.css');
    	unlink(lac_style_path.$_REQUEST['slug'].'/'.$_REQUEST['slug'].'.scss');
    	if(rmdir(lac_style_path.$_REQUEST['slug'])){echo true;}
    	else {echo false;}
    	exit();
    }    
    /**
     * Reads File Meta And Returns As Array
     * @param string $file path of the file
     * @return array | boolean
     * @since 0.1
     * @access public
     */
    public function file_meta($file = ""){
    	$default_headers = array(
    			'name' => 'name',
    			'slug' => 'slug',
    			'ctime' => 'CreatedTime',
    			'cby' => 'CreatedBy',
    			'listing' => 'Listing',
    			'colors' => 'color'
    	);
    	return get_file_data( $file, $default_headers);
    }
    
    
    /**
     * Reads All Files & Folders From [lac_style_path]
     * @since 0.1
     * @access private
     * @return null
     */
    private function get_css_files(){
    	$Got_files = array();
    	$scanDir = scandir(lac_style_path);
    	foreach($scanDir as $folder){
    		if($folder == '.' || $folder == '..'){continue;}
    		$innser_scanDir = scandir(lac_style_path.$folder);
    		foreach($innser_scanDir as $InnerFile){
    			if($InnerFile == '.' || $InnerFile == '..'){continue;}
    			if($InnerFile == $folder.'.css'){ $Got_files[$folder] = lac_style_path.$folder.'/'.$InnerFile;}
    		}
    	}
    	$this->css_files = $Got_files;  
    	return $Got_files;  	
    }
    
    
    /**
     * Enques The Existing Styles To Profile Page
     * @since 0.1
     * @access public
     */
    public function enque_user_styles(){    	
    	$files = $this->get_css_files(); 
    	foreach($files as $fk => $f){
    		$MetaData = $this->file_meta($f);
    		$exColor = explode(",",$MetaData['colors']); 
    		$removeColor = array('#000000','#ffffff');
    		$actualColor = array_filter(str_replace($removeColor,'',$exColor));
    		$css_url = lac_style_url.$fk.'/'.$fk.'.css';
    		wp_admin_css_color(strtolower($MetaData['slug']),$MetaData['name'],$css_url,$actualColor,$actualColor);
    	}
    	  
		# Enque Plugin Default Styles 
		$url = lac_url.'default_themes/';
    	wp_admin_css_color('matt_black','Matt Black',$url.'Matt_Black/Matt_Black.css',array('#3b3b3b','#f0f0f0','#303030','#363536'),array('#3b3b3b','#f0f0f0','#303030','#363536'));
    	wp_admin_css_color('matt_blue','Matt Blue',$url.'Matt_Blue/Matt_Blue.css',array("#044370", "#edebed", "#065c91", "#0d6794", "#008bd1", "#045f8c", "#84c3e3", "#ffffff", "#249ad1", "#023e63", "#cbe6f2", "#276b8c", "#00a6d4", "#789cbf", "#24aed4", "#054375", "#005da8"),array("#044370", "#edebed", "#065c91", "#0d6794", "#008bd1", "#045f8c", "#84c3e3", "#ffffff", "#249ad1", "#023e63", "#cbe6f2", "#276b8c", "#00a6d4", "#789cbf", "#24aed4", "#054375", "#005da8"));
    	wp_admin_css_color('matt_green','Matt Green',$url.'Matt_Green/Matt_Green.css',array("#03821e", "#ebe8eb", "#0c6e31", "#01210d", "#03401a", "#095e30", "#03a843", "#7cdea8", "#edfaf3", "#c7c7c7", "#065e2e", "#056e34"),array("#03821e", "#ebe8eb", "#0c6e31", "#01210d", "#03401a", "#095e30", "#03a843", "#7cdea8", "#edfaf3", "#c7c7c7", "#065e2e", "#056e34"));
    	wp_admin_css_color('matt_pink','Matt Pink',$url.'Matt_Pink/Matt_Pink.css',array("#c7325c", "#edebec", "#d9184e", "#a80c38", "#e80948", "#c25775", "#7d1b39", "#ffadc5", "#700a27", "#ffffff", "#630422"),array("#c7325c", "#edebec", "#d9184e", "#a80c38", "#e80948", "#c25775", "#7d1b39", "#ffadc5", "#700a27", "#ffffff", "#630422"));
    	wp_admin_css_color('matt_red','Matt Red',$url.'Matt_Red/Matt_Red.css',array("#ba2d2d", "#ebe8eb", "#de1829", "#e3a8ad", "#780a13", "#a3000e", "#ebced1", "#ba9b9d", "#5e020a", "#bababa", "#f5f0f0", "#dedede", "#b00c0c"),array("#ba2d2d", "#ebe8eb", "#de1829", "#e3a8ad", "#780a13", "#a3000e", "#ebced1", "#ba9b9d", "#5e020a", "#bababa", "#f5f0f0", "#dedede", "#b00c0c"));
    	wp_admin_css_color('3_Color','3 Color',$url.'3_Color/3_Color.css',array("#1c141c", "#7a2602", "#bababa", "#541d05", "#912c00", "#ffffff", "#ffffff", "#5e1e02"),array("#1c141c", "#7a2602", "#bababa", "#541d05", "#912c00", "#ffffff", "#ffffff", "#5e1e02"));
    }

    /**
     * Check For Theme Edit Action.
     * @since 0.2
     * @access public
     */
    public function edit_exSCSS(){
        if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['theme']) ){
            $this->lac_pages->scss_output = $this->read_scss($_GET['theme']);    
        }
        
    }
    
    /**
     * Reads The Theme File.
     * @param   string Theme Slug
     * @returns Array
     * @since 0.2
     */
    public function read_scss($slug){
        $scss_file = lac_style_path.$slug.'/'.$slug.'.scss';
        $css_file = lac_style_path.$slug.'/'.$slug.'.css';
        
        if (! file_exists($scss_file)) {
            return null;
        }
        
        $name = $this->file_meta($css_file);
        $file = fopen($scss_file,"r");
        $contents = fread($file, filesize($scss_file));
        $search = array('$',':',';','
',' ');
        $replace = array('','=','','&','');
        $scss_variable = str_replace($search,$replace,$contents); 
        parse_str($scss_variable, $output);
        foreach($output as $opk => $opv){
            if($opv == null || $opv == 'null'){
                $output[$opk] = '';
            }
        }
        
        $output['file_base_name'] = $name['name'];
        return $output;
    }

}

/* 
 * Although it would be preferred to do this on hook,
 * load early to make sure Open Sans is removed
 */
$live_admin_customizer = new Live_Admin_Customizer; 
?>