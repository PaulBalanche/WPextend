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
	$instance_timber_wpextend;

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

		$this->instance_multilanguage = Multilanguage::getInstance();
		$this->instance_global_settings = GlobalSettings::getInstance();
		if( WPEXTEND_ENABLE_GUTENBERG ) {
			$this->instanceGutenbergBlockWpextend = GutenbergBlock::getInstance();
			$this->instance_timber_wpextend = Timber::getInstance();
		}
		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE || WPEXTEND_ENABLE_GUTENBERG ){ $this->instance_post_type_wpextend = PostType::getInstance(); }

		add_action('admin_menu', array ( __CLASS__ ,  'define_admin_menu' ) );

		// admin_enqueue_scripts
		add_action('admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );

		add_theme_support('post-thumbnails');
    }




	/**
    * Admin menu construction
    */
    public static function define_admin_menu() {

		add_menu_page(GlobalSettings::getInstance()->title_page_editor_global_settings, GlobalSettings::getInstance()->title_page_editor_global_settings, 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( GlobalSettings::getInstance(), 'render_admin_page' ), '', 3 );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', 'Site settings', 'Site settings', 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( GlobalSettings::getInstance(), 'render_admin_page' ) );

		add_menu_page('WP Extend', 'WP Extend', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE, array( GlobalSettings::getInstance(), 'render_admin_page' ) );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Site settings', 'Site settings', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . GlobalSettings::$admin_url, array( GlobalSettings::getInstance(), 'render_admin_page' ) );
		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE ){ add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Custom Post Type', 'Custom Post Type', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . PostType::$admin_url, array( PostType::getInstance(), 'render_admin_page' ) ); }
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Export', 'Export', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Main::$admin_url_export, array( Main::getInstance(), 'render_export' ) );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Import', 'Import', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Main::$admin_url_import, array( Main::getInstance(), 'render_import' ) );
	 }





	/**
	* Wordpress Enqueues functions
	*
	*/
	public static function script_admin() {

		wp_enqueue_style( 'style_admin_mainWpextend', WPEXTEND_ASSETS_URL . 'style/admin/mainWpextend.css', false, true );
		wp_enqueue_style( 'style_jquery-ui', WPEXTEND_ASSETS_URL . 'style/admin/jquery-ui.min.css', false, true );
		wp_enqueue_style( 'style_jquery-ui-theme', WPEXTEND_ASSETS_URL . 'style/admin/jquery-ui.theme.min.css', false, true );

		// Wpextend_Custom_Field Style and Script 
		wp_enqueue_script( 'script_admin_wpextend_custom_field_setting_page', WPEXTEND_ASSETS_URL . 'js/admin/custom_field_setting_page.js', array('jquery'));

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

	 	$retour_html = '';

	 	// Global settings
		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings', 'wpextend_global_settings_export', stripslashes( json_encode( GlobalSettings::getInstance()->wpextend_global_settings, JSON_UNESCAPED_UNICODE ) ), false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		// Global settings values
		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings values', 'wpextend_global_settings_value_export', GlobalSettings::getInstance()->prepare_values_to_export(), false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE ) {
			// Custom post type
			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'WP Extend Custom Post Type', 'wpextend_custom_post_type_export', stripslashes( json_encode( PostType::getInstance()->custom_post_type_wpextend, JSON_UNESCAPED_UNICODE ) ), false, '', false );
			$retour_html .= RenderAdminHtml::table_edit_close();
		}

		if( WPEXTEND_ENABLE_GUTENBERG ) {
			// Gutenberg blocks
			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'Gutenberg blocks', 'wpextend_gutenberg_blocks_export', stripslashes( GutenbergBlock::getInstance()->export_blocks_saved() ), false, '', false );
			$retour_html .= RenderAdminHtml::table_edit_close();
		}

	 	echo $retour_html;
	 }



	 /**
	 * Render HTML export
	 *
	 */
	 public static function render_import() {

	 	$retour_html = '';

	 	// Formulaire d'import Global settings
		$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_global_settings', 'import_wpextend_global_settings' );

		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings to import', 'wpextend_global_settings_to_import', '', false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		$retour_html .= RenderAdminHtml::form_close( 'Import' );
		if( file_exists( WPEXTEND_IMPORT_DIR . 'global_settings.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_global_settings', 'file' => 'global_settings'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_global_settings')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';

		// Formulaire d'import Global settings values
		$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_global_settings_values', 'import_wpextend_global_settings_values' );

		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_textarea( 'WP Extend Global settings values to import', 'wpextend_global_settings_values_to_import', '', false, '', false );
		$retour_html .= RenderAdminHtml::table_edit_close();

		$retour_html .= RenderAdminHtml::form_close( 'Import' );
		if( file_exists( WPEXTEND_IMPORT_DIR . 'global_settings_value.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_global_settings_values', 'file' => 'global_settings_value'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_global_settings_values')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';

		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE ){
			// Formulaire d'import Custom Post type
			$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_custom_post_type', 'import_wpextend_custom_post_type' );

			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'WP Extend Custom Post Type to import', 'wpextend_custom_post_type_to_import', '', false, '', false );
			$retour_html .= RenderAdminHtml::table_edit_close();

			$retour_html .= RenderAdminHtml::form_close( 'Import' );
			if( file_exists( WPEXTEND_IMPORT_DIR . 'custom_post_type.json' ) ){
				$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_custom_post_type', 'file' => 'custom_post_type'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_custom_post_type')) . '" class="button" >Import JSON file</a></p>';
			}

			$retour_html .= '<br /><hr><br />';
		}

		if( WPEXTEND_ENABLE_GUTENBERG ) {
			// Gutenberg blocks

			$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_gutenberg_blocks', 'import_wpextend_gutenberg_blocks' );

			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_textarea( 'Gutenberg blocks to import', 'wpextend_gutenberg_blocks_to_import', '', false, '', false );
			$retour_html .= RenderAdminHtml::table_edit_close();

			$retour_html .= RenderAdminHtml::form_close( 'Import' );
			if( file_exists( WPEXTEND_IMPORT_DIR . 'gutenberg_blocks.json' ) ){
				$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_gutenberg_blocks', 'file' => 'gutenberg_blocks'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_gutenberg_blocks')) . '" class="button" >Import JSON file</a></p>';
			}
		}

	 	echo $retour_html;
	 }



}