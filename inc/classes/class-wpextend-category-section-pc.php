<?php

/**
 *
 */
class Wpextend_Category_Section_Pc {

	public $list_sections;



	/**
	  *
	  */
	 public function __construct( $list_sections ){

		$this->list_sections = $list_sections;
	 }





	/**
	* Render form to add new category
	*/
  static public function render_form_create($post_type = false){

	  $retour_html = '<div class="form_add_category_section form_add_elt_wpextend">';

	  $retour_html .= '<input type="button" class="add_new_category_section button button-primary" value="New category">';

	  $retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'add_category_section_wpextend', 'add_category_section_wpextend', 'hidden' );

	  $retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
	  $retour_html .= Wpextend_Type_Field::render_input_text( 'Name', 'name' );
	  $retour_html .= Wpextend_Type_Field::render_input_select( 'Post type', 'post_type', Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress(), $post_type );
	  $retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

	  $retour_html .= Wpextend_Render_Admin_Html::form_close( 'Add category' );

	  $retour_html .= '</div>';

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

	  if( isset( $_POST['name'], $_POST['post_type'] ) ) {

		  // Get Wpextend_Section_Pc instance
		  $instance_Wpextend_Section_Pc = Wpextend_Section_Pc::getInstance();

		  // Protect data
		  $name = sanitize_text_field( $_POST['name'] );
		  $post_type = sanitize_text_field( $_POST['post_type'] );

		  // Add in Wpextend_Section_Pc
		  $instance_Wpextend_Section_Pc->add_new_category($name, $post_type);

		  // Save in Wordpress database
		  $instance_Wpextend_Section_Pc->save();

		  if( !isset( $_POST['ajax'] ) ) {
			  $goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
			  wp_safe_redirect( $goback );
		  }
		  exit;
	  }
  }



}
