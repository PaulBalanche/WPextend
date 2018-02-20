<?php


/**
 *
 */
class Wpextend_Post_Type {



	 private static $_instance;
	 public $intial_post_type;
	 public $custom_post_type_buzzpress;
	 public $name_option_in_database = '_custom_post_type_buzzpress';
	 static public $admin_url = 'buzzpress_custom_post_type';
	 static public $list_base_post_type = array('post' => 'Post', 'page' => 'Page');



	/**
	* Static method which instance post_type_buzzpress
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
		$this->custom_post_type_buzzpress = get_option( $this->name_option_in_database );
		if( !is_array( $this->custom_post_type_buzzpress ) ) {
			$this->custom_post_type_buzzpress = array();
		}

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

		$this->custom_post_type_buzzpress = apply_filters( 'load_custom_post_type_buzzpress', $this->custom_post_type_buzzpress );
	}




	/**
	*
	*
	* @return void
	*/
	public function initialize() {

		if( is_array($this->custom_post_type_buzzpress) ){
			foreach( $this->custom_post_type_buzzpress as $slug => $val ) {
				$custom_post = new Buzzpress_Single_Post_Type( $slug, $val );
				$custom_post->register_custom_post_type();
			}
		}
	}



	/**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

		// Initialise les customs posts
		add_action( 'init', array( $this, 'initialize') );
	  	add_action( 'admin_post_add_custom_post_type_buzzpress', 'Buzzpress_Single_Post_Type::add_new' );
	  	add_action( 'admin_post_delete_custom_post_type', 'Buzzpress_Single_Post_Type::delete' );
	  	add_action( 'admin_post_import_buzzpress_custom_post_type', array($this, 'import') );
	}



	/**
	* Render HTML admin page
	*
	* @return string
	*/
	public function render_admin_page() {

		// Header page & open form
		$retour_html = Wpextend_Render_Admin_Html::header('Custom Post Type');

		// Get all custom type to create fieldset
		$all_custom_post_type = $this->get_all();
		$retour_html .= '<ul>';
		foreach( $all_custom_post_type as $key => $val) {

			$retour_html .= '<li>'.$val.' <a href="'.add_query_arg( 'id', $key ).'" >edit</a> | <a href="'.add_query_arg( array( 'action' => 'delete_custom_post_type', 'id' => $key ), admin_url( 'admin-post.php' ) ).'" >delete</a></li>';
		}
		$retour_html .= '</ul>';

		// Add custom psot type form
		if(isset($_GET['id'])){

			$custom_post = new Buzzpress_Single_Post_Type( $_GET['id'], $this->custom_post_type_buzzpress[ $_GET['id'] ] );
			$retour_html .= $custom_post->render_form_edit();
		}
		else{
			$retour_html .= Buzzpress_Single_Post_Type::render_form_create();
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

 		return update_option( $this->name_option_in_database , $this->custom_post_type_buzzpress);
 	}



	/**
 	* Update private variable Wpextend_Post_Type to add new setting
	*/
	public function add_new( $labels, $slug, $args, $taxonomy ){

		if(
			( !empty( $labels ) && is_array( $labels ) ) &&
			!empty( $slug ) &&
			( !empty( $args ) && is_array( $args ) ) &&
			( !empty( $taxonomy ) && is_array( $taxonomy ) )
		) {

			$this->custom_post_type_buzzpress[$slug] = array( 'labels' => $labels, 'args' => $args, 'taxonomy' => $taxonomy );
		}
	}


	/**
 	* Update private variable Wpextend_Post_Type to add new setting
	*/
	public function delete($slug){

		if( array_key_exists( $slug, $this->custom_post_type_buzzpress ) ){
			unset( $this->custom_post_type_buzzpress[$slug] );
		}
	}



    /**
    * Retrieve all custom post registered
    */
	 public function get_all(){

		 $all_custom_post_buzzpress = array();
		 if( is_array($this->custom_post_type_buzzpress) ){
			 foreach($this->custom_post_type_buzzpress as $slug => $val) {
				 $all_custom_post_buzzpress[$slug] = $val['labels']['name'];
			 }
		}

		 $return_all_custom_post_buzzpress = apply_filters( 'Wpextend_Post_Type_get_all', $all_custom_post_buzzpress );

		 return $return_all_custom_post_buzzpress;
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

		if( isset( $_POST['buzzpress_custom_post_type_to_import'] ) && !empty($_POST['buzzpress_custom_post_type_to_import']) ) {

			$this->custom_post_type_buzzpress = json_decode( stripslashes($_POST['buzzpress_custom_post_type_to_import']), true );
		}
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' );
			$this->custom_post_type_buzzpress = json_decode( $data_json_file, true );
		}
		else{
			exit;
		}

		// Save in Wordpress database
		if( is_array($this->custom_post_type_buzzpress) ){

			$this->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}

}
