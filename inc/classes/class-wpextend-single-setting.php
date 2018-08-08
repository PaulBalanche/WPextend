<?php


/**
 *
 */
class Wpextend_Single_Setting {

    public $id;
    public $name;
    public $description;
    public $placeholder;
	public $category;
	public $type;
    public $options;
    public $value;
    public $repeatable;


	/**
	*
	*/
	public function __construct($id, $category) {

		$instance_settings_wpextend = Wpextend_Global_Settings::getInstance();
		if( array_key_exists($category, $instance_settings_wpextend->wpextend_global_settings) &&
			array_key_exists($id, $instance_settings_wpextend->wpextend_global_settings[$category]['fields'])
		) {

			$this->id = $id;
			$this->name = $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]['name'];
			$this->description = ( array_key_exists('description', $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]) ) ? $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]['description'] : '';
			$this->placeholder = '';
			$this->category = $category;
			$this->type = $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]['type'];
			$this->options = ( array_key_exists( 'options', $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]) ) ? $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]['options'] : false;
			$this->value = $instance_settings_wpextend->get( $id, $category );
			$this->repeatable = ( array_key_exists('repeatable', $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]) ) ? $instance_settings_wpextend->wpextend_global_settings[$category]['fields'][$id]['repeatable'] : false;

			// Pour les champs custom post
			if( $this->type == 'select_post_type' ){
				$list_custom_post = get_posts( array(
					'posts_per_page'   => -1,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'post_type'        => $this->options,
					'post_status'      => 'publish',
					'suppress_filters' => false
				));
				$this->options = [];
				foreach( $list_custom_post as $custom_post ){
					$this->options[$custom_post->ID] = $custom_post->post_title;
				}
			}
		}
	}







	/**
	 * Render HTML field depend on type
	 */
	public function render_html() {

		// Get info current screen
		$current_screen = get_current_screen();

		$retour_html = '';
		switch( $this->type ) {

			case 'text':
				$retour_html .= Wpextend_Type_Field::render_input_text( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->placeholder, $this->repeatable, $this->description );
				break;

			case 'textarea':
				$retour_html .= Wpextend_Type_Field::render_input_textarea( $this->name, 'textarea__fields__cat__'.$this->category.'__id__'.$this->id, $this->value, $this->repeatable, $this->description );
				break;

			case 'select':
				$retour_html .= Wpextend_Type_Field::render_input_select( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
				break;

			case 'select_post_type':
				$retour_html .= Wpextend_Type_Field::render_input_select( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
				break;

			case 'radio':
				$retour_html .= Wpextend_Type_Field::render_input_radio( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
				break;

			case 'link':
				$retour_html .= Wpextend_Type_Field::render_input_cta( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description );
				break;

			case 'checkbox':
				$retour_html .= Wpextend_Type_Field::render_input_checkbox( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
				break;

			case 'image':
				$retour_html .= Wpextend_Type_Field::render_input_image( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description );
				break;

			case 'gallery_image':
				$retour_html .= Wpextend_Type_Field::render_input_image_gallery( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->description, false );
				break;

			case 'file':
				$retour_html .= Wpextend_Type_Field::render_input_file( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description );
				break;
		}

		if($current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE){
			$retour_html .= '<tr><td><a href="'.add_query_arg( array( 'action' => 'delete_setting', 'category' => $this->category, 'key' => $this->id, '_wpnonce' => wp_create_nonce( 'delete_setting' ) ), admin_url( 'admin-post.php' ) ).'">Delete</a></td></tr>';
		}

		return $retour_html;
	}








	/**
	* Render form to add new field
	*/
	static public function render_form_create( $tab_list_category = array(), $key_category = false ){

		$retour_html = '<div class="form_add_setting_wpextend">';

		$retour_html .= '<input type="button" class="add_new_settings button button-primary" value="New setting">';

		$retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'add_settings_wpextend', '', 'hidden');

		$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
		$retour_html .= Wpextend_Type_Field::render_input_text( 'Name', 'name' );
		$retour_html .= Wpextend_Type_Field::render_input_text( 'Description', 'description' );
		$retour_html .= Wpextend_Type_Field::render_input_select('Type', 'type_field', Wpextend_Type_Field::get_available_fields() );
		$retour_html .= Wpextend_Type_Field::render_input_select('Post type select', 'post_type_options', Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress() );
		$retour_html .= Wpextend_Type_Field::render_input_checkbox( 'Repeatable ?', 'repeatable', array( 'true' => 'Ce champ pourra être dupliqué') );
		$retour_html .= Wpextend_Type_Field::render_input_select('Catégorie', 'category', $tab_list_category, $key_category );
		$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

		$retour_html .= Wpextend_Render_Admin_Html::form_close( 'Add settings' );

		$retour_html .= '</div>';

		return $retour_html;
	}







	/**
	* Get POST and create new setting field
	*
	* @return boolean
	*/
	static public function add_new() {


		// Check valid nonce
		check_admin_referer($_POST['action']);

		if( isset( $_POST['name'], $_POST['description'], $_POST['type_field'], $_POST['category'] ) ) {

			// Get Wpextend_Global_Settings instance
			$instance_global_settings = Wpextend_Global_Settings::getInstance();

			// Protect data
			$name 			= sanitize_text_field( $_POST['name'] );
			$description 	= sanitize_text_field( $_POST['description'] );
			$type 			= sanitize_text_field( $_POST['type_field'] );
			$options 		= false;
			$id_category 	= sanitize_text_field( $_POST['category'] );

			if( $type == 'select' || $type == 'radio' || $type == 'checkbox' ){
				$options = $_POST['options'];
			}
			if( is_array($options) ){
				foreach( $options as $key => $val ){
					$options[$key] = sanitize_text_field( $val );
				}
			}

			if( $type == 'select_post_type' && isset( $_POST['post_type_options'] ) ){
				$options = sanitize_text_field( $_POST['post_type_options'] );
			}

			// Champs repeatable
			$repeatable = ( isset($_POST['repeatable']) && is_array($_POST['repeatable']) && $_POST['repeatable'][0] == true ) ? true : false;

			// Add in Wpextend_Global_Settings
			$instance_global_settings->add_new_setting($name, $description, $type, $id_category, $options, $repeatable);

			// Save in Wordpress database
			$instance_global_settings->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
				exit;
			}
		}
	}







	static public function delete_setting(){

		// Check valid nonce
		check_admin_referer('delete_setting');

		if( isset( $_GET['category'], $_GET['key'] ) ) {

			// Get Wpextend_Global_Settings instance
			$instance_global_settings = Wpextend_Global_Settings::getInstance();

			// Protect data
			$category = sanitize_text_field( $_GET['category'] );
			$key = sanitize_text_field( $_GET['key'] );

			// Add in Wpextend_Global_Settings
			$instance_global_settings->remove_setting( $category, $key );

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
