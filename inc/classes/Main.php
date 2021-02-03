<?php

namespace Wpextend;

use \Wpextend\Package\AdminNotice;
use \Wpextend\Package\RenderAdminHtml;
use \Wpextend\Package\TypeField;
use \Wpextend\Package\Multilanguage;

/**
 *
 */
class Main {

    private static $_instance;

	public $instance_multilanguage,
	 $instance_global_settings,
	 $instance_post_type_wpextend,
	 $instanceGutenbergBlockWpextend,
	 $instance_timber_wpextend,
	 $instance_blade_wpextend,
	 $instance_thumbnail_api;

	static public $admin_url_import = '_import',
	 $admin_url_export = '_export';



	/**
    * Static method which instance Wpextend main class
    */
    public static function getInstance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new Main();
        }
        return self::$_instance;
    }




	/**
    * Construct
    */
    private function __construct() {

		AdminNotice::getInstance();
		Options::getInstance();
		$this->instance_multilanguage = Multilanguage::getInstance();
		$this->instance_global_settings = GlobalSettings::getInstance();
		if( Options::getInstance()->get_option('enable_gutenberg') ) {
			$this->instanceGutenbergBlockWpextend = GutenbergBlock::getInstance();

			if( defined('WPE_TEMPLATE_ENGINE') && WPE_TEMPLATE_ENGINE == 'timber' )
				$this->instance_timber_wpextend = Timber::getInstance();
			else if( defined('WPE_TEMPLATE_ENGINE') && WPE_TEMPLATE_ENGINE == 'blade' )
				$this->instance_blade_wpextend = Blade::getInstance();
		}
		if( Options::getInstance()->get_option('enable_custom_post_type') || ( Options::getInstance()->get_option('enable_gutenberg') && function_exists('acf_register_block') ) ){ $this->instance_post_type_wpextend = PostType::getInstance(); }
		if( Options::getInstance()->get_option('enable_thumbnail_api') ) { $this->instance_thumbnail_api = ThumbnailApi::getInstance(); }

		add_theme_support('post-thumbnails');

		// Configure hooks
        $this->create_hooks();
    }



	/**
	 * Register some Hooks
	 *
	 * @return void
	 */
	public function create_hooks() {

		add_action( 'admin_menu', array ( __CLASS__ ,  'define_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );
        add_action( 'admin_post_generate_autoload_json_file', 'Wpextend\Main::generate_autoload_json_file' );
	}
	


	/**
    * Admin menu construction
    */
    public static function define_admin_menu() {

		add_menu_page(Options::getInstance()->get_site_settings_name(), Options::getInstance()->get_site_settings_name(), 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( GlobalSettings::getInstance(), 'render_admin_page' ), '', 3 );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', 'Site settings', 'Site settings', 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( GlobalSettings::getInstance(), 'render_admin_page' ) );

		add_menu_page('WPE config', 'WPE config', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Options::$admin_url, array( Options::getInstance(), 'render_admin_page' ) );
		
		do_action( 'wpextend_define_admin_menu' );

		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Export', 'Export', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Main::$admin_url_export, array( Main::getInstance(), 'render_export' ) );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Import', 'Import', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Main::$admin_url_import, array( Main::getInstance(), 'render_import' ) );
	 }





	/**
	* Wordpress Enqueues functions
	*
	*/
	public static function script_admin() {

		wp_enqueue_style( 'style_admin_mainWpextend', WPEXTEND_ASSETS_URL . 'style/admin/style.min.css', false, true );
		wp_enqueue_style( 'style_jquery-ui', WPEXTEND_ASSETS_URL . 'style/admin/jquery-ui.min.css', false, true );
		wp_enqueue_style( 'style_jquery-ui-theme', WPEXTEND_ASSETS_URL . 'style/admin/jquery-ui.theme.min.css', false, true );

		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
	}



	 /**
	 * Render HTML export
	 *
	 */
	 public static function render_export() {

	 	// Header page & open form
		$retour_html = RenderAdminHtml::header('Export');

        $retour_html .= '<div class="mt-1 white">';

		// Global settings values
		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings values', 'wpextend_global_settings_value_export', GlobalSettings::getInstance()->prepare_values_to_export(), false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		if( Options::getInstance()->get_option('enable_custom_post_type') ) {
			// Custom post type
			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'WP Extend Custom Post Type', 'wpextend_custom_post_type_export', stripslashes( json_encode( PostType::getInstance()->get_all_from_database(), JSON_UNESCAPED_UNICODE ) ), false, '', false );
			$retour_html .= RenderAdminHtml::table_edit_close();
		}

		$retour_html .= '</div>';

	 	echo $retour_html;
	 }



	 /**
	 * Render HTML export
	 *
	 */
	 public static function render_import() {

	 	// Header page & open form
		$retour_html = RenderAdminHtml::header('Import');

		$retour_html .= '</div><div class="mt-1 white">';

		// Formulaire d'import Global settings values
		$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_global_settings_values', 'import_wpextend_global_settings_values' );

		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings values to import', 'wpextend_global_settings_values_to_import', '', false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		$retour_html .= RenderAdminHtml::form_close( 'Import', true );
		if( file_exists( WPEXTEND_IMPORT_DIR . 'global_settings_value.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_global_settings_values', 'file' => 'global_settings_value'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_global_settings_values')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '</div>';

	 	echo $retour_html;
	 }



	/**
	 * Function which create all missing JSON file
	 * 
	 */
	static public function generate_autoload_json_file() {

		check_admin_referer($_GET['action']);

		if( ! file_exists( WPEXTEND_JSON_DIR ) )
			mkdir( WPEXTEND_JSON_DIR, 0777, true );

		do_action( 'wpextend_generate_autoload_json_file' );

		wp_safe_redirect( wp_get_referer() ); 
		exit;
	}



	/**
     * Add common missing file
     * 
     */
    public static function add_notice_json_file_missing() {
		
		if( strpos($_SERVER['REQUEST_URI'], 'admin-post.php?action=') !== false )
			return;
			
        AdminNotice::add_notice( '001', 'Some JSON configuration files do not exist yet. Click <a href="' . add_query_arg( array( 'action' => 'generate_autoload_json_file', '_wpnonce' => wp_create_nonce( 'generate_autoload_json_file' ) ), admin_url( 'admin-post.php' ) ) . '">here</a> to generate them.', 'warning', false, true, AdminNotice::$prefix_admin_notice );
    }



}