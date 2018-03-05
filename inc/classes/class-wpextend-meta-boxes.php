<?php


/**
*
*/
class Wpextend_Meta_Boxes {



	public $metabox_key;
	public $metabox_ID;
	public $metabox_name;
	public $post_type_applied;
	public $metabox_context;
	public $metabox_priority;
	public $list_fields;



	/**
	* The constructor
	*
	*/
	public function __construct($post_type, $metabox_ID, $metabox_name, $list_fields = array(), $metabox_context = 'advanced', $metabox_priority = 'high') {

		// Define attributes
		$this->metabox_key = $metabox_ID;
		$this->metabox_ID = sanitize_title( $metabox_name );
		$this->metabox_name = $metabox_name;
		$this->post_type_applied = $post_type;
		$this->metabox_context = $metabox_context;
		$this->metabox_priority = $metabox_priority;
		$this->list_fields = $list_fields;

		// Initialize meta_boxes
		$this->initialize();
	}



	/**
 	* Add actions to register and save meta_boxes
	*
 	*/
 	public function initialize() {

		add_action( 'add_meta_boxes_' . $this->post_type_applied, array($this, 'add_meta_box') );
		add_action( 'save_post_' . $this->post_type_applied, array($this, 'save_meta_box') );
 	}



	/**
 	* Called "add_meta_box" Wordpress function to register meta_box
 	*
 	*/
	public function add_meta_box($post = null){

		$show_metabox = true;
		$show_metabox = apply_filters( 'show_metabox_wpextend', $post, $this->metabox_key);
		if( $show_metabox ){
			add_meta_box( $this->metabox_ID, $this->metabox_name, array($this, 'show_meta_box'), $this->post_type_applied, $this->metabox_context, $this->metabox_priority );
		}
	}



	/**
 	* Callback function that fills the box with the desired content.
	* The function should echo its output.
 	*
 	*/
	public function show_meta_box($post = null){

		$retour_html = '';
		if( $post != null ){

			// Get current post_meta value
			$current_value_metabox = get_post_meta( $post->ID, WPEXTEND_PREFIX_DATA_IN_DB . $this->metabox_ID, true );

			$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();

			if( is_array($this->list_fields) ){
				foreach( $this->list_fields as $key => $val ){

					// Get post_meta value if field is indexable
					if( array_key_exists( 'indexable', $val ) && $val['indexable'] == 1 )
						$default_value_field = get_post_meta( $post->ID, WPEXTEND_PREFIX_DATA_IN_DB . $this->metabox_ID . '__' . $key . '_indexable', true );
					else
						$default_value_field = ( is_array( $current_value_metabox ) && array_key_exists( $key, $current_value_metabox ) ) ? $current_value_metabox[ $key ] : '';

					// If field is repeatable
					$repeatable = ( array_key_exists( 'repeatable', $val ) && $val['repeatable'] == 1 ) ? true : false;

					// Field description
					$description = ( array_key_exists( 'description', $val ) ) ? $val['description'] : '';

					$instance_single_custom_field = new Wpextend_Single_Custom_Field( $post->ID, $this->metabox_ID, $key, $val, $default_value_field, $repeatable, $description );
					$retour_html .= $instance_single_custom_field->render_html_in_metabox();
				}
			}

			$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();
		}
		echo $retour_html;
	}



	/**
	* Verify each metabox fields and save them in Wordpress database use "update_post_meta"
 	*
 	*/
	public function save_meta_box($post_id){

		$save_metabox = apply_filters( 'show_metabox_wpextend', get_post($post_id), $this->metabox_key);
		if( $save_metabox  && is_array( $this->list_fields ) && array_key_exists( $this->metabox_ID, $_POST ) ){

			// Textarea Traitement to include them in related fields
			foreach( $_POST as $key => $val ){
				if( preg_match( '/textarea__cat__(.*)__id__(.*)/', $key, $matches ) ){
					if( is_array($matches) && count($matches) == 3){
						$_POST[ $matches[1] ][ $matches[2] ] = $val;
					}
				}
			}

			// Get current post_meta value
			$current_value_in_database = get_post_meta( $post_id, WPEXTEND_PREFIX_DATA_IN_DB . $this->metabox_ID, true );
			$new_value_to_save = ( is_array( $current_value_in_database ) ) ? $current_value_in_database : array();

		   	foreach( $this->list_fields as $key => $val ){

				if( array_key_exists( $key, $_POST[ $this->metabox_ID ] ) ){

					// If is repeatable field, loop in variable to delete empty data
					if( array_key_exists( 'repeatable', $val ) && $val['repeatable'] == 1 && is_array($_POST[ $this->metabox_ID ][ $key ]) && count($_POST[ $this->metabox_ID ][ $key ]) > 1 ){
						foreach( $_POST[ $this->metabox_ID ][ $key ] as $key_single_repeatable_field => $val_single_repeatable_field ){
							if( count($_POST[ $this->metabox_ID ][ $key ]) > 1 && ($val_single_repeatable_field == '' || $val_single_repeatable_field == 'null') ){
								unset( $_POST[ $this->metabox_ID ][ $key ][ $key_single_repeatable_field ] );
							}
						}
					}

					// Set post_meta value if field is indexable
					if( array_key_exists( 'indexable', $val ) && $val['indexable'] == 1 ){
						$current_value_field = get_post_meta( $post_id, WPEXTEND_PREFIX_DATA_IN_DB . $this->metabox_ID . '__' . $key . '_indexable', true );
						update_post_meta( $post_id, WPEXTEND_PREFIX_DATA_IN_DB . $this->metabox_ID . '__' . $key . '_indexable', $_POST[ $this->metabox_ID ][ $key ], $current_value_field );
					}
					else
						$new_value_to_save[ $key ] = $_POST[ $this->metabox_ID ][ $key ];
				}
			}

			update_post_meta( $post_id, WPEXTEND_PREFIX_DATA_IN_DB . $this->metabox_ID, $new_value_to_save, $current_value_in_database );
		}
	}



}
