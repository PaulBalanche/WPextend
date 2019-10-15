<?php

namespace Wpextend;

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
		Settings::getInstance();
		$this->instance_multilanguage = Multilanguage::getInstance();
		$this->instance_global_settings = GlobalSettings::getInstance();
		if( Settings::getInstance()->enable_gutenberg ) {
			$this->instanceGutenbergBlockWpextend = GutenbergBlock::getInstance();
			$this->instance_timber_wpextend = Timber::getInstance();
		}
		if( Settings::getInstance()->enable_custom_post_type || Settings::getInstance()->enable_gutenberg ){ $this->instance_post_type_wpextend = PostType::getInstance(); }
		if( Settings::getInstance()->enable_thumbnail_api ) { $this->instance_thumbnail_api = ThumbnailApi::getInstance(); }

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

		add_action('admin_menu', array ( __CLASS__ ,  'define_admin_menu' ) );
		add_action('admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );
        add_action( 'admin_post_generate_autoload_json_file', 'Wpextend\Main::generate_autoload_json_file' );
	}
	


	/**
    * Admin menu construction
    */
    public static function define_admin_menu() {

		add_menu_page(Settings::getInstance()->get_site_settings_name(), Settings::getInstance()->get_site_settings_name(), 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( GlobalSettings::getInstance(), 'render_admin_page' ), '', 3 );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', 'Site settings', 'Site settings', 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( GlobalSettings::getInstance(), 'render_admin_page' ) );

		add_menu_page('WPE config', 'WPE config', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE, array( GlobalSettings::getInstance(), 'render_admin_page' ) );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Site settings', 'Site settings', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . GlobalSettings::$admin_url, array( GlobalSettings::getInstance(), 'render_admin_page' ) );
		if( Settings::getInstance()->enable_custom_post_type ){ add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Custom Post Type', 'Custom Post Type', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . PostType::$admin_url, array( PostType::getInstance(), 'render_admin_page' ) ); }
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Settings', 'Settings', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Settings::$admin_url, array( Settings::getInstance(), 'render_admin_page' ) );
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

	 	// Global settings
		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings', 'wpextend_global_settings_export', stripslashes( json_encode( GlobalSettings::getInstance()->wpextend_global_settings, JSON_UNESCAPED_UNICODE ) ), false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		// Global settings values
		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings values', 'wpextend_global_settings_value_export', GlobalSettings::getInstance()->prepare_values_to_export(), false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		if( Settings::getInstance()->enable_custom_post_type ) {
			// Custom post type
			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'WP Extend Custom Post Type', 'wpextend_custom_post_type_export', stripslashes( json_encode( PostType::getInstance()->get_all_from_database(), JSON_UNESCAPED_UNICODE ) ), false, '', false );
			$retour_html .= RenderAdminHtml::table_edit_close();
		}

		if( Settings::getInstance()->enable_gutenberg ) {
			// Gutenberg blocks
			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'Gutenberg blocks', 'wpextend_gutenberg_blocks_export', stripslashes( GutenbergBlock::getInstance()->export_blocks_saved() ), false, '', false );
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

        $retour_html .= '<div class="mt-1 white">';

	 	// Formulaire d'import Global settings
		$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_global_settings', 'import_wpextend_global_settings' );

		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings to import', 'wpextend_global_settings_to_import', '', false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		$retour_html .= RenderAdminHtml::form_close( 'Import', true );
		if( file_exists( WPEXTEND_IMPORT_DIR . 'global_settings.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_global_settings', 'file' => 'global_settings'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_global_settings')) . '" class="button" >Import JSON file</a></p>';
		}

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

		$retour_html .= '</div><div class="mt-1 white">';

		if( Settings::getInstance()->enable_gutenberg ) {
			// Gutenberg blocks

			$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_' . GutenbergBlock::$gutenberg_name_custom_post_type, 'import_wpextend_' . GutenbergBlock::$gutenberg_name_custom_post_type );

			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'Gutenberg blocks to import', 'wpextend_' . GutenbergBlock::$gutenberg_name_custom_post_type . '_to_import', '', false, '', false );
			$retour_html .= RenderAdminHtml::table_edit_close();

			$retour_html .= RenderAdminHtml::form_close( 'Import', true );
			if( file_exists( WPEXTEND_IMPORT_DIR . GutenbergBlock::$gutenberg_name_custom_post_type . '.json' ) ){
				$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_' . GutenbergBlock::$gutenberg_name_custom_post_type, 'file' => GutenbergBlock::$gutenberg_name_custom_post_type] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_' . GutenbergBlock::$gutenberg_name_custom_post_type)) . '" class="button" >Import JSON file</a></p>';
			}
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

		
		if( ! file_exists( WPEXTEND_JSON_DIR ) ) {
			mkdir( WPEXTEND_JSON_DIR, 0777, true );
		}

		// echo get_stylesheet_directory() . '<br />';
		// echo WPEXTEND_JSON_DIR . '<br />';

		

		// $dirs_json_files = str_replace( get_stylesheet_directory() . '/', '', WPEXTEND_JSON_DIR );
		// echo $dirs_json_files;

		// $dirs_json_files = explode( '/', trim($dirs_json_files, '/') );
		// pre($dirs_json_files);

		// foreach( $dirs_json_files as $key => $val ) {

		// 	$path_current_dir = '';
		// 	for( $i = 0; $i <= $key; $i++ ) {
		// 		$path_current_dir .= '/' . $dirs_json_files[$i];
		// 	}

		// 	if( ! file_exists( get_stylesheet_directory() . $path_current_dir ) )
		// 	string $pathname [, int $mode = 0777 [, bool $recursive = FALSE
		// }

		exit;
	}



}