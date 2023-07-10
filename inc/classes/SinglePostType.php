<?php

namespace Wpextend;

use \Wpextend\Package\AdminNotice;
use \Wpextend\Package\RenderAdminHtml;
use \Wpextend\Package\TypeField;

/**
 *
 */
class SinglePostType {

	public $slug;
	public $labels;
	public $args;
	public $annex_args;
	public $taxonomy;
	public static $default_labels = array(
		'name'						=> '%s',
		'singular_name'				=> 'New %s',
		'add_new'					=> 'Add',
		'add_new_item'				=> 'Add new %s',
		'new_item'					=> 'New',
		'edit_item'					=> 'Edit %s',
		'view_item'					=> 'View %s',
		'all_items'					=> 'All %p',
		'search_items'				=> 'Search %s',
		'parent_item_colon'			=> '%s parent',
		'not_found'					=> 'None %s',
		'not_found_in_trash'		=> 'None %s deleted'
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
		'description'			=> '',
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
		$this->annex_args = ( isset($data['annex_args']) ) ? wp_parse_args( $data['annex_args'], [ 'multiple_post_thumbnails' => '0' ] ) : [ 'multiple_post_thumbnails' => '0' ];

		if( array_key_exists('taxonomy', $data) && is_array($data['taxonomy']) && array_key_exists('slug', $data['taxonomy']) && array_key_exists('label', $data['taxonomy']) && !empty($data['taxonomy']['slug']) && !empty($data['taxonomy']['label']) ){
			$this->taxonomy = $data['taxonomy'];
			$this->taxonomy['hierarchical'] = ( array_key_exists('hierarchical', $data['taxonomy']) ) ? $data['taxonomy']['hierarchical'] : true;
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
				'hierarchical' => $this->taxonomy['hierarchical'],
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
		return $retour_html;
 	}



	/**
	* PRIVATE : Render form to create or edit custom post type
	*/
	static private function render_form( $tab_labels, $slug, $tab_args, $taxonomy, $annex_args ){

 		$retour_html = RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'add_custom_post_type_wpextend', 'add_custom_post_type_wpextend' );

 		$retour_html .= RenderAdminHtml::table_edit_open();
		$retour_html .= ( !in_array($slug, PostType::$list_reserved_post_types) ) ? TypeField::render_input_text( 'Slug', 'slug', $slug) : TypeField::render_disable_input_text( 'Slug', 'slug', $slug);

		// Regex naming
		$free_html = '<div class="flex_item mb-2">' . TypeField::render_input_text( '<strong>Singular</strong>', 'regex_naming[singular]', '', '', false, '', false ) . '</div>';
		$free_html .= '<div class="flex_item mb-2">' . TypeField::render_input_text( '<strong>Plural</strong>', 'regex_naming[plural]', '', '', false, '', false ) . '</div>';
		$retour_html .= TypeField::render_label_and_free_html( 'Regex naming', '', '<div class="flex-container">' . $free_html . '</div>' );

		// Labels
		$free_html = '';
		foreach( self::$default_labels as $key => $val) {
			$free_html .= '<div class="flex_item mb-2">' . TypeField::render_input_text( '<strong>' . $key . '</strong>', 'labels['.$key.']', $tab_labels[$key], '', false, '', false ) . '</div>';
		}
		$retour_html .= TypeField::render_label_and_free_html( 'Labels', '', '<div class="flex-container">' . $free_html . '</div>' );

		// Args
		$free_html = '';
		foreach( self::$default_args as $key => $val) {
			if( is_array($val) ) {
				if( isAssoc($val) ){
					$defaut_value = (self::$default_args != $tab_args) ? $tab_args[$key] : false;
					$free_html .= '<div class="flex_item mb-2">' . TypeField::render_input_checkbox( '<strong>' . $key . '</strong><br />', 'args['.$key.']', $val, $defaut_value, false, '', false, false ) . '</div>';
				}
				else{
					$defaut_value = (self::$default_args != $tab_args) ? $tab_args[$key] : false;
					$free_html .= '<div class="flex_item mb-2">' . TypeField::render_input_select( '<strong>' . $key . '</strong><br />', 'args['.$key.']', $val, $defaut_value, false, '', false ) . '</div>';
				}
			}
			else {
				$free_html .= '<div class="flex_item mb-2">' . TypeField::render_input_text( '<strong>' . $key . '</strong>', 'args['.$key.']', $tab_args[$key], '', false, '', false ) . '</div>';
			}
		}
		$retour_html .= TypeField::render_label_and_free_html( 'Args', '', '<div class="flex-container">' . $free_html . '</div>' );

		// Taxonomy
		$free_html = '<div class="flex_item mb-2">' . TypeField::render_input_text( '<strong>Taxonomy label</strong>', 'taxonomy[label]', $taxonomy['label'], '', false, '', false ) . '</div>';
		$free_html .= '<div class="flex_item mb-2">' . TypeField::render_input_text( '<strong>Taxonomy slug</strong>', 'taxonomy[slug]', $taxonomy['slug'], '', false, '', false ) . '</div>';
		$retour_html .= TypeField::render_label_and_free_html( 'Taxonomy', '', '<div class="flex-container">' . $free_html . '</div>' );

		// Multiple post thumbnails
		$free_html = '';
		foreach( self::$default_annex_args as $key => $val) {

			if( $key == 'multiple_post_thumbnails' )
				$free_html .= TypeField::render_input_text( '<strong>' . $key . '</strong>', 'annex_args['.$key.']', $annex_args[$key], '', false, 'Require "Multiple Post Thumbnails" plugin', false );
			else
				$free_html .= TypeField::render_input_text( '<strong>' . $key . '</strong>', 'annex_args['.$key.']', $annex_args[$key], '', false, '', false );
		}
		$retour_html .= TypeField::render_label_and_free_html('Multiple post thumbnails', '', $free_html);

		$retour_html .= RenderAdminHtml::table_edit_close();
		$retour_html .= RenderAdminHtml::form_close( 'Submit', true );

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

		if( isset($_POST['slug']) && ! empty($_POST['slug']) ) {

			// Get PostType instance
			$instance_Wpextend_Post_Type = PostType::getInstance();

			$slug = sanitize_title( $_POST['slug'] );

			// Use regex naming
			foreach( $_POST['labels'] as $key => $val ){
				
				$_POST['labels'][$key] = preg_replace( '/^(%s)(.*)/', ucfirst($_POST['regex_naming']['singular']) . '$2', $_POST['labels'][$key] );
				$_POST['labels'][$key] = preg_replace( '/^(.*)(%s)/', '$1' . strtolower($_POST['regex_naming']['singular']), $_POST['labels'][$key] );
				$_POST['labels'][$key] = preg_replace( '/^(%p)(.*)/', ucfirst($_POST['regex_naming']['plural']) . '$2', $_POST['labels'][$key] );
				$_POST['labels'][$key] = preg_replace( '/^(.*)(%p)/', '$1' . strtolower($_POST['regex_naming']['plural']), $_POST['labels'][$key] );
			}

			// Protect data
			$labels = array();
			if( isset($_POST['labels']) && is_array($_POST['labels']) ){

				$POST_labels = stripslashes_deep($_POST['labels']);
				foreach( $POST_labels as $key => $val ){
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
		
			if( !isset( $_POST['ajax'] ) ) {

				AdminNotice::add_notice( '010', 'Post type successfully added.', 'success', true, true, AdminNotice::$prefix_admin_notice );

				wp_safe_redirect( wp_get_referer() );
			}
			exit;
		}

		if( !isset( $_POST['ajax'] ) ) {
			wp_safe_redirect( wp_get_referer() );
		}
		exit;
	}



	static public function delete(){

		if( isset( $_GET['id'] ) ) {

			$id_post_type = sanitize_text_field( $_GET['id'] );

			// Get Wpextend_Post_Type instance and remove targeted element
			$instance_Wpextend_Post_Type = PostType::getInstance();
			$instance_Wpextend_Post_Type->delete( $id_post_type );

			if( !isset( $_POST['ajax'] ) ) {
				
				AdminNotice::add_notice( '012', 'Post type successfully removed.', 'success', true, true, AdminNotice::$prefix_admin_notice );

	 			wp_safe_redirect( wp_get_referer() );
			}
 			exit;
		}
	}





}
