<?php

namespace Wpextend;

/**
 *
 */
class PostType {



	private static $_instance;
	public $intial_post_type;
	public $custom_post_type_wpextend;
	public $name_option_in_database = '_custom_post_type_buzzpress';
	static public $admin_url = '_custom_post_type',
	 	$json_file_name = 'custom_post_type.json',
		$list_base_post_type = array('post' => 'Post', 'page' => 'Page'),
		$list_reserved_post_types = array('post', 'page', 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'action', 'author', 'order', 'theme');



	/**
	* Static method which instance post_type_wpextend
 	*/
	public static function getInstance() {
		 if (is_null(self::$_instance)) {
			  self::$_instance = new PostType();
		 }
		 return self::$_instance;
	}



	/**
	* The constructor.
	*
	* @return void
	*/
	private function __construct() {

		// Load custom post type
		$this->load_custom_post_type();

		// Configure hooks
		$this->create_hooks();
	}



	/**
	* Get and load custom post type
	*
	*/
	public function load_custom_post_type(){

		$hooked = apply_filters( 'load_custom_post_type_wpextend', []);
		foreach( $hooked as $key => $val ){
			$hooked[$key]['origin'] = 'Filter "load_custom_post_type_wpextend"';
		}

		$load_json = $this->load_json();
		foreach( $load_json as $key => $val ){
			$load_json[$key]['origin'] = 'JSON file';
		}

		$from_database = $this->get_all_from_database();
		foreach( $from_database as $key => $val ){
			$from_database[$key]['origin'] = 'Database';
		}

		$this->custom_post_type_wpextend = array_merge(
			$hooked,
			$load_json,
			$from_database
		);
	}



	/**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

		// Initialise les customs posts
		add_action( 'init', array( $this, 'initialize'), 4 );
		add_action( 'init', array( $this, 'initialize_multiple_post_thumbnails'), 4 );
	  	add_action( 'admin_post_add_custom_post_type_wpextend', 'Wpextend\SinglePostType::add_new' );
	  	add_action( 'admin_post_delete_custom_post_type', 'Wpextend\SinglePostType::delete' );
		add_action( 'admin_post_import_wpextend_custom_post_type', array($this, 'import') );
		
		add_action( 'wpextend_generate_autoload_json_file', array($this, 'generate_autoload_json_file') );
	}



	/**
	*
	*
	* @return void
	*/
	public function initialize() {

		if( is_array($this->custom_post_type_wpextend) ){
			foreach( $this->custom_post_type_wpextend as $slug => $val ) {
				if( !in_array($slug, self::$list_reserved_post_types) ){
					$custom_post = new SinglePostType( $slug, $val );
					$custom_post->register_custom_post_type();
				}
			}
		}
	}



	public function initialize_multiple_post_thumbnails(){

		if( WPEXTEND_MultiPostThumbnails && is_array($this->custom_post_type_wpextend) ){
			foreach( $this->custom_post_type_wpextend as $slug => $val ) {

				if( isset($val['annex_args']) && isset($val['annex_args']['multiple_post_thumbnails']) && is_numeric($val['annex_args']['multiple_post_thumbnails']) && $val['annex_args']['multiple_post_thumbnails'] > 0 ){

					for( $i = 1; $i <= $val['annex_args']['multiple_post_thumbnails']; $i++ ){
						$indice_featured_image = $i + 1;
						new MultiPostThumbnails(
					        array(
					            'label' => 'Featured Image (' . $indice_featured_image . ')',
					            'id' => 'featured-image-' . $indice_featured_image,
					            'post_type' => $slug
					        )
				    	);
					}
				}
			}
		}
	}



	/**
	* Render HTML admin page
	*
	* @return string
	*/
	public function render_admin_page() {

		// Header page & open form
		$retour_html = RenderAdminHtml::header('Custom Post Type');

		// Display table with all custom post type
		$all_custom_post_type = $this->get_all(true);
		if( is_array($all_custom_post_type) && count($all_custom_post_type) > 0 ) {

			$Wpextend_List_Table_data = [];
			foreach( $all_custom_post_type as $slug => $val) {

				$current_list_Table_data = [
					'name'			=> '<strong>' . $val['labels']['name'] . '</strong>',
					'slug'			=> $slug,
					'origin'		=> $val['origin']
				];

				// Edit action
				if( strpos($val['origin'], 'load_custom_post_type_wpextend') === false ) {
					$current_list_Table_data['action_edit'] = [
						'id' => $slug
					];
				}
				
				// Delete action
				if( $val['origin'] == 'Database' ){

					$current_list_Table_data['action_delete'] = [
						'action' => 'delete_custom_post_type',
						'id' => $slug,
						'_wpnonce' => wp_create_nonce( 'delete_custom_post_type' )
					];
				}
				
				$Wpextend_List_Table_data[] = $current_list_Table_data;
			}

			ob_start();
			$args_Wpextend_List_Table = [
				'data' 		=> $Wpextend_List_Table_data,
				'columns'	=> [
					'name'		=> 'Name',
					'slug'		=> 'Slug',
					'origin'	=> 'Origin'
				],
				'per_page'	=> 200
			];
			$Wpextend_List_Table = new ListTable($args_Wpextend_List_Table);
			$Wpextend_List_Table->prepare_items();
			$Wpextend_List_Table->display();
			$retour_html .= ob_get_contents();
			ob_end_clean();
		}	
		
		// Add / edit custom post type form
		if(isset($_GET['id'])){

			$retour_html .= '<div class="accordion_wpextend" active="0"><h2>Edit</h2><div>';
			$custom_post = ( isset($this->custom_post_type_wpextend[ $_GET['id'] ]) ) ? new SinglePostType( $_GET['id'], $this->custom_post_type_wpextend[ $_GET['id'] ] ) : new SinglePostType($_GET['id']);
			$retour_html .= $custom_post->render_form_edit();
		}
		else{

			$retour_html .= '<div class="accordion_wpextend"><h2>Add</h2><div>';
			$retour_html .= SinglePostType::render_form_create();
		}
		$retour_html .= '</div></div>';

		// return
		echo $retour_html;
	}



	/**
 	* Update private variable PostType to add new setting
	*/
	public function add_new( $labels, $slug, $args, $taxonomy, $annex_args ){

		if(
			is_array( $labels ) &&
			!empty( $slug ) &&
			is_array( $args ) &&
			is_array( $taxonomy ) &&
			is_array( $annex_args )
		) {

			$new_item_to_add = [
				$slug => [
					'labels' => $labels,
					'args' => $args,
					'taxonomy' => $taxonomy,
					'annex_args' => $annex_args
				]
			];
			
			if( file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {
				$actual_content_json_file = json_decode(file_get_contents(WPEXTEND_JSON_DIR . self::$json_file_name), true);
				$new_content_json_file = array_merge($actual_content_json_file, $new_item_to_add);
				return file_put_contents( WPEXTEND_JSON_DIR . self::$json_file_name, json_encode($new_content_json_file, JSON_PRETTY_PRINT) );
			}
			else {
				$actual_content_from_database = $this->get_all_from_database();
				$new_content_database = array_merge($actual_content_from_database, $new_item_to_add);
				return update_option( $this->name_option_in_database , $new_content_database);
			}
		}

		return false;
	}


	
	/**
 	* Update private variable PostType to add new setting
	*/
	public function delete($slug ){

		$actual_content_from_database = $this->get_all_from_database();
		if( array_key_exists( $slug, $actual_content_from_database ) ){
			unset( $actual_content_from_database[$slug] );
			$actual_content_from_database = ( $actual_content_from_database ) ?: null;
			return update_option( $this->name_option_in_database , $actual_content_from_database);
		}

		return false;
	}



    /**
    * Retrieve all custom post registered
    */
	 public function get_all($single = false){

		$all_custom_post_wpextend = array();
		if( is_array($this->custom_post_type_wpextend) ){
			foreach($this->custom_post_type_wpextend as $slug => $val) {
			 	if( !in_array($slug, self::$list_reserved_post_types) ){
					$all_custom_post_wpextend[$slug] = ( !$single ) ? $val['labels']['name'] : $val;
				}
			}
		}

		 $return_all_custom_post_wpextend = apply_filters( 'Wpextend_Post_Type_get_all', $all_custom_post_wpextend );

		 return $return_all_custom_post_wpextend;
    }



	/**
    * Retrieve all custom post registered
    */
	 public function get_all_include_base_wordpress(){

		 $all_post_type = array_merge( self::$list_base_post_type, $this->get_all() );
		 return $all_post_type;
	}



	/**
	 * Load JSON file in addition to database
	 * 
	 */
	public function load_json() {

		if( file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {

			$json_content = json_decode(file_get_contents(WPEXTEND_JSON_DIR . self::$json_file_name), true);
			if( is_array($json_content) )
				return $json_content;
		}
		else
			AdminNotice::add_notice( '017', 'Some JSON configuration files do not exist yet. Click <a href="' . add_query_arg( array( 'action' => 'generate_autoload_json_file', '_wpnonce' => wp_create_nonce( 'generate_autoload_json_file' ) ), admin_url( 'admin-post.php' ) ) . '">here</a> to generate them.', 'warning', false );

		return [];
	}



	/**
	 * Load custom post type saved in database
	 * 
	 */
	public function get_all_from_database(){

		// Get option from database
		$all_custom_post_type_wpextend_saved_in_database = get_option( $this->name_option_in_database );
		if( is_array($all_custom_post_type_wpextend_saved_in_database) ) {
			return $all_custom_post_type_wpextend_saved_in_database;
		}
		return [];
	}



	/**
	 * Create JSON file if doesn't exist
	 * 
	 */
	public function generate_autoload_json_file() {

		if( ! file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {
			if( touch(WPEXTEND_JSON_DIR . self::$json_file_name) )
				AdminNotice::add_notice( '015', self::$json_file_name .' file successfully created.', 'success' );
			else
				AdminNotice::add_notice( '016', 'unable to create ' . self::$json_file_name, 'error' );
		}
    }


}
