<?php


/**
 *
 */
// class Buzzpress_Single_Post_Type extends Buzzpress_Post {
class Buzzpress_Single_Post_Type {

	public $slug;
	public $labels;
	public $args;
	public $taxonomy;
	public static $default_labels = array(
		 'name'						=> 'New custom post',
		 'singular_name'			=> 'New custom post',
		 'add_new'					=> 'Add',
		 'add_new_item'			=> 'Add new item',
		 'new_item'					=> 'New',
		 'edit_item'				=> 'Edit custom post',
		 'view_item'				=> 'View custom post',
		 'all_items'				=> 'All custom post',
		 'search_items'			=> 'Search custom post',
		 'parent_item_colon'		=> 'Custom post parent',
		 'not_found'				=> 'None custom post',
		 'not_found_in_trash'	=> 'None custom post deleted'
	);

	public static $default_args = array(
		 'description'			=> 'Custom post description',
		 'public'				=> array( 'true', 'false' ),
		 'capability_type'	=> array( 'post' ),
		 'hierarchical'		=> array( 'false', 'true' ),
		 'show_in_menu'		=> array( 'true', 'false' ),
		 'menu_position'		=> 'null',
		 'rewrite'				=> array( 'false', 'true' ),
		 'has_archive'			=> array( 'false', 'true' ),
		 'show_in_rest'			=> array( 'false', 'true' ),
		 'supports'				=> array(
			 								'title' => 'title',
		 									'editor' => 'editor',
											'author' => 'author',
											'thumbnail' => 'thumbnail',
											'excerpt' => 'excerpt',
											'trackbacks' => 'trackbacks',
											'custom-fields' => 'custom-fields',
											'comments' => 'comments',
											'revisions' => 'revisions',
											'page-attributes' => 'page-attributes',
											'post-formats' => 'post-formats'
										)
	);






	/**
 	* The constructor.
 	*
 	* @return void
 	*/
 	public function __construct($slug, $data) {

		$this->labels = wp_parse_args( $data['labels'], Buzzpress_Single_Post_Type::$default_labels );
		$this->slug = $slug;
		$this->args = wp_parse_args( $data['args'], Buzzpress_Single_Post_Type::$default_args );
		$this->args['labels'] = $this->labels;

		if( array_key_exists('taxonomy', $data) && is_array($data['taxonomy']) && array_key_exists('slug', $data['taxonomy']) && array_key_exists('label', $data['taxonomy']) && !empty($data['taxonomy']['slug']) && !empty($data['taxonomy']['label']) ){
			$this->taxonomy = $data['taxonomy'];
		}
 	 }



	 /**
	 * Register new Post Type in Wordpress system
	 *
	 * @return void
	 */
	 public function register_custom_post_type(){

		 // Just convert in boolean if necessary
		foreach( $this->args as $key => $val ){
			if( in_array($val, array('true', 'false') ) ){
				$this->args[$key] = convertToBoolean($val);
			}
		}

		 // Call wodpress register post type function
	   register_post_type($this->slug, $this->args);
		if( is_array($this->taxonomy) ){
			$this->register_custom_taxonomy();
		}
	 }



	 /**
	 * Register new taxonomy in Wordpress system
	 *
	 * @return void
	 */
	 public function register_custom_taxonomy(){

		register_taxonomy(
			$this->taxonomy['slug'],
			$this->slug,
			array(
				'label' => $this->taxonomy['label'],
				'rewrite' => array( 'slug' => $this->taxonomy['slug'] ),
				'hierarchical' => true,
				'sort' => true
			)
		);
	}



	/**
 	* STATIC : Render form to add new field
 	*/
 	static public function render_form_create() {

		$tab_labels = Buzzpress_Single_Post_Type::$default_labels;
		$slug = '';
		$tab_args = Buzzpress_Single_Post_Type::$default_args;
		$taxonomy = array('slug' => '', 'label' => '');

		$retour_html = self::render_form( $tab_labels, $slug, $tab_args, $taxonomy );
		$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Add post type' );
		return $retour_html;
 	}





	/**
	* Render form to edit custom post type
	*/
	function render_form_edit() {

		$tab_labels = $this->labels;
		$slug = $this->slug;
		$tab_args = $this->args;
		$taxonomy = $this->taxonomy;

		$retour_html = self::render_form( $tab_labels, $slug, $tab_args, $taxonomy );
		$retour_html .= Buzzpress_Render_Admin_Html::form_close( 'Edit post type' );
		return $retour_html;
 	}





	/**
	* PRIVATE : Render form to create or edit custom post type
	*/
	static private function render_form( $tab_labels, $slug, $tab_args, $taxonomy ){

		$tab_labels_default = Buzzpress_Single_Post_Type::$default_labels;
		$tab_args_default = Buzzpress_Single_Post_Type::$default_args;

		$retour_html = '<hr>';
 		$retour_html .= Buzzpress_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'add_custom_post_type_buzzpress', 'add_custom_post_type_buzzpress' );

 		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_text( 'Slug', 'slug', $slug);
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= '<hr>';

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		foreach( $tab_labels_default as $key => $val) {
			if( is_array($val) ) {
				if( isAssoc($val) ){
					$retour_html .= Buzzpress_Type_Field::render_input_checkbox( $key, 'labels['.$key.']', $val );
				}
				else{
					$retour_html .= Buzzpress_Type_Field::render_input_select( $key, 'labels['.$key.']', $val );
				}
			}
			else {
				$retour_html .= Buzzpress_Type_Field::render_input_text( $key, 'labels['.$key.']', $tab_labels[$key] );
			}
		}
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= '<hr>';

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		foreach( $tab_args_default as $key => $val) {
			if( is_array($val) ) {
				if( isAssoc($val) ){
					$defaut_value = ($tab_args_default != $tab_args) ? $tab_args[$key] : false;
					$retour_html .= Buzzpress_Type_Field::render_input_checkbox( $key, 'args['.$key.']', $val, $defaut_value );
				}
				else{
					$defaut_value = ($tab_args_default != $tab_args) ? $tab_args[$key] : false;
					$retour_html .= Buzzpress_Type_Field::render_input_select( $key, 'args['.$key.']', $val, $defaut_value );
				}
			}
			else {
				$retour_html .= Buzzpress_Type_Field::render_input_text( $key, 'args['.$key.']', $tab_args[$key] );
			}
		}
 		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

		$retour_html .= '<hr>';

		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_open();
		$retour_html .= Buzzpress_Type_Field::render_input_text( 'Taxonomy label', 'taxonomy[label]', $taxonomy['label']);
		$retour_html .= Buzzpress_Type_Field::render_input_text( 'Taxonomy slug', 'taxonomy[slug]', $taxonomy['slug']);
		$retour_html .= Buzzpress_Render_Admin_Html::table_edit_close();

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

		if(
			isset( $_POST['labels'] , $_POST['slug'], $_POST['args'] ) &&
			is_array( $_POST['labels'] ) &&
			is_array( $_POST['args'] ) &&
			is_array( $_POST['taxonomy'] )
		 ) {

			// Get Buzzpress_Post_Type instance
			$instance_Buzzpress_Post_Type = Buzzpress_Post_Type::getInstance();

			// Protect data
			$labels = array();
			foreach( $_POST['labels'] as $key => $val ){
				$labels[$key] = sanitize_text_field( $val );
			}
			$taxonomy = array();
			foreach( $_POST['taxonomy'] as $key => $val ){
				$taxonomy[$key] = sanitize_text_field( $val );
			}
			$slug = sanitize_text_field( $_POST['slug'] );
			$args = array();
			foreach( $_POST['args'] as $key => $val ){

				if( is_array($val) ) {

					if( isAssoc( Buzzpress_Single_Post_Type::$default_args[$key] ) ) {
						$new_val = array();
						foreach( $val as $val2 ){
							$new_val[] = sanitize_text_field( $val2 );
						}
						$args[$key] = $new_val;
					}
					else{
						$args[$key] = sanitize_text_field( $val[0] );
					}

				}
				else{
					$args[$key] = sanitize_text_field( $val );
				}
			}

			// Add in Buzzpress_Post_Type
			$instance_Buzzpress_Post_Type->add_new( $labels, $slug, $args, $taxonomy );

			// Save in Wordpress database
			$instance_Buzzpress_Post_Type->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}





	static public function delete(){

		if( isset( $_GET['id'] ) ) {

			$id_post_type = sanitize_text_field( $_GET['id'] );

			// Get Buzzpress_Post_Type instance
			$instance_Buzzpress_Post_Type = Buzzpress_Post_Type::getInstance();

			// Add in Buzzpress_Post_Type
 			$instance_Buzzpress_Post_Type->delete( $id_post_type );

 			// Save in Wordpress database
 			$instance_Buzzpress_Post_Type->save();

			if( !isset( $_POST['ajax'] ) ) {
	 			$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
	 			wp_safe_redirect( $goback );
			}
 			exit;
		}
	}





}
