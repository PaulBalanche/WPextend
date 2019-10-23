<?php

namespace Wpextend;

/**
*
*/
class GlobalSettings {

	private static $_instance;

	public $wpextend_global_settings,
	$wpextend_global_settings_values = array(),
	$name_option_in_database = '_buzzpress_global_settings',
	$name_option_value_in_database = '_buzzpress_global_settings_value_',
	$wordpress_default_locale = null,
	$wordpress_current_langage = null;

	static public $admin_url = '_admin_site_settings',
		$json_file_name = 'site_settings.json';



	/**
	* First instance of class GlobalSettings
	*
	* @return object GlobalSettings
	*/
	public static function getInstance() {

		 if (is_null(self::$_instance)) {
			  self::$_instance = new GlobalSettings();
		 }
		 else{
			// Mutlilanguages initialisation
			self::$_instance->multilanguages_initialisation();
		}

		 return self::$_instance;
	}



	/**
	* The constructor.
	*
	* @return void
	*/
	private function __construct() {

		// Mutlilanguages initialisation
		$this->multilanguages_initialisation();

		// Get site settings definition from database (legacy) and JSON file
		$this->load_site_settings();

		// Get value in database and assign to $wpextend_global_settings_values
		foreach( $this->wpextend_global_settings as $key => $val){
			$value_in_database = get_option( $this->name_option_value_in_database . $key );

			if(is_array($value_in_database)){
				foreach($value_in_database as $key2 => $val2){
					if(is_array($val2)){
						foreach($val2 as $key3 => $val3){
							$value_in_database[$key2][$key3] = str_replace('\\', '', $val3);
						}
					}
				}
			}

			$this->wpextend_global_settings_values[$key] = $value_in_database;
			if( ! is_array($this->wpextend_global_settings_values[$key]) )
				$this->wpextend_global_settings_values[$key] = array();
		}

		// Configure hooks
		$this->create_hooks();
	}



	/**
	* Mutlilanguages initialisation
	*
	*/
	public function multilanguages_initialisation(){

		// Get default locale
		$this->wordpress_default_locale = substr(Multilanguage::get_wplang(), 0, 2);

		// Get admin & front current language
		if( apply_filters( 'wpml_current_language', NULL ) ) {
			$this->wordpress_current_langage = apply_filters( 'wpml_current_language', NULL );
		}
		elseif( isset($_GET['lang']) && !empty($_GET['lang']) ){
			$this->wordpress_current_langage = $_GET['lang'];
		}
		else{
			$this->wordpress_current_langage = $this->wordpress_default_locale;
		}
	}



	/**
	 * Get site settings definition from database (legacy) and JSON file
	 * 
	 */
	public function load_site_settings() {

		// Getting from database (legacy)
		$this->wpextend_global_settings = get_option( $this->name_option_in_database );
		
		// Getting from JSON file
		if( file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {
			$site_settings_json_file_content = json_decode(file_get_contents(WPEXTEND_JSON_DIR . self::$json_file_name), true);

			if( is_array( $site_settings_json_file_content ) )
				$this->wpextend_global_settings = array_merge($this->wpextend_global_settings, $site_settings_json_file_content);
		}
		else
			AdminNotice::add_notice_json_file_missing();

		if( ! is_array( $this->wpextend_global_settings ) )
			$this->wpextend_global_settings = array();
	}



	/**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

		// admin_enqueue_scripts
		add_action('admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );

	   	// $_POST traitment if necessary
	   	add_action( 'admin_post_update_settings_wpextend', 'Wpextend\GlobalSettings::udpate_values' );
	   	add_action( 'admin_post_add_category_setting_wpextend', 'Wpextend\CategorySettings::add_new' );
	   	add_action( 'admin_post_add_settings_wpextend', 'Wpextend\SingleSetting::add_new' );
		add_action( 'admin_post_delete_category_setting', 'Wpextend\CategorySettings::delete_category_setting' );
		add_action( 'admin_post_delete_setting', 'Wpextend\SingleSetting::delete_setting' );
		add_action( 'admin_post_import_wpextend_global_settings', array($this, 'import') );
		add_action( 'admin_post_import_wpextend_global_settings_values', array($this, 'import_values') );

	   	// AJAX $_POST traitment if necessary
	   	add_action( 'wp_ajax_update_settings_wpextend', 'Wpextend\GlobalSettings::udpate_values' );
	   	add_action( 'wp_ajax_add_category_setting_wpextend', 'Wpextend\CategorySettings::add_new' );
		add_action( 'wp_ajax_add_settings_wpextend', 'Wpextend\SingleSetting::add_new' );

		add_action( 'wpextend_generate_autoload_json_file', array($this, 'generate_autoload_json_file') );

		// Add sub-menu page into WPExtend menu
		add_action( 'wpextend_define_admin_menu', array($this, 'define_admin_menu') );
	}
	


	/**
     * Add sub-menu page into WPExtend menu
     * 
     */
    public function define_admin_menu() {

		add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Site settings', 'Site settings', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . self::$admin_url, array( $this, 'render_admin_page' ) );
	}



	/**
	* Wordpress Enqueues functions
	*
	*/
	public static function script_admin() {

		wp_enqueue_media();

		wp_enqueue_script( 'script_admin_wpextend_global_settings', WPEXTEND_ASSETS_URL . 'js/admin/site-settings.js', array('jquery'));
		wp_localize_script( 'script_admin_wpextend_global_settings', 'OBJECT', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}



	/**
	 * Function to get accurate settings via $ID and $CATEGORY or tab category
	 * 
	 */
	static public function get($id, $category) {

		// Get GlobalSettings instance
		$instance_global_settings = GlobalSettings::getInstance();

		if($id != null){

			$id = sanitize_title( $id );
			$category = sanitize_title( $category );

			if(
				is_array( $instance_global_settings->wpextend_global_settings_values ) &&
				array_key_exists($category, $instance_global_settings->wpextend_global_settings_values ) &&
				is_array( $instance_global_settings->wpextend_global_settings_values[$category] ) &&
				array_key_exists($id, $instance_global_settings->wpextend_global_settings_values[$category] ) &&
				(
					( $instance_global_settings->wpextend_global_settings[$category]['wpml_compatible'] == 1 && array_key_exists( $instance_global_settings->wordpress_current_langage, $instance_global_settings->wpextend_global_settings_values[$category][$id]) ) ||
					( $instance_global_settings->wpextend_global_settings[$category]['wpml_compatible'] != 1 && array_key_exists( $instance_global_settings->wordpress_default_locale, $instance_global_settings->wpextend_global_settings_values[$category][$id]) )
				)
			){

				if(
					$instance_global_settings->wpextend_global_settings[$category]['wpml_compatible'] == 1 &&
					$instance_global_settings->wordpress_current_langage != null && array_key_exists($instance_global_settings->wordpress_current_langage, $instance_global_settings->wpextend_global_settings_values[$category][$id])
				){
					return $instance_global_settings->wpextend_global_settings_values[$category][$id][$instance_global_settings->wordpress_current_langage];
				}
				
				return $instance_global_settings->wpextend_global_settings_values[$category][$id][$instance_global_settings->wordpress_default_locale];
			}
		}
		else{

			$category = sanitize_title( $category );

			if(
				is_array( $instance_global_settings->wpextend_global_settings_values ) &&
				array_key_exists($category, $instance_global_settings->wpextend_global_settings_values ) &&
				is_array( $instance_global_settings->wpextend_global_settings_values[$category] )
			){
				$retour = array();
				foreach( $instance_global_settings->wpextend_global_settings_values[$category] as $key => $val ){
					if(
						$instance_global_settings->wpextend_global_settings[$category]['wpml_compatible'] == 1 &&
						$instance_global_settings->wordpress_current_langage != null && array_key_exists($instance_global_settings->wordpress_current_langage, $val)
					){
						$retour[$key] = $val[$instance_global_settings->wordpress_current_langage];
					}
					else
						$retour[$key] = $val[$instance_global_settings->wordpress_default_locale];
				}
				return $retour;
			}
		}

		return false;
	}



	/**
	* Get all settingd
	*/
	static public function get_all_settings(){

		// Get GlobalSettings instance
		$instance_global_settings = GlobalSettings::getInstance();

		return $instance_global_settings->wpextend_global_settings_values;
	}



	/**
 	* Retrieve all categories juste using loop in $this->wpextend_global_settings
	*
	* @return array
 	*/
 	public function get_all_category() {

 		$all_category = array();
 		if( is_array($this->wpextend_global_settings) ) {

 			foreach( $this->wpextend_global_settings as $key => $val ) {
 				$all_category[$key] = $val['name'];
 			}
 		}

		// Return categories
 		return $all_category;
 	}




	/**
    * Render HTML admin page
	*
	* @return string
	*/
	public function render_admin_page() {

		// Get info current screen
		$current_screen = get_current_screen();

		// Header page & open form
		$retour_html = RenderAdminHtml::header( Options::getInstance()->get_site_settings_name() );

		$retour_html .= '<div class="accordion_wpextend">';

		 // Get all categories to create fieldset
		 $all_category = $this->get_all_category();
		 foreach( $all_category as $key => $val) {

			$instance_category = new CategorySettings($key);
		 	if(
		 		(
					 $instance_category->capabilities == 'all' ||
					 current_user_can('manage_options')
				) &&
				(
					$current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE ||
					! $instance_category->wpml_compatible ||
					(
						$instance_category->wpml_compatible &&
						! empty( apply_filters('wpml_current_language', NULL) )
					)
				) &&
				(
					$current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE ||
					( $current_screen->parent_base != WPEXTEND_MAIN_SLUG_ADMIN_PAGE && is_array($instance_category->list_settings) && count($instance_category->list_settings) > 0 )
				)
		 	) {

			 	$retour_html .= '<h2>'.$val;
			 	if( $instance_category->wpml_compatible && !empty(apply_filters('wpml_current_language', NULL )) ){
			 		$retour_html .= ' ('. apply_filters('wpml_current_language', NULL ) .')';
				}
				 
				if( $current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE ){
					$retour_html .= ' ('. $instance_category->capabilities .')';
				}
			 	$retour_html .= '</h2><div>';

			 	if($current_screen->parent_base != WPEXTEND_MAIN_SLUG_ADMIN_PAGE){
					$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'update_settings_wpextend');
				}
				
				$retour_html .= TypeField::render_input_hidden( 'category', $key );

				$retour_html .= $instance_category->render_html();

				if( $current_screen->parent_base != WPEXTEND_MAIN_SLUG_ADMIN_PAGE ){
					$retour_html .= RenderAdminHtml::form_close('Save', true);
				}

				$retour_html .= '</div>';
			}
		}

		$retour_html .= '</div>';

		// Add catergory form & add setting form
		if( $current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE ) {
			$retour_html .= CategorySettings::render_form_create();
		}

		// return
		echo $retour_html;
	}





	/**
 	* Update private variable $wpextend_global_settings to add new category
	*/
	public function add_new_category($name, $traduisible = false, $capabilities = false) {

		if( ! empty($name) && is_bool($traduisible) ) {

			// Create ID
			$id_new_category = sanitize_title($name);

			// Test if no already exists
			if( ! array_key_exists($id_new_category, $this->wpextend_global_settings) ) {
				$this->wpextend_global_settings[$id_new_category] = array('name' => $name, 'wpml_compatible' => $traduisible, 'capabilities' => $capabilities, 'fields' => array() );
			}
		}
	}





	/**
 	* Update private variable $wpextend_global_settings to add new setting
	*/
	public function add_new_setting($name, $description, $type, $id_category, $options = false, $repeatable = false) {

		if( ! empty($name) &&
			array_key_exists( $type, TypeField::get_available_fields() ) &&
		 	array_key_exists( $id_category, $this->get_all_category() )
		) {

			// Create ID
			$id_new_setting = sanitize_title($name);

			// Test if no already exists
			if( !array_key_exists( $id_new_setting, $this->wpextend_global_settings[$id_category]['fields'] ) ){
				$this->wpextend_global_settings[$id_category]['fields'][$id_new_setting] = array('name' => $name, 'description' => $description, 'type' => $type, 'value' => null, 'repeatable' => $repeatable );
				if( $type == 'select' || $type == 'radio' || $type == 'checkbox' || $type ==  'select_post_type' ){
					$this->wpextend_global_settings[$id_category]['fields'][$id_new_setting]['options'] = $options;
				}
			}
		}
	}




	/**
	 * Use update_option to save in Wordpress database
	 *
	 * @return boolean
	 */
	public function save($key_category = false) {

		global $wp_rewrite;
		$wp_rewrite->flush_rules(false);

		if( $key_category == false ){

			if( file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {

				if( count($this->wpextend_global_settings) == 0)
					$this->wpextend_global_settings = [];

				return file_put_contents( WPEXTEND_JSON_DIR . self::$json_file_name, json_encode($this->wpextend_global_settings, JSON_PRETTY_PRINT) );
			}
			else{

				if( count($this->wpextend_global_settings) == 0)
					$this->wpextend_global_settings = false;

				return update_option( $this->name_option_in_database , $this->wpextend_global_settings);
			}
		}
		else{

			if( array_key_exists($key_category, $this->wpextend_global_settings_values) ){
				if( count($this->wpextend_global_settings_values[$key_category]) == 0)
					$this->wpextend_global_settings_values[$key_category] = false;

				return update_option( $this->name_option_value_in_database . $key_category, $this->wpextend_global_settings_values[$key_category] );
			}
		}
	}



	/**
	* Get POST and apply correspond function
	*
	* @return boolean
	*/
	static public function udpate_values() {

		check_admin_referer($_POST['action']);

		if( isset( $_POST['category'] ) ){

			// Textarea Traitement to include them in related fields
			foreach( $_POST as $key => $val ){
				if( preg_match( '/textarea__fields__cat__(.*)__id__(.*)/', $key, $matches ) ){
					if( is_array($matches) && count($matches) == 3){
						$_POST['fields'][ $matches[1] ][ $matches[2] ] = $val;
					}
				}
			}

			if( isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ){
				foreach( $_POST['fields'] as $key_category => $category ) {

					// First test if category exists
					if( array_key_exists( $key_category, GlobalSettings::getInstance()->get_all_category() ) && is_array( $category ) ){

						foreach( $category as $key_field => $value ){

							// Second test if setting exists
							if( array_key_exists( $key_field, GlobalSettings::getInstance()->wpextend_global_settings[$key_category]['fields'] ) ){

								// Cleanning repeatable variable
								if( is_array($value) && GlobalSettings::getInstance()->wpextend_global_settings[$key_category]['fields'][$key_field]['repeatable'] == 1 && count($value) > 1 ){

									$element_is_link = ( GlobalSettings::getInstance()->wpextend_global_settings[$key_category]['fields'][$key_field]['type'] == 'link' ) ? true : false;
									$value = Helper::clean_repeatable_element($value, $element_is_link);
								}
								
								// Sanitize value
								if( !is_array($value) && GlobalSettings::getInstance()->wpextend_global_settings[$key_category]['fields'][$key_field]['type'] != 'textarea' ) {
									$value = sanitize_text_field($value);
								}

								// Define key language
								$key_language = ( GlobalSettings::getInstance()->wpextend_global_settings[$key_category]['wpml_compatible'] == 1 ) ? GlobalSettings::getInstance()->wordpress_current_langage : GlobalSettings::getInstance()->wordpress_default_locale;

								// Update value in Global settings
								GlobalSettings::getInstance()->wpextend_global_settings_values[$key_category][$key_field][$key_language] = $value;
							}
						}
					}
				}
			}

			// Save in Wordpress database
			if( GlobalSettings::getInstance()->save( $_POST['category'] ) )
				AdminNotice::add_notice( '002', 'The changes have been saved.', 'success', true, true, AdminNotice::$prefix_admin_notice );

			wp_safe_redirect( wp_get_referer() );
			exit;
		}
	}



	public function remove_category_setting($category){

		if( array_key_exists( $category, $this->wpextend_global_settings ) ){
			unset( $this->wpextend_global_settings[$category] );
			unset( $this->wpextend_global_settings_values[$category] );

			$this->save( $category );
		}
	}




	public function remove_setting( $category, $key ){

		if( array_key_exists( $category, $this->wpextend_global_settings ) && array_key_exists( $key, $this->wpextend_global_settings[$category]['fields'] ) ){
			unset( $this->wpextend_global_settings[$category]['fields'][$key] );
			unset( $this->wpextend_global_settings_values[$category][$key] );

			$this->save( $category );
		}
	}


	public function import(){
		
		// Check valid nonce
		$action_nonce = ( isset($_GET['action']) ) ? $_GET['action'] : $_POST['action'];
		check_admin_referer($action_nonce);

		// Get new data to import
		if( isset( $_POST['wpextend_global_settings_to_import'] ) && !empty($_POST['wpextend_global_settings_to_import']) ) {

			$this->wpextend_global_settings = json_decode( stripslashes($_POST['wpextend_global_settings_to_import']), true );
		}
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' );
			$this->wpextend_global_settings = json_decode( $data_json_file, true );
		}
		else{
			exit;
		}

		// Save in Wordpress database
		if( is_array($this->wpextend_global_settings) ){

			$this->save();

			if( !isset( $_POST['ajax'] ) ) {

				AdminNotice::add_notice( '005', 'File successfully imported.', 'success', true, true, AdminNotice::$prefix_admin_notice );
				
				wp_safe_redirect( wp_get_referer() );
			}
			exit;
		}
	}


	
	public function import_values(){
		
		// Check valid nonce
		$action_nonce = ( isset($_GET['action']) ) ? $_GET['action'] : $_POST['action'];
		check_admin_referer($action_nonce);

		// Get new data to import
		if( isset( $_POST['wpextend_global_settings_values_to_import'] ) && !empty($_POST['wpextend_global_settings_values_to_import']) ) {

			$this->wpextend_global_settings_values = json_decode( stripslashes($_POST['wpextend_global_settings_values_to_import']), true );
		}
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' );
			$this->wpextend_global_settings_values = json_decode( $data_json_file, true );
		}
		else{
			exit;
		}

		// Save in Wordpress database
		if( is_array($this->wpextend_global_settings) && is_array($this->wpextend_global_settings_values) ){

			foreach( $this->wpextend_global_settings as $key => $val ){
				$this->save($key);
			}

			if( !isset( $_POST['ajax'] ) ) {

				AdminNotice::add_notice( '006', 'Value successfully imported.', 'success', true, true, AdminNotice::$prefix_admin_notice );

				wp_safe_redirect( wp_get_referer() );
			}
			exit;
		}
	}



	public function prepare_values_to_export(){

		$values_to_export = $this->wpextend_global_settings_values;

		foreach($values_to_export as $key => $val){
			foreach($val as $key2 => $val2){
				foreach($val2 as $key3 => $val3){

					$val3_temp = $val3;
					
					if( !is_array($val3_temp) ) {
						$val3_temp = apply_filters('the_content', $val3 );
						$val3_temp = preg_replace('/\n/', '', $val3_temp);
						
						$val3_temp = html_entity_decode($val3_temp, ENT_NOQUOTES | ENT_HTML5, 'UTF-8');

						$val3_temp = str_replace('“', '"', $val3_temp);
						$val3_temp = str_replace('”', '"', $val3_temp);
						$val3_temp = str_replace('’', '\'', $val3_temp);
						$val3_temp = str_replace('"', '\"', $val3_temp);
					}

					$values_to_export[$key][$key2][$key3] = $val3_temp;
				}
			}
		}

		$values_to_export = json_encode( $values_to_export, JSON_UNESCAPED_UNICODE );

		return $values_to_export;
	}



	/**
	 * Create JSON file if doesn't exist
	 * 
	 */
	public function generate_autoload_json_file() {

		if( ! file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {
			
			if( touch(WPEXTEND_JSON_DIR . self::$json_file_name) )
				AdminNotice::add_notice( '013', self::$json_file_name .' file successfully created.', 'success', true, true, AdminNotice::$prefix_admin_notice );
			else
				AdminNotice::add_notice( '014', 'unable to create ' . self::$json_file_name, 'error', true, true, AdminNotice::$prefix_admin_notice );
		}
    }



}