<?php


/**
*
*/
class Wpextend_Global_Settings {

	 private static $_instance;
	 public $wpextend_global_settings;
	 public $wpextend_global_settings_values = array();
	 public $wpextend_global_settings_values_to_export = array();
	 public $name_option_in_database = '_buzzpress_global_settings';
	 public $name_option_value_in_database = '_buzzpress_global_settings_value_';
	 public $WPML_default_langage = 'all';
	 public $WPML_current_langage = null;
	 public $WPML_langage;
	 static public $admin_url = 'buzzpress';



	/**
	* First instance of class Wpextend_Global_Settings
	*
	* @return object Wpextend_Global_Settings
	*/
	public static function getInstance() {
		 if (is_null(self::$_instance)) {
			  self::$_instance = new Wpextend_Global_Settings();
		 }
		 return self::$_instance;
	}



	/**
	* The constructor.
	*
	* @return void
	*/
	private function __construct() {

		// WPML initialisation
		$WPML_default_lang = apply_filters('wpml_default_language', NULL );
 		$this->WPML_current_langage = apply_filters( 'wpml_current_language', NULL );
		if( $this->WPML_current_langage && !empty($this->WPML_current_langage) && $this->WPML_current_langage != $WPML_default_lang )
			$this->WPML_langage = $this->WPML_current_langage;
		else
			$this->WPML_langage = $this->WPML_default_langage;



		// Set option from database
		$this->wpextend_global_settings = get_option( $this->name_option_in_database );
		if( !is_array( $this->wpextend_global_settings ) ) {
			$this->wpextend_global_settings = array();
		}


		foreach( $this->wpextend_global_settings as $key => $val){
			$value_in_database = get_option( $this->name_option_value_in_database . $key );

			$this->wpextend_global_settings[$key] = $value_in_database;

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
			if( !is_array($this->wpextend_global_settings_values[$key]) )
				$this->wpextend_global_settings_values[$key] = array();

			if( !is_array($this->wpextend_global_settings_values_to_export[$key]) )
				$this->wpextend_global_settings_values_to_export[$key] = array();
		}

		// Configure hooks
		$this->create_hooks();
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
	   	add_action( 'admin_post_update_settings_buzzpress', 'Wpextend_Global_Settings::udpate_values' );
	   	add_action( 'admin_post_add_category_setting_buzzpress', 'Wpextend_Category_Settings::add_new' );
	   	add_action( 'admin_post_add_settings_buzzpress', 'Wpextend_Single_Setting::add_new' );
		add_action( 'admin_post_delete_category_setting', 'Wpextend_Category_Settings::delete_category_setting' );
		add_action( 'admin_post_delete_setting', 'Wpextend_Single_Setting::delete_setting' );
		add_action( 'admin_post_import_wpextend_global_settings', array($this, 'import') );
		add_action( 'admin_post_import_wpextend_global_settings_values', array($this, 'import_values') );

	   	// AJAX $_POST traitment if necessary
	   	add_action( 'wp_ajax_update_settings_buzzpress', 'Wpextend_Global_Settings::udpate_values' );
	   	add_action( 'wp_ajax_add_category_setting_buzzpress', 'Wpextend_Category_Settings::add_new' );
	   	add_action( 'wp_ajax_add_settings_buzzpress', 'Wpextend_Single_Setting::add_new' );
	}



	/**
	* Wordpress Enqueues functions
	*
	*/
	public static function script_admin() {

		wp_enqueue_media();

		wp_enqueue_script( 'script_admin_wpextend_global_settings', WPEXTEND_ASSETS_URL . 'js/admin/site-settings.js', array('jquery'));
		wp_localize_script( 'script_admin_wpextend_global_settings', 'OBJECT', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		wp_enqueue_style( 'style_admin_wpextend_global_settings', WPEXTEND_ASSETS_URL . 'style/admin/site-settings.css', false, true );
	}



	/**
	* @param void $id
	*/
	public function get($id, $category) {

		if($id != null){

			$id = sanitize_title( $id );
			$category = sanitize_title( $category );

			if(
				is_array( $this->wpextend_global_settings_values ) &&
				array_key_exists($category, $this->wpextend_global_settings_values ) &&
				is_array( $this->wpextend_global_settings_values[$category] ) &&
				array_key_exists($id, $this->wpextend_global_settings_values[$category] ) &&
				(
					( $this->wpextend_global_settings[$category]['wpml_compatible'] == 1 && array_key_exists( $this->WPML_langage, $this->wpextend_global_settings_values[$category][$id]) ) ||
					( $this->wpextend_global_settings[$category]['wpml_compatible'] != 1 && array_key_exists( $this->WPML_default_langage, $this->wpextend_global_settings_values[$category][$id]) )
				)
			){

				if( $this->wpextend_global_settings[$category]['wpml_compatible'] == 1 ){

					if( $this->WPML_current_langage != null && array_key_exists($this->WPML_current_langage, $this->wpextend_global_settings_values[$category][$id]) )
						return $this->wpextend_global_settings_values[$category][$id][$this->WPML_current_langage];
					else
						return $this->wpextend_global_settings_values[$category][$id][$this->WPML_langage];
				}
				else
					return $this->wpextend_global_settings_values[$category][$id][$this->WPML_default_langage];
			}
		}
		else{

			$category = sanitize_title( $category );

			if(
				is_array( $this->wpextend_global_settings_values ) &&
				array_key_exists($category, $this->wpextend_global_settings_values ) &&
				is_array( $this->wpextend_global_settings_values[$category] )
			){
				$retour = array();
				foreach( $this->wpextend_global_settings_values[$category] as $key => $val ){
					if( $this->wpextend_global_settings[$category]['wpml_compatible'] == 1 ){
						
						if( $this->WPML_current_langage != null && array_key_exists($this->WPML_current_langage, $val) )
							$retour[$key] = $val[$this->WPML_current_langage];
						else
							$retour[$key] = $val[$this->WPML_langage];
					}
					else
						$retour[$key] = $val[$this->WPML_default_langage];
				}
				return $retour;
			}
		}

		return false;
	}



	/**
	* Get all settingd
	*/
	public function get_all_settings(){

		return $this->wpextend_global_settings_values;
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
		 $retour_html = Wpextend_Render_Admin_Html::header('Site settings');

		 $retour_html .= '<div class="accordionBuzzpress">';

		 // Get all categories to create fieldset
		 $all_category = $this->get_all_category();
		 foreach( $all_category as $key => $val) {

		 	$instance_category = new Wpextend_Category_Settings($key);
		 	if(
		 		$instance_category->capabilities == null ||
		 		$instance_category->capabilities == 'null' ||
		 		$instance_category->capabilities == 'editor' ||
		 		( current_user_can('manage_options') )
		 	){

			 	$retour_html .= '<h2>'.$val;
			 	if( $instance_category->wpml_compatible && !empty(apply_filters('wpml_current_language', NULL )) ){
			 		$retour_html .= ' ('.apply_filters('wpml_current_language', NULL ).')';
			 	}
			 	$retour_html .= '</h2><div>';

			 	if($current_screen->parent_base != 'buzzpress'){
					$retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'update_settings_buzzpress');
				}
				
				$retour_html .= Wpextend_Type_Field::render_input_hidden( 'category', $key );

				if($current_screen->parent_base == 'buzzpress'){
					$retour_html .= '<h2>'.$val.' (<a href="'.add_query_arg( array( 'action' => 'delete_category_setting', 'category' => $key, '_wpnonce' => wp_create_nonce( 'delete_setting' ) ), admin_url( 'admin-post.php' ) ).'">Delete</a>)</h2>';
				}

				$retour_html .= $instance_category->render_html();

				if($current_screen->parent_base != 'buzzpress'){
					$retour_html .= Wpextend_Render_Admin_Html::form_close();
				}

				if($current_screen->parent_base == 'buzzpress'){
				 	$retour_html .= '<hr>'.Wpextend_Single_Setting::render_form_create( $this->get_all_category(), $key );
			 	}
				$retour_html .= '</div>';
			}
		}

		$retour_html .= '</div>';

		// Add catergory form & add setting form
		if($current_screen->parent_base == 'buzzpress'){
			$retour_html .= '<fieldset class="fieldset_buzzpress"><h2>New settings category</h2>';
			$retour_html .= Wpextend_Category_Settings::render_form_create();
			$retour_html .= '</fieldset>';
		}

		// return
		echo $retour_html;
	}





	/**
 	* Update private variable $wpextend_global_settings to add new category
	*/
	public function add_new_category($name, $traduisible = false, $capabilities = false) {

		if( !empty($name) && is_bool($traduisible) ) {

			// Create ID
			$id_new_category = sanitize_title($name);

			// Test if no already exists
			if( !array_key_exists($id_new_category, $this->wpextend_global_settings) ) {
				$this->wpextend_global_settings[$id_new_category] = array('name' => $name, 'wpml_compatible' => $traduisible, 'capabilities' => $capabilities, 'fields' => array() );
			}
		}
	}





	/**
 	* Update private variable $wpextend_global_settings to add new setting
	*/
	public function add_new_setting($name, $description, $type, $id_category, $options = false, $repeatable = false) {

		if( !empty($name) &&
			array_key_exists( $type, Wpextend_Type_Field::get_available_fields() ) &&
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

			if( count($this->wpextend_global_settings) == 0)
				$this->wpextend_global_settings = false;

			return update_option( $this->name_option_in_database , $this->wpextend_global_settings);
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
	* Get POST and appli correspond function
	*
	* @return boolean
	*/
	static public function udpate_values() {

		check_admin_referer($_POST['action']);

		if( isset( $_POST['category'], $_POST['fields'] ) && is_array( $_POST['fields'] ) ){

			// Get Wpextend_Global_Settings instance
			$instance_global_settings = Wpextend_Global_Settings::getInstance();

			$all_category = $instance_global_settings->get_all_category();

			foreach( $_POST['fields'] as $key_category => $category ) {

				// First test if category exists
				if( array_key_exists( $key_category, $all_category ) && is_array( $category ) ){

					foreach( $category as $key_field => $value ){

						// Second test if setting exists
						if( array_key_exists( $key_field, $instance_global_settings->wpextend_global_settings[$key_category]['fields'] ) ){

							if( $instance_global_settings->wpextend_global_settings[$key_category]['wpml_compatible'] == 1 ){

								if( is_array($value) ){

									// Cleanning repeatble variable
									if($instance_global_settings->wpextend_global_settings[$key_category]['fields'][$key_field]['repeatable'] == 1){
										if($instance_global_settings->wpextend_global_settings[$key_category]['fields'][$key_field]['type'] == 'link'){
											foreach($value as $key_tab_link => $val_tab_link){
												if(count($value) > 1 && $val_tab_link['link'] == '' && $val_tab_link['label'] == ''){ unset($value[$key_tab_link]); }
											}
										}
									}

									$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_langage] = $value;
									if( $instance_global_settings->WPML_current_langage != null && $instance_global_settings->WPML_langage != $instance_global_settings->WPML_current_langage )
										$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_current_langage] = $value;
								}
								elseif( $instance_global_settings->wpextend_global_settings[$key_category]['fields'][$key_field]['type'] == 'textarea' ){
									$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_langage] = $value;
									if( $instance_global_settings->WPML_current_langage != null && $instance_global_settings->WPML_langage != $instance_global_settings->WPML_current_langage )
										$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_current_langage] = $value;
								}
								else{
									$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_langage] = sanitize_text_field($value);
									if( $instance_global_settings->WPML_current_langage != null && $instance_global_settings->WPML_langage != $instance_global_settings->WPML_current_langage )
										$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_current_langage] = sanitize_text_field($value);
								}
							}
							else{

								if( is_array($value) ){

									// Cleanning repeatble variable
									if($instance_global_settings->wpextend_global_settings[$key_category]['fields'][$key_field]['repeatable'] == 1){
										if($instance_global_settings->wpextend_global_settings[$key_category]['fields'][$key_field]['type'] == 'link'){
											foreach($value as $key_tab_link => $val_tab_link){
												if(count($value) > 1 && $val_tab_link['link'] == '' && $val_tab_link['label'] == ''){ unset($value[$key_tab_link]); }
											}
										}
									}

									$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_default_langage] = $value;
								}
								elseif( $instance_global_settings->wpextend_global_settings[$key_category]['fields'][$key_field]['type'] == 'textarea' ){
									$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_default_langage] = $value;
								}
								else{
									$instance_global_settings->wpextend_global_settings_values[$key_category][$key_field][$instance_global_settings->WPML_default_langage] = sanitize_text_field($value);
								}
							}
						}
					}

				}
			}

			// Save in Wordpress database
			$instance_global_settings->save( $_POST['category'] );

			$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
			wp_safe_redirect( $goback );
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
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' );
			$this->wpextend_global_settings = json_decode( $data_json_file, true );
		}
		else{
			exit;
		}

		// Save in Wordpress database
		if( is_array($this->wpextend_global_settings) ){

			$this->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
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
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' );
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
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}

}
