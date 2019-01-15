<?php


/**
 *
 */
class Wpextend_Post_Type {



	 private static $_instance;
	 public $intial_post_type;
	 public $custom_post_type_wpextend;
	 public $name_option_in_database = '_custom_post_type_buzzpress';
	 static public $admin_url = '_custom_post_type';
	 static public $list_base_post_type = array('post' => 'Post', 'page' => 'Page');
	 static public $list_reserved_post_types = array('post', 'page', 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'action', 'author', 'order', 'theme');



	/**
	* Static method which instance post_type_wpextend
 	*/
	public static function getInstance() {
		 if (is_null(self::$_instance)) {
			  self::$_instance = new Wpextend_Post_Type();
		 }
		 return self::$_instance;
	}



	/**
	* The constructor.
	*
	* @return void
	*/
	private function __construct() {

		// Get option from database
		$this->custom_post_type_wpextend = get_option( $this->name_option_in_database );
		if( !is_array( $this->custom_post_type_wpextend ) ) {
			$this->custom_post_type_wpextend = array();
		}
		// pre($this->custom_post_type_wpextend);die;

		// Load initial custom post type
		$this->load_custom_post_defaut();

		// Configure hooks
		$this->create_hooks();
	}



	/**
	* Get and load custom post type inital
	*
	*/
	public function load_custom_post_defaut(){

		$this->custom_post_type_wpextend = apply_filters( 'load_custom_post_type_wpextend', $this->custom_post_type_wpextend );
	}



	/**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

		// Initialise les customs posts
		add_action( 'init', array( $this, 'initialize') );
		add_action( 'init', array( $this, 'initialize_multiple_post_thumbnails') );
	  	add_action( 'admin_post_add_custom_post_type_wpextend', 'Wpextend_Single_Post_Type::add_new' );
	  	add_action( 'admin_post_delete_custom_post_type', 'Wpextend_Single_Post_Type::delete' );
	  	add_action( 'admin_post_import_wpextend_custom_post_type', array($this, 'import') );
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
					$custom_post = new Wpextend_Single_Post_Type( $slug, $val );
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
				    )	;
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
		$retour_html = Wpextend_Render_Admin_Html::header('Custom Post Type');



		// Display table with all custom post type
		$all_custom_post_type = $this->get_all_include_base_wordpress();
		if( is_array($all_custom_post_type) && count($all_custom_post_type) > 0 ) {

			$Wpextend_List_Table_data = [];
			foreach( $all_custom_post_type as $key => $val) {

				$current_list_Table_data = [
					'name'	=> $val,
					'action_edit' => [
						'id' => $key
					]
				];

				if( !in_array($key, self::$list_reserved_post_types) ){

					$current_list_Table_data_delete = [
						'action_delete' => [
							'action' => 'delete_custom_post_type',
							'id' => $key,
							'_wpnonce' => wp_create_nonce( 'delete_custom_post_type' )
						]
					];
				}
				else{
					$current_list_Table_data_delete = [];
				}
				
				$Wpextend_List_Table_data[] = array_merge($current_list_Table_data, $current_list_Table_data_delete);
			}

			ob_start();
			$args_Wpextend_List_Table = [
				'data' 		=> $Wpextend_List_Table_data,
				'columns'	=> [
					'name'			=> 'Name'
				],
				'per_page'	=> 200
			];
			$Wpextend_List_Table = new Wpextend_List_Table($args_Wpextend_List_Table);
			$Wpextend_List_Table->prepare_items();
			$Wpextend_List_Table->display();
			$retour_html .= ob_get_contents();
			ob_end_clean();
		}	
		


		// Add custom psot type form
		if(isset($_GET['id'])){

			$custom_post = ( isset($this->custom_post_type_wpextend[ $_GET['id'] ]) ) ? new Wpextend_Single_Post_Type( $_GET['id'], $this->custom_post_type_wpextend[ $_GET['id'] ] ) : new Wpextend_Single_Post_Type($_GET['id']);
			$retour_html .= $custom_post->render_form_edit();
		}
		else{
			$retour_html .= Wpextend_Single_Post_Type::render_form_create();
		}

		// return
		echo $retour_html;
	}



	/**
 	* Use update_option to save in Wordpress database
 	*
 	* @return boolean
 	*/
 	public function save() {

 		return update_option( $this->name_option_in_database , $this->custom_post_type_wpextend);
 	}



	/**
 	* Update private variable Wpextend_Post_Type to add new setting
	*/
	public function add_new( $labels, $slug, $args, $taxonomy, $annex_args ){

		if(
			is_array( $labels ) &&
			!empty( $slug ) &&
			is_array( $args ) &&
			is_array( $taxonomy ) &&
			is_array( $annex_args )
		) {
			$this->custom_post_type_wpextend[$slug] = array( 'labels' => $labels, 'args' => $args, 'taxonomy' => $taxonomy, 'annex_args' => $annex_args );
		}
	}


	/**
 	* Update private variable Wpextend_Post_Type to add new setting
	*/
	public function delete($slug){

		if( array_key_exists( $slug, $this->custom_post_type_wpextend ) ){
			unset( $this->custom_post_type_wpextend[$slug] );
		}
	}



    /**
    * Retrieve all custom post registered
    */
	 public function get_all(){

		$all_custom_post_wpextend = array();
		if( is_array($this->custom_post_type_wpextend) ){
			foreach($this->custom_post_type_wpextend as $slug => $val) {
			 	if( !in_array($slug, self::$list_reserved_post_types) ){
					$all_custom_post_wpextend[$slug] = $val['labels']['name'];
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
    * Import function
    */
    public function import(){
		
		// Check valid nonce
		$action_nonce = ( isset($_GET['action']) ) ? $_GET['action'] : $_POST['action'];
		check_admin_referer($action_nonce);

		if( isset( $_POST['wpextend_custom_post_type_to_import'] ) && !empty($_POST['wpextend_custom_post_type_to_import']) ) {

			$this->custom_post_type_wpextend = json_decode( stripslashes($_POST['wpextend_custom_post_type_to_import']), true );
		}
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' );
			$this->custom_post_type_wpextend = json_decode( $data_json_file, true );
		}
		else{
			exit;
		}

		// Save in Wordpress database
		if( is_array($this->custom_post_type_wpextend) ){

			$this->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}

}
