<?php


/**
 *
 */
class Wpextend_Type_Section_Pc {


	public $type_post;
	public $category;
	public $id;
	public $data;




	/**
	 *
	 */
	public function __construct($type_post, $category, $id, $data){

		$this->type_post = $type_post;
		$this->category = $category;
		$this->id = $id;
		$this->data = $data;

		$this->alias = ( array_key_exists('alias', $data ) ) ? $data['alias'] : 'none';
	}





	/**
	* Render form to add new category
	*/
  static public function render_form_create($category = false){

	  $retour_html = '<div class="form_add_section form_add_elt_wpextend">';

	  $retour_html .= '<input type="button" class="add_new_section button button-primary" value="New section">';

	  $retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'add_type_section_wpextend', 'add_type_section_wpextend', 'hidden' );

	  $retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
	  $retour_html .= Wpextend_Type_Field::render_input_text( 'Name', 'name' );
	  $retour_html .= Wpextend_Type_Field::render_input_text( 'Description', 'description' );
	  $retour_html .= Wpextend_Type_Field::render_input_select('Category', 'category', Wpextend_Section_Pc::getInstance()->get_all_category(), $category );
	  $retour_html .= Wpextend_Type_Field::render_input_select('File controller / view', 'file_controller_view', Wpextend_Section_Pc::getInstance()->scan_views_controllers() );
	  $retour_html .= Wpextend_Type_Field::render_input_select( 'Est un alias', 'alias', array_merge( array('none' => 'None'), Wpextend_Section_Pc::getInstance()->get_all_type_section_pc(true) ) );
	  $retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

	  $retour_html .= Wpextend_Render_Admin_Html::form_close( 'Add section' );

	  $retour_html .= '</div>';

	  return $retour_html;
  }




  /**
  * Render form to add new category
  */
 public function render_form_edit(){

	 $retour_html = '<hr>';
	 $retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'update_type_section_wpextend', 'update_type_section_wpextend' );

	 $retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
	 $retour_html .= Wpextend_Type_Field::render_input_hidden( 'id', $this->id );
	 $retour_html .= Wpextend_Type_Field::render_input_text( 'Name', 'name', $this->data['name'] );
	 $retour_html .= Wpextend_Type_Field::render_input_text( 'Description', 'description', $this->data['description'] );
	 $retour_html .= Wpextend_Type_Field::render_input_select('Category', 'category', Wpextend_Section_Pc::getInstance()->get_all_category(), $this->type_post.'__'.$this->category );
	 $retour_html .= Wpextend_Type_Field::render_input_select('File controller / view', 'file_controller_view', Wpextend_Section_Pc::getInstance()->scan_views_controllers(), $this->data['file'] );
	 $retour_html .= Wpextend_Type_Field::render_input_select( 'Est un alias', 'alias', array_merge( array('none' => 'None'), Wpextend_Section_Pc::getInstance()->get_all_type_section_pc(true) ), $this->alias );
	 $retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

	 $retour_html .= Wpextend_Render_Admin_Html::form_close( 'Update section' );

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

	  if(
		  isset( $_POST['name'], $_POST['description'], $_POST['category'], $_POST['file_controller_view'] ) ||
		  (
			  isset( $_POST['category'], $_POST['alias'] ) &&
			  $_POST['alias'] != 'none'
		  )
	  ) {

		  // Get Wpextend_Section_Pc instance
		  $instance_Wpextend_Section_Pc = Wpextend_Section_Pc::getInstance();

		  // Protect data
		  $name				= sanitize_text_field( $_POST['name'] );
		  $description 	= sanitize_text_field( $_POST['description'] );
		  $category			= explode('__', $_POST['category'], 2);
		  $post_type		= $category[0];
		  $category			= $category[1];
		  $file				= sanitize_text_field( $_POST['file_controller_view'] );
		  $alias				= sanitize_text_field( $_POST['alias'] );

		  // Add in Wpextend_Custom_Field
		  $instance_Wpextend_Section_Pc->add_new_section( $name, $description, $post_type, $category, $file, $alias );

		  // Save in Wordpress database
		  $instance_Wpextend_Section_Pc->save();

		  if( !isset( $_POST['ajax'] ) ) {
			  $goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
			  wp_safe_redirect( $goback );
		  }
		  exit;
	  }
  }









  /**
  * Get POST and update section type
  *
  * @return boolean
  */
  static public function update() {

	 // Check valid nonce
	 check_admin_referer($_POST['action']);

	 if(
		 isset( $_POST['id'], $_POST['name'], $_POST['description'], $_POST['category'], $_POST['file_controller_view'] ) ||
		 (
			 isset( $_POST['category'], $_POST['alias'] ) &&
			 $_POST['alias'] != 'none'
		)
	 ) {

		 // Get Wpextend_Section_Pc instance
		 $instance_Wpextend_Section_Pc = Wpextend_Section_Pc::getInstance();

		 // Protect data
		 $id					= sanitize_text_field( $_POST['id'] );
		 $name				= sanitize_text_field( $_POST['name'] );
		 $description 		= sanitize_text_field( $_POST['description'] );
		 $category			= explode('__', $_POST['category'], 2);
		 $post_type			= $category[0];
		 $category			= $category[1];
		 $file				= sanitize_text_field( $_POST['file_controller_view'] );
		 $alias				= sanitize_text_field( $_POST['alias'] );

		 // Update in Wpextend_Custom_Field
		 $instance_Wpextend_Section_Pc->update_section( $id, $name, $description, $post_type, $category, $file, $alias );

		 // Save in Wordpress database
		 $instance_Wpextend_Section_Pc->save();

		 if( !isset( $_POST['ajax'] ) ) {
			 $goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
			 wp_safe_redirect( $goback );
		 }
		 exit;
	 }
  }









	static public function delete(){

		if( isset( $_GET['post_type'], $_GET['category'] ) ) {

			// Protect data
			$post_type = sanitize_text_field( $_GET['post_type'] );
			$category = sanitize_text_field( $_GET['category'] );

			// Get Wpextend_Section_Pc instance
			$instance_Wpextend_Section_Pc = Wpextend_Section_Pc::getInstance();

			if(isset($_GET['id'])){

				// Protect data
				$id = sanitize_text_field( $_GET['id'] );

				// Delete in Wpextend_Section_Pc
				$instance_Wpextend_Section_Pc->delete_section_type( $post_type, $category, $id );
			}else{

				// Delete in Wpextend_Section_Pc
				$instance_Wpextend_Section_Pc->delete_category_section_type( $post_type, $category );
			}

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
