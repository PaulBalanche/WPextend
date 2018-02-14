<?php


/**
 *
 */
class Wpextend_Category_Settings {

    public $id;
    public $name;
    public $capabilities;
    public $wpml_compatible;
    public $position;
    public $list_settings;




	/**
	 *
	 */
	public function __construct($id) {

		$instance_settings_buzzpress = Wpextend_Global_Settings::getInstance();
		if( array_key_exists($id, $instance_settings_buzzpress->wpextend_global_settings) ) {

			$this->id = $id;
			$this->name = $instance_settings_buzzpress->wpextend_global_settings[$id]['name'];
			$this->capabilities = ( array_key_exists('capabilities', $instance_settings_buzzpress->wpextend_global_settings[$id]) && !empty($instance_settings_buzzpress->wpextend_global_settings[$id]['capabilities']) && $instance_settings_buzzpress->wpextend_global_settings[$id]['capabilities'] != null ) ? $instance_settings_buzzpress->wpextend_global_settings[$id]['capabilities'] : null;
			$this->wpml_compatible = ( array_key_exists('wpml_compatible', $instance_settings_buzzpress->wpextend_global_settings[$id]) && !empty($instance_settings_buzzpress->wpextend_global_settings[$id]['wpml_compatible']) && $instance_settings_buzzpress->wpextend_global_settings[$id]['wpml_compatible'] ) ? true : false;
			$this->list_settings = $instance_settings_buzzpress->wpextend_global_settings[$id]['fields'];
		}
	}




	/**
	* Render HTML field depend on type
	*/
	public function render_html() {

		$retour_html = Buzzpress_Render_Admin_Html::table_edit_open();
		foreach( $this->list_settings as $key => $val) {

			$instance_field_setting = new Buzzpress_Single_Setting($key, $this->id);
			$retour_html .= $instance_field_setting->render_html();
		}
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		return $retour_html;
	}





	/**
	* Render form to add new category
	*/
	static public function render_form_create(){

		$retour_html = Buzzpress_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'add_category_setting_buzzpress', 'add_category_setting_buzzpress' );

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_text( 'Name', 'name' );
		$retour_html .= Buzzpress_Type_Field::render_input_checkbox( 'Traduction ?', 'wpml_compatible', array( 'true' => 'Les champs doivent être traduisible') );
		$retour_html .= Buzzpress_Type_Field::render_input_select( 'Capabilities', 'capabilities', array( 'administrator' => 'Administrator', 'editor' => 'Editor' ) );
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Add Category' );

		return $retour_html;
	}





	/**
	* Get POST and create new category
	*
	* @return boolean
	*/
	static public function add_new() {

		// Check valid nonce
		check_admin_referer($_POST['action']);

		if( isset( $_POST['name'], $_POST['capabilities'] ) ) {

			// Get Wpextend_Global_Settings instance
			$instance_global_settings = Wpextend_Global_Settings::getInstance();

			// Protect data
			$name = sanitize_text_field( $_POST['name'] );
			$traduisible = ( isset( $_POST['wpml_compatible'] ) && is_array( $_POST['wpml_compatible'] ) && $_POST['wpml_compatible'][0] == true ) ? true : false;
			$capabilities = sanitize_text_field( $_POST['capabilities'] );

			// Add in Wpextend_Global_Settings
			$instance_global_settings->add_new_category($name, $traduisible, $capabilities);

			// Save in Wordpress database
			$instance_global_settings->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
				exit;
			}
		}
	}




	static public function delete_category_setting(){

		// Check valid nonce
		check_admin_referer('delete_setting');

		if( isset( $_GET['category'] ) ) {

			// Get Wpextend_Global_Settings instance
			$instance_global_settings = Wpextend_Global_Settings::getInstance();

			// Protect data
			$category = sanitize_text_field( $_GET['category'] );

			// Add in Wpextend_Global_Settings
			$instance_global_settings->remove_category_setting( $category );

			// Save in Wordpress database
			$instance_global_settings->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
				exit;
			}
		}
	}



}
