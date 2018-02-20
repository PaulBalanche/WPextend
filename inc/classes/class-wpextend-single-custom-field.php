<?php
/**
 *
 */
class Wpextend_Single_Custom_Field {

	public $post_id;
	public $kez_metabox;
    public $key;
	public $data;
    public $default_value_field;
	public $options;
	public $repeatable;


	/**
	*
	*/
	 public function __construct($post_ID, $key_metabox, $key, $val, $default_value_field, $repeatable = false){

		 $this->post_id = $post_ID;
		 $this->key_metabox = $key_metabox;
		 $this->key=  $key;
		 $this->data = $val;
		 $this->default_value_field = $default_value_field;
		 $this->options = ( array_key_exists( 'options', $val ) ) ? $val['options'] : false;
		 $this->repeatable = $repeatable;

		 // Pour les champs custom post
		 if( $this->data['type'] == 'select_post_type' ){
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





	/**
 	* Render form to add new category
 	*/
 	static public function render_form_create($post_type, $key_category, $key_type, $key_metabox){

		$retour_html = '<div class="form_add_custom_field form_add_elt_buzzpress">';

		$retour_html .= '<input type="button" class="add_new_custom_field button button-primary" value="New field">';

 		$retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'add_custom_field_buzzpress', 'add_custom_field_buzzpress', 'hidden' );

 		$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
 		$retour_html .= Wpextend_Type_Field::render_input_text( 'Name', 'name' );
		$retour_html .= Wpextend_Type_Field::render_input_select('Type', 'type_field', Wpextend_Type_Field::get_available_fields() );
		$retour_html .= Wpextend_Type_Field::render_input_select('Post type select', 'post_type_options', Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress() );
		$retour_html .= Wpextend_Type_Field::render_input_hidden( 'post_type', $post_type );
		$retour_html .= Wpextend_Type_Field::render_input_hidden( 'category', $key_category );
		$retour_html .= Wpextend_Type_Field::render_input_hidden( 'type', $key_type );
		$retour_html .= Wpextend_Type_Field::render_input_hidden( 'metabox', $key_metabox );
		$retour_html .= Wpextend_Type_Field::render_input_checkbox( 'Repeatable ?', 'repeatable', array( 'true' => 'Ce champ pourra être dupliqué') );
		$retour_html .= Wpextend_Type_Field::render_input_checkbox( 'Indexable ?', 'indexable', array( 'true' => 'Ce champ pourra faire l\'objet d\'une recherche') );
 		$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

 		$retour_html .= Wpextend_Render_Admin_Html::form_close( 'Add field' );

		$retour_html .= '</div>';

 		return $retour_html;
 	}




	/**
	* Get POST and create new category
	*
	* @return boolean
	*/
	static public function DEPRECEATED_add_new() {

		// Check valid nonce
		check_admin_referer($_POST['action']);

		if( isset( $_POST['name'], $_POST['type'], $_POST['metabox'] ) ) {

			// Get Wpextend_Custom_Field instance
			$instance_wpextend_custom_field = Wpextend_Custom_Field::getInstance();

			// Protect data
			$name = sanitize_text_field( $_POST['name'] );
			$type = sanitize_text_field( $_POST['type'] );
			$options = false;
			$metabox = explode('&&', $_POST['metabox'], 2);
			$post_type = $metabox[0];
			$metabox = $metabox[1];
			$indexable = ( isset( $_POST['indexable'] ) && is_array( $_POST['indexable'] ) && $_POST['indexable'][0] == true ) ? true : false;

			if( $type == 'select' || $type == 'radio' || $type == 'checkbox' ){
				$options = $_POST['options'];
			}
			if( is_array($options) ){
				foreach( $options as $key => $val ){
					$options[$key] = sanitize_text_field( $val );
				}
			}

			if( $type == 'select_post_type' && isset( $_POST['post_type'] ) ){
				$options = sanitize_text_field( $_POST['post_type'] );
			}

			// Add in Wpextend_Custom_Field
			$instance_wpextend_custom_field->add_new_custom_field($name, $type, $post_type, $metabox, $options, $indexable);

			// Save in Wordpress database
			$instance_wpextend_custom_field->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}




	/**
	* Get POST and create new category
	*
	* @return boolean
	*/
	static public function delete(){

		if( isset( $_GET['post_type'], $_GET['spec_post_type'], $_GET['metabox'] ) ) {

			// Protect data
			$post_type = sanitize_text_field( $_GET['post_type'] );
			$spec_post_type = sanitize_text_field( $_GET['spec_post_type'] );
			$metabox = sanitize_text_field( $_GET['metabox'] );

			// Get Wpextend_Custom_Field instance
			$instance_wpextend_custom_field = Wpextend_Custom_Field::getInstance();

			if( isset( $_GET['id']) ){

				// Protect data
				$id = sanitize_text_field( $_GET['id'] );

				// Delete in Wpextend_Custom_Field
				$instance_wpextend_custom_field->delete_custom_field( $post_type, $spec_post_type, $metabox, $id );
			}
			else{

				// Delete in Wpextend_Custom_Field
				$instance_wpextend_custom_field->delete_metabox( $post_type, $spec_post_type, $metabox );
			}

			// Save in Wordpress database
			$instance_wpextend_custom_field->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}









	public function render_html_in_metabox(){

		$retour_html = '';
		
		switch( $this->data['type'] ){

			case 'text':
				$retour_html .= Wpextend_Type_Field::render_input_text( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->default_value_field, '', $this->repeatable );
				break;

			case 'textarea':
				$retour_html .= Wpextend_Type_Field::render_input_textarea( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->default_value_field );
				break;

			case 'select':
				$retour_html .= Wpextend_Type_Field::render_input_select( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->options, $this->default_value_field, $this->repeatable );
				break;

			case 'select_post_type':
				$retour_html .= Wpextend_Type_Field::render_input_select( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->options, $this->default_value_field, $this->repeatable );
				break;

			case 'radio':
				$retour_html .= Wpextend_Type_Field::render_input_radio( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->options, $this->default_value_field );
				break;

			case 'checkbox':
				$retour_html .= Wpextend_Type_Field::render_input_checkbox( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->options, $this->default_value_field );
				break;

			case 'link':
				$retour_html .= Wpextend_Type_Field::render_input_cta( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->default_value_field, $this->repeatable );
				break;

			case 'image':
				$retour_html .= Wpextend_Type_Field::render_input_image( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->default_value_field );
				break;

			case 'file':
				$retour_html .= Wpextend_Type_Field::render_input_file( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->default_value_field );
				break;

			case 'daterange':

				$retour_html .= Wpextend_Type_Field::render_input_daterange( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->default_value_field );
				break;

			case 'sliderrange':

				$retour_html .= Wpextend_Type_Field::render_input_sliderrange( $this->data['name'], $this->key_metabox.'['.$this->key.']', $this->default_value_field );
				break;

			case 'listing_section':

				$instance_post_parent = new Wpextend_Post( $this->post_id );
				$default_value_field = $instance_post_parent->get_sections_pc_buzzpress();

				$retour_html .= Wpextend_Section_Pc::listing_section( $this->post_id, $default_value_field );
				break;
		}

		return $retour_html;
	}






}
