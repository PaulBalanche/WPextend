<?php


/**
 *
 */
class Wpextend_Post {

	public $id;
	public $instance_WP_Post;
	public $list_sections_pc_wpextend;


	/**
	*
	*/
	public function __construct($post_id){

		if(is_numeric($post_id)){
			$this->id = $post_id;
			$this->instance_WP_Post = get_post($this->id);

			// Add metadata to instance
			$meta_data = get_metadata('post', $this->id);
			$this->instance_WP_Post->meta_data = (object) [];
			foreach( $meta_data as $key => $val ){
				if( strpos( $key, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
					$this->instance_WP_Post->meta_data->{str_replace( WPEXTEND_PREFIX_DATA_IN_DB, '', $key )} = get_post_meta($post_id, $key, true);
				}
			}
		}
	}



	/**
	*
	*/
	public function get_sections_pc_wpextend(){

		$tab_sections = get_post_meta( $this->instance_WP_Post->ID, Wpextend_Section_Pc::$key_list_section_in_database, true );
		if( !is_array($tab_sections) ){
			$tab_sections = array();
		}

		return $tab_sections;
	}



	/**
	* @param void $id_section
	*/
	public function add_section($id_section){

		// Get all actual sections
		$tab_sections = $this->get_sections_pc_wpextend();

		if( !in_array($id_section, $tab_sections) ){
			// Add new section
			$tab_sections[] = $id_section;

			// Save in database
			update_post_meta( $this->instance_WP_Post->ID, Wpextend_Section_Pc::$key_list_section_in_database, $tab_sections );
		}
	}




	/**
	* @param void $id_section
	*/
	public function update_sections($new_tab_section){

		// Save in database
		update_post_meta( $this->instance_WP_Post->ID, Wpextend_Section_Pc::$key_list_section_in_database, $new_tab_section );
	}




	/**
	*	Return the main parent post ID
	*/
	public function get_main_parent_id(){

		$config_section = get_post_meta( $this->instance_WP_Post->ID, Wpextend_Section_Pc::getInstance()->name_option_in_database . '_config_section', true);
		$main_parent_id = ( is_array($config_section) && isset($config_section['parent_id']) ) ? $config_section['parent_id'] : false;

		return $main_parent_id;
	}



	/**
	*	Return section type
	*/
	public function get_type_section(){

		$config_section = get_post_meta( $this->instance_WP_Post->ID, Wpextend_Section_Pc::getInstance()->name_option_in_database . '_config_section', true);
		if( is_array($config_section) && array_key_exists( 'type_section', $config_section ) )
			return $config_section['type_section'];
		else
			return false;
	}



	public function update_config_section($parent_id, $type_section){

		if( $parent_id && $type_section && is_numeric($parent_id) && !empty($type_section) ){

			$new_config_section['parent_id'] = $parent_id;
			$new_config_section['type_section'] = $type_section;

			return update_post_meta( $this->instance_WP_Post->ID, Wpextend_Section_Pc::getInstance()->name_option_in_database . '_config_section', $new_config_section );
		}
	}



	/**
	* Return file to load related to section type
	*/
	public function get_file_to_load(){

		if( WPEXTEND_ENABLE_SECTION ){

			$post_type_parent = get_post_type( $this->get_main_parent_id() );
			$type_section_temp = explode( '__', $this->get_type_section(), 2 );
			$category_type_section = $type_section_temp[0];
			$type_section = $type_section_temp[1];


			$instance_Wpextend_Section_Pc = Wpextend_Section_Pc::getInstance();
			return $instance_Wpextend_Section_Pc->Wpextend_Section_Pc[$post_type_parent][$category_type_section]['sections'][$type_section]['file'];
		}

		return false;
	}



}