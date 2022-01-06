<?php

namespace Wpextend;

use \Wpextend\Package\AdminNotice;
use \Wpextend\Package\RenderAdminHtml;
use \Wpextend\Package\TypeField;

/**
 *
 */
class SingleSetting {

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

		$instance_settings_wpextend = GlobalSettings::getInstance();
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
			if( $this->type == 'select_post_type' ) {

				$list_custom_post = get_posts( array(
					'posts_per_page'   => -1,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'post_type'        => $this->options,
					'post_status'      => 'publish',
					'suppress_filters' => false
				));

				$list_custom_post_hierarchical = $this->posts_sort_hierarchically($list_custom_post);
				$this->options = $this->get_deep_select_option( $list_custom_post_hierarchical );
			}
		}
	}



	public function get_deep_select_option( $list, $prefix = '' ) {

		$options = [];

		foreach( $list as $value ){

			$options[ $value->ID ] = ( ! empty($prefix) ) ? $prefix . ' ' . $value->post_title : $value->post_title;

			if( isset( $value->child ) ) {
				$options = $options + $this->get_deep_select_option( $value->child, $prefix . '-' );
			}
		}

		return $options;
	}



	public function posts_sort_hierarchically( $posts ) {

		$posts_sorted = [];

		while( count($posts) > 0 ) {

			foreach( $posts as $key => $post ) {

				$posts[$key]->child = [];

				if( ! isset($posts[$key]->post_parent) || ! $posts[$key]->post_parent || $posts[$key]->post_parent == 0 ) {
					$posts_sorted[ $posts[$key]->ID ] = $posts[$key];
					unset($posts[$key]);
				}
				elseif( isset($posts_sorted[ $posts[$key]->post_parent ] ) ) {
					$posts_sorted[ $posts[$key]->post_parent ]->child[ $posts[$key]->ID ] = $posts[$key];
					unset($posts[$key]);
				}
				else {
					$this->find_deep_relationship( $posts_sorted, $posts, $key );
				}
			}
		}

		return $posts_sorted;
	}



	public function find_deep_relationship( &$posts_sorted, &$ref_posts, $key_post ) {

		foreach( $posts_sorted as $key => $post ) {

			if( count($post->child) > 0 ) {
				if( isset($post->child[ $ref_posts[$key_post]->post_parent ] ) ) {
					$posts_sorted[ $key ]->child[ $ref_posts[$key_post]->post_parent ]->child[ $ref_posts[$key_post]->ID ] = $ref_posts[$key_post];
					unset($ref_posts[$key_post]);
				}
				else {
					$this->find_deep_relationship( $posts_sorted[ $key ]->child, $ref_posts, $key_post );
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

		if($current_screen->parent_base == WPEXTEND_MAIN_SLUG_ADMIN_PAGE){
			$retour_html .= TypeField::render_label_and_free_html( $this->name, 'fields['.$this->category.']['.$this->id.']', '<a class="button" href="'.add_query_arg( array( 'action' => 'delete_setting', 'category' => $this->category, 'key' => $this->id, '_wpnonce' => wp_create_nonce( 'delete_setting' ) ), admin_url( 'admin-post.php' ) ).'">Remove</a>', $this->type );
		}
		else {
			switch( $this->type ) {

				case 'text':
					$retour_html .= TypeField::render_input_text( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->placeholder, $this->repeatable, $this->description );
					break;

				case 'textarea':

					if( $this->repeatable ) {
						$retour_html .= TypeField::render_input_textarea( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description, false );
					}
					else{
						$retour_html .= TypeField::render_input_textarea( $this->name, 'textarea__fields__cat__'.$this->category.'__id__'.$this->id, $this->value, $this->repeatable, $this->description );
					}
					break;

				case 'select':
					$retour_html .= TypeField::render_input_select( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
					break;

				case 'select_post_type':
					$retour_html .= TypeField::render_input_select( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
					break;

				case 'radio':
					$retour_html .= TypeField::render_input_radio( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
					break;

				case 'checkbox':
					$retour_html .= TypeField::render_input_checkbox( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->options, $this->value, $this->repeatable, $this->description );
					break;

				case 'link':
					$retour_html .= TypeField::render_input_cta( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description );
					break;

				case 'image':
					$retour_html .= TypeField::render_input_image( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description );
					break;

				case 'gallery_image':
					$retour_html .= TypeField::render_input_image_gallery( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->description, false );
					break;

				case 'file':
					$retour_html .= TypeField::render_input_file( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description );
					break;
				
				case 'multiple_files':
					$retour_html .= TypeField::render_input_file( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, $this->repeatable, $this->description, true );
					break;

				case 'daterange':
					$retour_html .= TypeField::render_input_daterange( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value, '', $this->description );
					break;

				case 'sliderrange':
					$retour_html .= TypeField::render_input_sliderrange( $this->name, 'fields['.$this->category.']['.$this->id.']', $this->value );
					break;
			}
		}

		return $retour_html;
	}








	/**
	* Render form to add new field
	*/
	static public function render_form_create( $key_category  ){

		$retour_html = '<div class="form_add_setting_wpextend">';

		$retour_html .= '<input type="button" class="add_new_settings button button-primary" value="New field">';

		$retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'add_settings_wpextend', '', 'hidden');

		$retour_html .= RenderAdminHtml::table_edit_open( 'New field' );
		$retour_html .= TypeField::render_input_text( 'Name', 'name' );
		$retour_html .= TypeField::render_input_text( 'Description', 'description' );
		$retour_html .= TypeField::render_input_select('Type', 'type_field', TypeField::get_available_fields() );
		$retour_html .= TypeField::render_input_select('Post type select', 'post_type_options', PostType::getInstance()->get_all_include_base_wordpress() );
		$retour_html .= TypeField::render_input_checkbox( 'Repeatable ?', 'repeatable', array( 'true' => 'Ce champ pourra être dupliqué') );
		$retour_html .= TypeField::render_input_hidden( 'category', $key_category );
		$retour_html .= RenderAdminHtml::table_edit_close();

		$retour_html .= RenderAdminHtml::form_close( 'Add', true );

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

			// Get GlobalSettings instance
			$instance_global_settings = GlobalSettings::getInstance();

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

			// Add in GlobalSettings
			$instance_global_settings->add_new_setting($name, $description, $type, $id_category, $options, $repeatable);

			// Save in Wordpress database
			$instance_global_settings->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );

				AdminNotice::add_notice( '009', 'Field successfully added.', 'success', true, true, AdminNotice::$prefix_admin_notice );

				wp_safe_redirect( $goback );
				exit;
			}
		}
	}







	static public function delete_setting(){

		// Check valid nonce
		check_admin_referer('delete_setting');

		if( isset( $_GET['category'], $_GET['key'] ) ) {

			// Get GlobalSettings instance
			$instance_global_settings = GlobalSettings::getInstance();

			// Protect data
			$category = sanitize_text_field( $_GET['category'] );
			$key = sanitize_text_field( $_GET['key'] );

			// Add in GlobalSettings
			$instance_global_settings->remove_setting( $category, $key );

			// Save in Wordpress database
			$instance_global_settings->save();

			if( !isset( $_POST['ajax'] ) ) {

				AdminNotice::add_notice( '011', 'Field successfully removed', 'success', true, true, AdminNotice::$prefix_admin_notice );

				wp_safe_redirect( wp_get_referer() );
				exit;
			}
		}
	}



}
