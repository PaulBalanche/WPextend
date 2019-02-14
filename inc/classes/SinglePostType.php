<?php

namespace Wpextend;

/**
 *
 */
class SinglePostType {

	public $slug;
	public $labels;
	public $args;
	public $taxonomy;
	public static $default_labels = array(
		'name'						=> 'New custom post',
		'singular_name'				=> 'New custom post',
		'add_new'					=> 'Add',
		'add_new_item'				=> 'Add new item',
		'new_item'					=> 'New',
		'edit_item'					=> 'Edit custom post',
		'view_item'					=> 'View custom post',
		'all_items'					=> 'All custom post',
		'search_items'				=> 'Search custom post',
		'parent_item_colon'			=> 'Custom post parent',
		'not_found'					=> 'None custom post',
		'not_found_in_trash'		=> 'None custom post deleted'
	);

	public static $default_args = array(
		'description'			=> '',
		'public'				=> array( 'true', 'false' ),
		'capability_type'		=> array( 'post' ),
		'hierarchical'			=> array( 'false', 'true' ),
		'show_in_menu'			=> array( 'true', 'false' ),
		'menu_position'			=> '',
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

	public static $default_args_on_creation = array(
		'description'			=> 'Custom post description',
		'public'				=> 'true',
		'capability_type'		=> 'post',
		'hierarchical'			=> 'false',
		'show_in_menu'			=> 'true',
		'menu_position'			=> 'null',
		'rewrite'				=> 'true',
		'has_archive'			=> 'false',
		'show_in_rest'			=> 'true',
		'supports'				=> array(
			'title',
			'editor',
			'author',
			'thumbnail'
		)
	);

	public static $default_annex_args = array(
		'multiple_post_thumbnails'	=> '0'
	);



	/**
 	* The constructor.
 	*
 	* @return void
 	*/
 	public function __construct($slug, $data = array()) {

		$this->labels = ( isset($data['labels']) ) ? wp_parse_args( $data['labels'], self::$default_labels ) : self::$default_labels;
		$this->slug = $slug;
		$this->args = ( isset($data['args']) ) ? wp_parse_args( $data['args'], self::$default_args ) : self::$default_args;
		$this->args['labels'] = $this->labels;
		$this->annex_args = ( isset($data['annex_args']) ) ? wp_parse_args( $data['annex_args'], self::$default_annex_args ) : self::$default_annex_args;

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
				'sort' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_in_rest' => true
			)
		);
	}



	/**
 	* STATIC : Render form to add new field
 	*/
 	static public function render_form_create() {

		$tab_labels = self::$default_labels;
		$slug = '';
		$tab_args = self::$default_args_on_creation;
		$taxonomy = array('slug' => '', 'label' => '');
		$annex_args = self::$default_annex_args;

		$retour_html = self::render_form( $tab_labels, $slug, $tab_args, $taxonomy, $annex_args );
		$retour_html .= RenderAdminHtml::form_close( 'Add post type' );
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
		$annex_args = $this->annex_args;

		$retour_html = self::render_form( $tab_labels, $slug, $tab_args, $taxonomy, $annex_args );
		$retour_html .= RenderAdminHtml::form_close( 'Edit post type' );
		return $retour_html;
 	}





	/**
	* PRIVATE : Render form to create or edit custom post type
	*/
	static private function render_form( $tab_labels, $slug, $tab_args, $taxonomy, $annex_args ){

		$retour_html = '<hr>';
 		$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'add_custom_post_type_wpextend', 'add_custom_post_type_wpextend' );

 		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= ( !in_array($slug, PostType::$list_reserved_post_types) ) ? TypeField::render_input_text( 'Slug', 'slug', $slug) : TypeField::render_disable_input_text( 'Slug', 'slug', $slug);
		$retour_html .= RenderAdminHtml::table_edit_close();

		if( !in_array($slug, PostType::$list_reserved_post_types) ){

			$retour_html .= '<br /><br /><br /><br /><h3>Informations:</h3><div style="float:left; width: 50%">';

			$retour_html .= RenderAdminHtml::table_edit_open();
			foreach( self::$default_labels as $key => $val) {
				$retour_html .= TypeField::render_input_text( $key, 'labels['.$key.']', $tab_labels[$key] );
			}
			$retour_html .= RenderAdminHtml::table_edit_close();

			$retour_html .= '</div><div style="float:right; width: 50%">';

			$retour_html .= RenderAdminHtml::table_edit_open();
			foreach( self::$default_args as $key => $val) {
				if( is_array($val) ) {
					if( isAssoc($val) ){
						$defaut_value = (self::$default_args != $tab_args) ? $tab_args[$key] : false;
						$retour_html .= TypeField::render_input_checkbox( $key, 'args['.$key.']', $val, $defaut_value );
					}
					else{
						$defaut_value = (self::$default_args != $tab_args) ? $tab_args[$key] : false;
						$retour_html .= TypeField::render_input_select( $key, 'args['.$key.']', $val, $defaut_value );
					}
				}
				else {
					$retour_html .= TypeField::render_input_text( $key, 'args['.$key.']', $tab_args[$key] );
				}
			}
	 		$retour_html .= RenderAdminHtml::table_edit_close();

			$retour_html .= '</div><div style="clear:both"></div><br /><br /><br /><br /><h3>Taxonomy:</h3>';
		

			$retour_html .= RenderAdminHtml::table_edit_open();
			$retour_html .= TypeField::render_input_text( 'Taxonomy label', 'taxonomy[label]', $taxonomy['label']);
			$retour_html .= TypeField::render_input_text( 'Taxonomy slug', 'taxonomy[slug]', $taxonomy['slug']);
			$retour_html .= RenderAdminHtml::table_edit_close();

		}

		$retour_html .= '<br /><br /><br /><br /><h3>Multiple post thumbnails:</h3>';
		$retour_html .= RenderAdminHtml::table_edit_open();
		foreach( self::$default_annex_args as $key => $val) {

			if( $key == 'multiple_post_thumbnails' )
				$retour_html .= TypeField::render_input_text( $key, 'annex_args['.$key.']', $annex_args[$key], '', false, 'Require "Multiple Post Thumbnails" plugin' );
			else
				$retour_html .= TypeField::render_input_text( $key, 'annex_args['.$key.']', $annex_args[$key] );
		}
		$retour_html .= RenderAdminHtml::table_edit_close();
		

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

		if( isset($_POST['slug']) ) {

			// Get PostType instance
			$instance_Wpextend_Post_Type = PostType::getInstance();

			$slug = sanitize_text_field( $_POST['slug'] );

			// Protect data
			$labels = array();
			if( isset($_POST['labels']) && is_array($_POST['labels']) ){
				foreach( $_POST['labels'] as $key => $val ){
					$labels[$key] = sanitize_text_field( $val );
				}
			}

			$taxonomy = array();
			if( isset($_POST['taxonomy']) && is_array($_POST['taxonomy']) ){
				foreach( $_POST['taxonomy'] as $key => $val ){
					$taxonomy[$key] = sanitize_text_field( $val );
				}
			}

			$args = array();
			if( isset($_POST['args']) && is_array($_POST['args']) ){
				foreach( $_POST['args'] as $key => $val ){

					if( is_array($val) ) {

						if( isAssoc( self::$default_args[$key] ) ) {
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
			}

			$annex_args = array();
			if( isset($_POST['annex_args']) && is_array($_POST['annex_args']) ){
				foreach( $_POST['annex_args'] as $key => $val ){
					$annex_args[$key] = sanitize_text_field( $val );
				}
			}

			// Add in Wpextend_Post_Type
			$instance_Wpextend_Post_Type->add_new( $labels, $slug, $args, $taxonomy, $annex_args );

			// Save in Wordpress database
			$instance_Wpextend_Post_Type->save();
		
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

			// Get Wpextend_Post_Type instance
			$instance_Wpextend_Post_Type = PostType::getInstance();

			// Add in Wpextend_Post_Type
 			$instance_Wpextend_Post_Type->delete( $id_post_type );

 			// Save in Wordpress database
 			$instance_Wpextend_Post_Type->save();

			if( !isset( $_POST['ajax'] ) ) {
	 			$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
	 			wp_safe_redirect( $goback );
			}
 			exit;
		}
	}





}
