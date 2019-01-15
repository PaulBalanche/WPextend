<?php


/**
 *
 */
class Wpextend_Main {

    private static $_instance;

	public $instance_global_settings;
	public $instance_post_type_wpextend;

	static public $admin_url_import = '_import';
	static public $admin_url_export = '_export';



	/**
    * Static method which instance Wpextend main class
    */
    public static function getInstance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new Wpextend_Main();
        }
        return self::$_instance;
    }




	/**
    * Construct
    */
    private function __construct() {

		$this->instance_global_settings = Wpextend_Global_Settings::getInstance();
		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE ){ $this->instance_post_type_wpextend = Wpextend_Post_Type::getInstance(); }

		add_action('admin_menu', array ( __CLASS__ ,  'define_admin_menu' ) );

		// admin_enqueue_scripts
		add_action('admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );

		add_theme_support('post-thumbnails');
    }




	/**
    * Admin menu construction
    */
    public static function define_admin_menu() {

		add_menu_page(WPEXTEND_NAME_MENU_SETTINGS_EDITOR, WPEXTEND_NAME_MENU_SETTINGS_EDITOR, 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( Wpextend_Global_Settings::getInstance(), 'render_admin_page' ), '', 3 );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', 'Site settings', 'Site settings', 'edit_posts', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_site_settings', array( Wpextend_Global_Settings::getInstance(), 'render_admin_page' ) );

		add_menu_page('WP Extend', 'WP Extend', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE, array( Wpextend_Global_Settings::getInstance(), 'render_admin_page' ) );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Site settings', 'Site settings', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Wpextend_Global_Settings::$admin_url, array( Wpextend_Global_Settings::getInstance(), 'render_admin_page' ) );
		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE ){ add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Custom Post Type', 'Custom Post Type', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Wpextend_Post_Type::$admin_url, array( Wpextend_Post_Type::getInstance(), 'render_admin_page' ) ); }
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Export', 'Export', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Wpextend_Main::$admin_url_export, array( Wpextend_Main::getInstance(), 'render_export' ) );
		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Import', 'Import', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . Wpextend_Main::$admin_url_import, array( Wpextend_Main::getInstance(), 'render_import' ) );
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
		$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
		$retour_html .= Wpextend_Type_Field::render_input_textarea( 'WP Extend Global settings', 'wpextend_global_settings_export', stripslashes( json_encode( Wpextend_Global_Settings::getInstance()->wpextend_global_settings, JSON_UNESCAPED_UNICODE ) ), false, '', false );
		$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

		// Global settings values
		$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
		$retour_html .= Wpextend_Type_Field::render_input_textarea( 'WP Extend Global settings values', 'wpextend_global_settings_value_export', Wpextend_Global_Settings::getInstance()->prepare_values_to_export(), false, '', false );
		$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE ){
			// Custom post type
			$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
			$retour_html .= Wpextend_Type_Field::render_input_textarea( 'WP Extend Custom Post Type', 'wpextend_custom_post_type_export', stripslashes( json_encode( Wpextend_Post_Type::getInstance()->custom_post_type_wpextend, JSON_UNESCAPED_UNICODE ) ), false, '', false );
			$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();
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
		$retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_global_settings', 'import_wpextend_global_settings' );

		$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
		$retour_html .= Wpextend_Type_Field::render_input_textarea( 'WP Extend Global settings to import', 'wpextend_global_settings_to_import', '', false, '', false );
		$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

		$retour_html .= Wpextend_Render_Admin_Html::form_close( 'Import' );
		if( file_exists( WPEXTEND_IMPORT_DIR . 'global_settings.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_global_settings', 'file' => 'global_settings'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_global_settings')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';

		// Formulaire d'import Global settings values
		$retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_global_settings_values', 'import_wpextend_global_settings_values' );

		$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
		$retour_html .= Wpextend_Type_Field::render_input_textarea( 'WP Extend Global settings values to import', 'wpextend_global_settings_values_to_import', '', false, '', false );
		$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

		$retour_html .= Wpextend_Render_Admin_Html::form_close( 'Import' );
		if( file_exists( WPEXTEND_IMPORT_DIR . 'global_settings_value.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_global_settings_values', 'file' => 'global_settings_value'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_global_settings_values')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';

		if( WPEXTEND_ENABLE_CUSTOM_POST_TYPE ){
			// Formulaire d'import Custom Post type
			$retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_custom_post_type', 'import_wpextend_custom_post_type' );

			$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
			$retour_html .= Wpextend_Type_Field::render_input_textarea( 'WP Extend Custom Post Type to import', 'wpextend_custom_post_type_to_import', '', false, '', false );
			$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

			$retour_html .= Wpextend_Render_Admin_Html::form_close( 'Import' );
			if( file_exists( WPEXTEND_IMPORT_DIR . 'custom_post_type.json' ) ){
				$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_custom_post_type', 'file' => 'custom_post_type'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_custom_post_type')) . '" class="button" >Import JSON file</a></p>';
			}

			$retour_html .= '<br /><hr><br />';
		}

	 	echo $retour_html;
	 }



}