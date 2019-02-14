<?php

namespace Wpextend;

/**
 *
 */
class CategorySettings {

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

		$instance_settings_Wpextend = GlobalSettings::getInstance();
		if( array_key_exists($id, $instance_settings_Wpextend->wpextend_global_settings) ) {

			$this->id = $id;
			$this->name = $instance_settings_Wpextend->wpextend_global_settings[$id]['name'];
			$this->capabilities = ( array_key_exists('capabilities', $instance_settings_Wpextend->wpextend_global_settings[$id]) && !empty($instance_settings_Wpextend->wpextend_global_settings[$id]['capabilities']) ) ? $instance_settings_Wpextend->wpextend_global_settings[$id]['capabilities'] : 'all';
			$this->wpml_compatible = ( array_key_exists('wpml_compatible', $instance_settings_Wpextend->wpextend_global_settings[$id]) && !empty($instance_settings_Wpextend->wpextend_global_settings[$id]['wpml_compatible']) && $instance_settings_Wpextend->wpextend_global_settings[$id]['wpml_compatible'] ) ? true : false;
			$this->list_settings = $instance_settings_Wpextend->wpextend_global_settings[$id]['fields'];
		}
	}




	/**
	* Render HTML field depend on type
	*/
	public function render_html() {

		// Get info current screen
		$current_screen = get_current_screen();
		
		$retour_html = RenderAdminHtml::table_edit_open();
		if($current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE){ $Wpextend_List_Table_data = []; }
		foreach( $this->list_settings as $key => $val) {

			if($current_screen->parent_base != WPEXTEND_MAIN_SLUG_ADMIN_PAGE){
				$instance_field_setting = new SingleSetting($key, $this->id);
				$retour_html .= $instance_field_setting->render_html();
			}
			else{
				$Wpextend_List_Table_data[] = array_merge($val, [
					'action_delete' => [
						'action' => 'delete_setting',
						'category' => $this->id,
						'key' => $key,
						'_wpnonce' => wp_create_nonce( 'delete_setting' )
					]
				] );
			}
		}

		if($current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE){
			ob_start();
			$args_Wpextend_List_Table = [
				'data' 		=> $Wpextend_List_Table_data,
				'columns'	=> [
					'name'			=> 'Name',
					'description' 	=> 'Description',
					'type' 			=> 'Type',
					'repeatable' 	=> 'Repeatable'
				],
				'per_page'	=> 200
			];
			$Wpextend_List_Table = new ListTable($args_Wpextend_List_Table);
			$Wpextend_List_Table->prepare_items();
			$Wpextend_List_Table->display();
			$retour_html .= ob_get_contents();
			ob_end_clean();
		}

		$retour_html .= RenderAdminHtml::table_edit_close();

		return $retour_html;
	}





	/**
	* Render form to add new category
	*/
	static public function render_form_create(){

		$retour_html = RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'add_category_setting_wpextend', 'add_category_setting_wpextend' );

		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= TypeField::render_input_text( __('Name', WPEXTEND_TEXTDOMAIN), 'name' );
		$retour_html .= TypeField::render_input_checkbox( __('Multilanguage compatibility', WPEXTEND_TEXTDOMAIN), 'wpml_compatible', array( 'true' => __('Must be multilingual?', WPEXTEND_TEXTDOMAIN) ) );
		$retour_html .= TypeField::render_input_radio( __('Capabilities', WPEXTEND_TEXTDOMAIN), 'capabilities', array( 'all' => __('Everyone', WPEXTEND_TEXTDOMAIN), 'only_administrator' => __('Only administrator', WPEXTEND_TEXTDOMAIN) ), 'all' );
		$retour_html .= RenderAdminHtml::table_edit_close();

		$retour_html .= RenderAdminHtml::form_close( __('Add category', WPEXTEND_TEXTDOMAIN) );

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

			// Get GlobalSettings instance
			$instance_global_settings = GlobalSettings::getInstance();

			// Protect data
			$name = sanitize_text_field( $_POST['name'] );
			$traduisible = ( isset( $_POST['wpml_compatible'] ) && is_array( $_POST['wpml_compatible'] ) && $_POST['wpml_compatible'][0] == true ) ? true : false;
			$capabilities = sanitize_text_field( $_POST['capabilities'] );

			// Add in GlobalSettings
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
		check_admin_referer('delete_category_setting');

		if( isset( $_GET['category'] ) ) {

			// Get GlobalSettings instance
			$instance_global_settings = GlobalSettings::getInstance();

			// Protect data
			$category = sanitize_text_field( $_GET['category'] );

			// Add in GlobalSettings
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
