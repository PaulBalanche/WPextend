<?php


/**
 *
 */
class Wpextend_Post {

	public $id;
	public $instance_WP_Post;
	public $list_sections_pc_buzzpress;


	/**
	*
	*/
	public function __construct($post_id){

		if(is_numeric($post_id)){
			$this->id = $post_id;
			$this->instance_WP_Post = get_post($post_id);
		}
	}




	/**
	*
	*/
	public function get_sections_pc_buzzpress(){

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
		$tab_sections = $this->get_sections_pc_buzzpress();

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



}
