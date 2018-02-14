<?php


/**
 *
 */
class Buzzpress_Main {

    private static $_instance;

	public $instance_global_settings;
	public $instance_wpextend_custom_field;
	public $instance_post_type_buzzpress;
	public $section_pc_buzzpress;

	static public $admin_url_import = 'buzzpress_import';
	static public $admin_url_export = 'buzzpress_export';




	/**
    * Static method which instance BuzzPress main class
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Buzzpress_Main();
        }
        return self::$_instance;
    }




	/**
    * Construct
    */
    private function __construct() {

		 if( WPEXTEND_ENABLE_SECTION ){ $this->section_pc_buzzpress = Buzzpress_Section_Pc::getInstance(); }
		 $this->instance_global_settings = Buzzpress_Global_Settings::getInstance();
		 $this->instance_wpextend_custom_field = Wpextend_Custom_Field::getInstance();
		 $this->instance_post_type_buzzpress = Buzzpress_Post_Type::getInstance();

		 add_action('admin_menu', array ( __CLASS__ ,  'define_admin_menu' ) );

		 // admin_enqueue_scripts
		 add_action('admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );

		 add_theme_support('post-thumbnails');
    }




	/**
    * Admin menu construction
    */
    public static function define_admin_menu() {

		add_menu_page(WPEXTEND_NAME_MENU_SETTINGS_EDITOR, WPEXTEND_NAME_MENU_SETTINGS_EDITOR, 'edit_posts', 'buzzpress_site_settings', array( Buzzpress_Global_Settings::getInstance(), 'render_admin_page' ), '', 3 );
		add_submenu_page('buzzpress_site_settings', 'Site settings', 'Site settings', 'edit_posts', 'buzzpress_site_settings', array( Buzzpress_Global_Settings::getInstance(), 'render_admin_page' ) );

		add_menu_page('BuzzPress', 'BuzzPress', 'manage_options', 'buzzpress', array( Buzzpress_Global_Settings::getInstance(), 'render_admin_page' ) );
		add_submenu_page('buzzpress', 'BuzzPress - Site settings', 'Site settings', 'manage_options', Buzzpress_Global_Settings::$admin_url, array( Buzzpress_Global_Settings::getInstance(), 'render_admin_page' ) );
		add_submenu_page('buzzpress', 'BuzzPress - Custom Post Type', 'Custom Post Type', 'manage_options', Buzzpress_Post_Type::$admin_url, array( Buzzpress_Post_Type::getInstance(), 'render_admin_page' ) );
		add_submenu_page('buzzpress', 'BuzzPress - Custom Fields', 'Custom Fields', 'manage_options', Wpextend_Custom_Field::$admin_url, array( Wpextend_Custom_Field::getInstance(), 'render_admin_page' ) );
		if( WPEXTEND_ENABLE_SECTION ){ add_submenu_page('buzzpress', 'BuzzPress - Section', 'Section', 'manage_options', Buzzpress_Section_Pc::$admin_url, array( Buzzpress_Section_Pc::getInstance(), 'render_admin_page' ) ); }
		add_submenu_page('buzzpress', 'BuzzPress - Export', 'Export', 'manage_options', Buzzpress_Main::$admin_url_export, array( Buzzpress_Main::getInstance(), 'render_export' ) );
		add_submenu_page('buzzpress', 'BuzzPress - Import', 'Import', 'manage_options', Buzzpress_Main::$admin_url_import, array( Buzzpress_Main::getInstance(), 'render_import' ) );
	 }





	/**
	* Wordpress Enqueues functions
	*
	*/
	public static function script_admin() {

		wp_enqueue_style( 'style_admin_mainBuzzpress', WPEXTEND_ASSETS_URL . 'style/admin/mainBuzzpress.css', false, true );
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

	 	$retour_html = '';

	 	// Global settings
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Global settings', 'buzzpress_global_settings_export', stripslashes( json_encode( Buzzpress_Global_Settings::getInstance()->buzzpress_global_settings, JSON_UNESCAPED_UNICODE ) ) );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		// Global settings values
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Global settings values', 'buzzpress_global_settings_value_export', stripslashes( json_encode( Buzzpress_Global_Settings::getInstance()->buzzpress_global_settings_values_to_export, JSON_UNESCAPED_UNICODE ) ) );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		// Custom post type
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Custom Post Type', 'buzzpress_custom_post_type_export', stripslashes( json_encode( Buzzpress_Post_Type::getInstance()->custom_post_type_buzzpress, JSON_UNESCAPED_UNICODE ) ) );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

	 	// Custom field
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Custom Field', 'wpextend_custom_field_value_export', stripslashes( json_encode( Wpextend_Custom_Field::getInstance()->Wpextend_Custom_Field, JSON_UNESCAPED_UNICODE ) ) );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		if( WPEXTEND_ENABLE_SECTION ){ 
			// Buzzpress Section Pc
			$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
			$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Section Pc', 'buzzpress_section_pc_value_export', stripslashes( json_encode( Buzzpress_Section_Pc::getInstance()->Buzzpress_Section_Pc, JSON_UNESCAPED_UNICODE ) ) );
			$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();
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
		$retour_html .= Buzzpress_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_buzzpress_global_settings', 'import_buzzpress_global_settings' );

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Global settings to import', 'buzzpress_global_settings_to_import' );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Import' );
		if( file_exists( WPEXTEND_DIR . '/inc/import/global_settings.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_buzzpress_global_settings', 'file' => 'global_settings'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_buzzpress_global_settings')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';

		// Formulaire d'import Global settings values
		$retour_html .= Buzzpress_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_buzzpress_global_settings_values', 'import_buzzpress_global_settings_values' );

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Global settings values to import', 'buzzpress_global_settings_values_to_import' );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Import' );
		if( file_exists( WPEXTEND_DIR . '/inc/import/global_settings_value.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_buzzpress_global_settings_values', 'file' => 'global_settings_value'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_buzzpress_global_settings_values')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';

		// Formulaire d'import Custom Post type
		$retour_html .= Buzzpress_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_buzzpress_custom_post_type', 'import_buzzpress_custom_post_type' );

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Custom Post Type to import', 'buzzpress_custom_post_type_to_import' );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Import' );
		if( file_exists( WPEXTEND_DIR . '/inc/import/custom_post_type.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_buzzpress_custom_post_type', 'file' => 'custom_post_type'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_buzzpress_custom_post_type')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';


	 	// Formulaire d'import Custom field
		$retour_html .= Buzzpress_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_wpextend_custom_field', 'import_wpextend_custom_field' );

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Custom Field to import', 'wpextend_custom_field_to_import' );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Import' );
		if( file_exists( WPEXTEND_DIR . '/inc/import/custom_field.json' ) ){
			$retour_html .= '<p><a href="' . add_query_arg( ['action' => 'import_wpextend_custom_field', 'file' => 'custom_field'] , wp_nonce_url(admin_url( 'admin-post.php' ), 'import_wpextend_custom_field')) . '" class="button" >Import JSON file</a></p>';
		}

		$retour_html .= '<br /><hr><br />';

		if( WPEXTEND_ENABLE_SECTION ){

			// Formulaire d'import Buzzpress Section Pc
			$retour_html .= Buzzpress_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'import_buzzpress_section_pc', 'import_buzzpress_section_pc' );

			$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
			$retour_html .= Buzzpress_Type_Field::render_input_textarea( 'Buzzpress Section Pc to import', 'buzzpress_section_pc_to_import' );
			$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

			$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Import' );
		}

	 	echo $retour_html;
	 }



}
