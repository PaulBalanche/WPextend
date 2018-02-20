<?php


/**
*
*/
class Wpextend_Custom_Field {



	private static $_instance;
	public $Wpextend_Custom_Field;
	public $name_option_in_database = '_buzzpress_custom_field';
	static public $admin_url = 'wpextend_custom_field';



	/**
	* Static method which instance Wpextend_Custom_Field
	*/
	public static function getInstance() {
		if( is_null(self::$_instance) ) {
			self::$_instance = new Wpextend_Custom_Field();
		}
		return self::$_instance;
	}



	/**
	* The constructor
	*/
	private function __construct() {

		// Get option from database
		$this->Wpextend_Custom_Field = get_option( $this->name_option_in_database );
		if( !is_array( $this->Wpextend_Custom_Field ) ) {
			$this->Wpextend_Custom_Field = array();
		}

		// Create a filter
		$this->Wpextend_Custom_Field = apply_filters( 'wpextend_custom_field_after_get_option', $this->Wpextend_Custom_Field );

		// Configure hooks
		$this->create_hooks();

		// Initialisation des metaboxes
		$this->initialize_metaboxes();
		//
		// update_option( $this->name_option_in_database, false );
	}



	/**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

		// admin_enqueue_scripts
		add_action('admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );

		// $_POST or $_GET traitment if necessary
		add_action( 'admin_post_add_metabox_buzzpress', array( $this, 'add_new_metabox') );
		add_action( 'admin_post_add_custom_field_buzzpress', array( $this, 'add_new_custom_field') );
		add_action( 'admin_post_delete_metabox_buzzpress', array( $this, 'delete_metabox' ) );
		add_action( 'admin_post_delete_custom_field_buzzpress', array( $this, 'delete_custom_field') );

		add_action( 'admin_post_import_wpextend_custom_field', array($this, 'import') );

		// Autoriser les fichiers SVG
		add_filter( 'upload_mimes', array( $this, 'wpc_mime_types') );
	}



	/**
	* Wordpress Enqueues functions
	*
	*/
	static public function script_admin(){

		wp_enqueue_style( 'style_admin_wpextend_custom_field', WPEXTEND_ASSETS_URL . 'style/admin/custom_field.css', false, true );

		wp_enqueue_script( 'script_admin_wpextend_custom_field', WPEXTEND_ASSETS_URL . 'js/admin/custom_field.js', array('jquery'));
		wp_enqueue_script( 'script_admin_wpextend_custom_field_setting_page', WPEXTEND_ASSETS_URL . 'js/admin/custom_field_setting_page.js', array('jquery'));
	}



	/**
 	* Explore Wpextend_Custom_Field and create Wpextend_Meta_Boxes instance
	*
 	*/
 	public function initialize_metaboxes() {

		// Get all configured metaboxes
		$all_metaboxes = apply_filters( 'get_all_metabox_before_initialize_it', $this->get_all_metabox() );

		if( is_array( $all_metaboxes ) ){

			// Foreach, create Wpextend_Meta_Boxes instance
			foreach( $all_metaboxes as $key_matebox => $data_metabox ){

				$post_type = apply_filters( 'set_post_type_before_instance_metabox', $data_metabox['post_type'] );
				$instance_metabox = new Wpextend_Meta_Boxes( $post_type, $key_matebox, $data_metabox['name'], $data_metabox['fields'] );
			}
		}
 	}



	/**
	* Autoriser les fichiers SVG
	*
	*/
	public function wpc_mime_types( $mimes ){

	  $mimes['svg'] = 'image/svg+xml';
	  return $mimes;
	}




	/**
 	* Render HTML admin page
 	*
 	* @return string
 	*/
 	public function render_admin_page() {

		// If post_type doesn't exists > create it to admin interface
		$all_custom_post = Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress();
		foreach($all_custom_post as $key_post_type => $post_type){
			if( !array_key_exists($key_post_type, $this->Wpextend_Custom_Field) ){
				$this->Wpextend_Custom_Field[$key_post_type] = array( 'default' => array( 'default' => array() ) );
			}
		}

		// Create new tab to render clean visual
		$tab_final_to_show = array();
		foreach( $this->Wpextend_Custom_Field as $post_type => $list_category ) {

			$sub_category = 'default';

			if( strpos($post_type, '::') !== false){
				$data_post_type = explode( '::', $post_type);
				$post_type = $data_post_type[0];
				$sub_category = $data_post_type[1];
			}

			if( !array_key_exists($post_type, $tab_final_to_show) )
				$tab_final_to_show[$post_type] = array();

			$tab_final_to_show[$post_type][$sub_category] = $list_category;
		}

		$retour_html = '';

		// Header page & open form
 		$retour_html .= Wpextend_Render_Admin_Html::header('Custom Fields');

		// Render actual metabox and custom fields
		$retour_html .= '<div class="tabs"><ul>';
		foreach($tab_final_to_show as $post_type_root => $list_category )
			$retour_html .= '<li><a href="#'.$post_type_root.'">'.ucfirst($post_type_root).'</a></li>';
		$retour_html .= '</ul>';

		foreach( $tab_final_to_show as $post_type_root => $list_category_post_type ){

			$retour_html .= '<div id="'.$post_type_root.'" class="accordionBuzzpress">';

			foreach( $list_category_post_type as $post_type => $list_category ) {

				if( $post_type_root == Wpextend_Section_Pc::$name_section_register_post_type ){
					$post_type_temp = $post_type;
					$name_post_type = ucfirst($post_type);
					$post_type = $post_type_root.'::'.$post_type;
				}
				else{
					$post_type = $post_type_root;
					$name_post_type = ucfirst($post_type);
				}

				$retour_html .= '<h2>'.$name_post_type.'</h2><div class="accordionPostType '.$post_type.'">';

				if( is_array($list_category) ){

					$retour_html .= '<div class="tabs"><ul>';
					// Each Category : default = default
					foreach($list_category as $key_category => $list_type ){
						if( $key_category != 'default' )
							$retour_html .= '<li><a href="#tab_'.$key_category.'">'.$key_category.'</a></li>';
					}
					$retour_html .= '</ul>';

					// Each Category : default = default
					foreach( $list_category as $key_category => $list_type ){

						if( $key_category != 'default' )
							$retour_html .= '<div id="tab_'.$key_category.'" class="li_subPostType '.$key_category.' accordionBuzzpress">';

						// Each Type : default = default
						foreach( $list_type as $key_type => $list_metabox ){

							if( $key_type != 'default' )
								$retour_html .= '<h3>'.$key_type.'</h3><div class="accordionKeyType '.$key_type.'">';

							if( is_array($list_metabox) ){

								$retour_html .= '<ul class="ulMetabox">';

								foreach( $list_metabox as $key_metabox => $data_metabox ){

									if( is_array($data_metabox) && array_key_exists('name', $data_metabox) && array_key_exists('fields', $data_metabox) ){
										$retour_html .= '<li class="liMetabox"><h4>'.$data_metabox['name'].' (<a href="'.add_query_arg( array( 'action' => 'delete_metabox_buzzpress', 'post_type' => $post_type, 'category' => $key_category, 'type' => $key_type, 'metabox' => $key_metabox, '_wpnonce' => wp_create_nonce( 'delete_metabox_buzzpress' ) ), admin_url( 'admin-post.php' ) ).'">Delete</a>)</h4><ul>';

										// Each fields
										foreach( $data_metabox['fields'] as $key => $val ){

											$retour_html .= '<li class="liCustomField">'.$val['name'].' | '.$val['type'];

											if( array_key_exists('indexable', $val) && $val['indexable'] == 1 )
												$retour_html .= ' <i>indexable</i>';

											if( array_key_exists('repeatable', $val) && $val['repeatable'] == 1 )
												$retour_html .= ' <i>repeatable</i>';

											$retour_html .=' (<a href="'.add_query_arg( array( 'action' => 'delete_custom_field_buzzpress', 'post_type' => $post_type, 'category' => $key_category, 'type' => $key_type, 'metabox' => $key_metabox, 'id' => $key, '_wpnonce' => wp_create_nonce( 'delete_custom_field_buzzpress' ) ), admin_url( 'admin-post.php' ) ).'">Delete</a>)</li>';
										}

										$retour_html .= Buzzpress_Single_Custom_Field::render_form_create( $post_type, $key_category, $key_type, $key_metabox );

										$retour_html .= '</ul></li>';
									}
								}

								$retour_html .= '<div class="clear"></div></ul>';
							}

							// Form add metabox
							$retour_html .= '<div class="form_add_metabox form_add_elt_buzzpress">';

							$retour_html .= '<input type="button" class="add_new_metabox button button-primary" value="New metabox">';

					 		$retour_html .= Wpextend_Render_Admin_Html::form_open( admin_url( 'admin-post.php' ), 'add_metabox_buzzpress', 'add_metabox_buzzpress', 'hidden' );

					 		$retour_html .= Wpextend_Render_Admin_Html::table_edit_open();
					 		$retour_html .= Buzzpress_Type_Field::render_input_text( 'Name', 'name' );
							$retour_html .= Buzzpress_Type_Field::render_input_hidden( 'post_type', $post_type );
							$retour_html .= Buzzpress_Type_Field::render_input_hidden( 'category', $key_category );
							$retour_html .= Buzzpress_Type_Field::render_input_hidden( 'type', $key_type );
					 		$retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

					 		$retour_html .= Wpextend_Render_Admin_Html::form_close( 'Add metabox' );

							$retour_html .= '</div>';
							// END Form add metabox

							if( $key_type != 'default' )
								$retour_html .= '</div>';
						}

						if( $key_category != 'default' )
							$retour_html .= '</div>';
					}
					$retour_html .= '</ul></div>';
				}
				$retour_html .= '</div>';
	 		}
			$retour_html .= '</div>';
		}
		$retour_html .= '</div>';

 		// return
 		echo $retour_html;
 	}



	/**
   	* Import
   	*
   	*/
   	public function import() {

 		// Check valid nonce
 		$action_nonce = ( isset($_GET['action']) ) ? $_GET['action'] : $_POST['action'];
		check_admin_referer($action_nonce);

		if( isset( $_POST['wpextend_custom_field_to_import'] ) && !empty($_POST['wpextend_custom_field_to_import']) ) {

			$this->Wpextend_Custom_Field = json_decode( stripslashes($_POST['wpextend_custom_field_to_import']), true );
		}
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_DIR . '/inc/import/' . $_GET['file'] . '.json' );
			$this->Wpextend_Custom_Field = json_decode( $data_json_file, true );
		}
		else{
			exit;
		}

		// Save in Wordpress database
		if( is_array($this->Wpextend_Custom_Field) ){

			$this->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}



	/**
 	* Update private variable Wpextend_Custom_Field to add new metabox
	*
	*/
	public function add_new_metabox() {

		// Check valid nonce
		check_admin_referer($_POST['action']);

		if( isset( $_POST['name'], $_POST['post_type'], $_POST['category'], $_POST['type'] ) ) {

			// Protect data
			$name				= sanitize_text_field( $_POST['name'] );
			$id				= $this->get_unique_id_metabox( $name );
			$post_type		= sanitize_text_field( $_POST['post_type'] );
			$category		= sanitize_text_field( $_POST['category'] );
			$type				= sanitize_text_field( $_POST['type'] );

			// Traitement de la sous catégorie default
			if( strpos($post_type, '::') !== false){
				$post_type_temp = explode( '::', $post_type);
				if( $post_type_temp[1] == 'default' )
					$post_type = $post_type_temp[0];
			}

			// Test if postType exists in tab
			if( !array_key_exists($post_type, $this->Wpextend_Custom_Field) )
				$this->Wpextend_Custom_Field[ $post_type ] = array();

			// Test if category exists in tab
			if( !array_key_exists($category, $this->Wpextend_Custom_Field[$post_type]) )
				$this->Wpextend_Custom_Field[$post_type][ $category ] = array();

			// Test if type exists in tab
			if( !array_key_exists($type, $this->Wpextend_Custom_Field[$post_type][$category]) )
				$this->Wpextend_Custom_Field[$post_type][$category][ $type ] = array();

			// Add metabox
			$this->Wpextend_Custom_Field[$post_type][$category][$type][ $id ] = array( 'name' => $name, 'fields' => array() );

			// Save in Wordpress database
			$this->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
				exit;
			}
		}
	}



	/**
 	* Update private variable $wpextend_global_settings to add new custom field
	*
	*/
	public function add_new_custom_field(){

		// Check valid nonce
		check_admin_referer($_POST['action']);

		if( isset( $_POST['name'], $_POST['post_type'], $_POST['category'], $_POST['type'], $_POST['type_field'], $_POST['metabox'] ) ) {

			// Protect data
			$name				= sanitize_text_field( $_POST['name'] );
			$id				= sanitize_title( $name );
			$post_type		= sanitize_text_field( $_POST['post_type'] );
			$category		= sanitize_text_field( $_POST['category'] );
			$type				= sanitize_text_field( $_POST['type'] );
			$type_field		= sanitize_text_field( $_POST['type_field'] );
			$metabox			= sanitize_text_field( $_POST['metabox'] );
			$options			= false;

			// Traitement de la sous catégorie default
			if( strpos($post_type, '::') !== false){
				$post_type_temp = explode( '::', $post_type);
				if( $post_type_temp[1] == 'default' )
					$post_type = $post_type_temp[0];
			}

			// Champs repeatable
			$repeatable		= ( isset( $_POST['repeatable'] ) && is_array( $_POST['repeatable'] ) && $_POST['repeatable'][0] == true ) ? true : false;

			// Champs indexable
			$indexable		= ( isset( $_POST['indexable'] ) && is_array( $_POST['indexable'] ) && $_POST['indexable'][0] == true ) ? true : false;

			if( $type_field == 'select' || $type_field == 'radio' || $type_field == 'checkbox' ){
				$options = $_POST['options'];
			}
			if( is_array($options) ){
				foreach( $options as $key => $val ){
					$options[$key] = sanitize_text_field( $val );
				}
			}

			if( $type_field == 'select_post_type' && isset( $_POST['post_type_options'] ) ){
				$options = sanitize_text_field( $_POST['post_type_options'] );
			}

			// Test if fields exists in tab
			if( !array_key_exists('fields', $this->Wpextend_Custom_Field[$post_type][$category][$type][ $metabox ]) )
				$this->Wpextend_Custom_Field[$post_type][$category][$type][ $metabox ]['fields'] = array();

			// Add field
			$this->Wpextend_Custom_Field[$post_type][$category][$type][ $metabox ]['fields'][$id] = array('name' => $name, 'type' => $type_field);

			if( $type_field == 'select' || $type_field == 'select_post_type' || $type_field == 'radio' || $type_field == 'checkbox' )
				$this->Wpextend_Custom_Field[$post_type][$category][$type][ $metabox ]['fields'][$id]['options'] = $options;
			if($indexable)
				$this->Wpextend_Custom_Field[$post_type][$category][$type][ $metabox ]['fields'][$id]['indexable'] = true;
			if($repeatable)
				$this->Wpextend_Custom_Field[$post_type][$category][$type][ $metabox ]['fields'][$id]['repeatable'] = true;

			// Save in Wordpress database
			$this->save();

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
	}



	/**
 	* Delete metabox in attribute Wpextend_Custom_Field
	*
	*/
	public function delete_metabox(){

		// Check valid nonce
		check_admin_referer($_GET['action']);
		$error = array();

		if( isset( $_GET['post_type'], $_GET['category'], $_GET['type'], $_GET['metabox'] ) ) {

			// Protect data
			$post_type		= sanitize_text_field( $_GET['post_type'] );
			$category		= sanitize_text_field( $_GET['category'] );
			$type				= sanitize_text_field( $_GET['type'] );
			$metabox			= sanitize_text_field( $_GET['metabox'] );

			// Traitement de la sous catégorie default
			if( strpos($post_type, '::') !== false){
				$post_type_temp = explode( '::', $post_type);
				if( $post_type_temp[1] == 'default' )
					$post_type = $post_type_temp[0];
			}

			// Get all metaboxes saved
			$actual_metaboxes = $this->get_all_metabox(true);
  			if( is_array($actual_metaboxes) && array_key_exists($metabox, $actual_metaboxes ) ){

				if(
					array_key_exists( $post_type, $this->Wpextend_Custom_Field ) &&
				 	array_key_exists( $category, $this->Wpextend_Custom_Field[$post_type] ) &&
					array_key_exists( $type, $this->Wpextend_Custom_Field[$post_type][$category] ) &&
					array_key_exists( $metabox, $this->Wpextend_Custom_Field[$post_type][$category][$type] )
				){
					// Delete element in attribute Wpextend_Custom_Field
					unset( $this->Wpextend_Custom_Field[$post_type][$category][$type][$metabox] );

					// Save in Wordpress database
					$this->save();
				}
				else
					$error = array( 'message' => 'error2', 'data' => $post_type.' - '.$category.' - '.$type.' - '.$metabox );
			}
			else
					$error = array( 'message' => 'error1', 'data' => $post_type.' - '.$category.' - '.$type.' - '.$metabox );

			if( !isset( $_POST['ajax'] ) ) {

				if( count($error) > 0 )
					$goback = add_query_arg( array_merge(array('udpate' =>'false'), $error), wp_get_referer() );
				else
					$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );

				wp_safe_redirect( $goback );
				exit;
			}
		}
	}



	/**
 	* Delete custom_field in attribute Wpextend_Custom_Field
	*
	*/
	public function delete_custom_field(){

		// Check valid nonce
		check_admin_referer($_GET['action']);
		$error = array();

		if( isset( $_GET['post_type'], $_GET['category'], $_GET['type'], $_GET['metabox'], $_GET['id'] ) ) {

			// Protect data
			$post_type		= sanitize_text_field( $_GET['post_type'] );
			$category		= sanitize_text_field( $_GET['category'] );
			$type				= sanitize_text_field( $_GET['type'] );
			$metabox			= sanitize_text_field( $_GET['metabox'] );
			$id_field		= sanitize_text_field( $_GET['id'] );

			// Traitement de la sous catégorie default
			if( strpos($post_type, '::') !== false){
				$post_type_temp = explode( '::', $post_type);
				if( $post_type_temp[1] == 'default' )
					$post_type = $post_type_temp[0];
			}

			// Get all metaboxes saved
			$actual_metaboxes = $this->get_all_metabox(true);
  			if( is_array($actual_metaboxes) && array_key_exists($metabox, $actual_metaboxes ) ){

				if(
					array_key_exists( $post_type, $this->Wpextend_Custom_Field ) &&
				 	array_key_exists( $category, $this->Wpextend_Custom_Field[$post_type] ) &&
					array_key_exists( $type, $this->Wpextend_Custom_Field[$post_type][$category] ) &&
					array_key_exists( $metabox, $this->Wpextend_Custom_Field[$post_type][$category][$type] ) &&
					array_key_exists( $id_field, $this->Wpextend_Custom_Field[$post_type][$category][$type][$metabox]['fields'] )
				){
					// Delete element in attribute Wpextend_Custom_Field
					unset( $this->Wpextend_Custom_Field[$post_type][$category][$type][$metabox]['fields'][$id_field] );

					// Save in Wordpress database
					$this->save();
				}
				else
					$error = array( 'message' => 'error2');
			}
			else
				$error = array( 'message' => 'error1');

			if( !isset( $_POST['ajax'] ) ) {

				if( count($error) > 0 )
					$goback = add_query_arg( $error, wp_get_referer() );
				else
					$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );

				wp_safe_redirect( $goback );
			}
			exit;
		}
	}



	 /**
  	 * Retrieve all metabox juste using loop in $this->Wpextend_Custom_Field
 	 *
 	 * @return array
  	 */
		 public function get_all_metabox($name_only = false){

			$all_metabox = array();
			if( is_array( $this->Wpextend_Custom_Field ) ){

				// Each Post Type
				foreach( $this->Wpextend_Custom_Field as $post_type => $list_category ) {

					if( is_array($list_category) ){

						// Each Category : default = default
						foreach($list_category as $key_category => $list_type ){

							if( is_array($list_type) ){

								// Each Type : default = default
								foreach( $list_type as $key_type => $list_metabox ){

									if( is_array($list_metabox) ){

										// Each Type : default = default
										foreach( $list_metabox as $key_metabox => $data_metabox ){

											if( is_array($data_metabox) && array_key_exists('name', $data_metabox) && array_key_exists('fields', $data_metabox) ){

												if($name_only)
													$all_metabox[ $key_metabox ] = $data_metabox['name'];
												else{
													$all_metabox[ $key_metabox ] = $data_metabox;
													$all_metabox[ $key_metabox ]['post_type'] = $post_type;
													$all_metabox[ $key_metabox ]['key_category'] = $key_category;
													$all_metabox[ $key_metabox ]['key_type'] = $key_type;
												}
											}
										}
									}
								}
							}
						}
					}
				}
	  	}
			return $all_metabox;
		}



	 /**
  	 * Retrieve information about metaboxes
 	 *
 	 * @return array
  	 */
    public function get_informations_metabox($metabox_ID){

		 $actual_metaboxes = $all_metaboxes = apply_filters( 'get_all_metabox_before_initialize_it', $this->get_all_metabox() );
		 if( is_array($actual_metaboxes) && array_key_exists($metabox_ID, $actual_metaboxes) )
			 return $actual_metaboxes[$metabox_ID];
		else
			return false;
	 }



	 /**
    * Create new metabox ID (key) and test if no exists. If not, generate new ID until find uniq ID
	 *
    */
	 public function get_unique_id_metabox($name){

		 $unique_id = sanitize_title( $name );
		 $actual_metaboxes = $this->get_all_metabox(true);

		 $is_unique = false;
		 while( !$is_unique ){
			 if( is_array($actual_metaboxes) && array_key_exists($unique_id, $actual_metaboxes ) ){

				 $unique_id = $unique_id . '-' . mt_rand (0, 999);
			 }
			 else
			 	$is_unique = true;
		}

		 return $unique_id;
	 }



	 /**
	 * Delete emtpy elements in attribute Wpextend_Custom_Field
	 *
	 */
	 public function cleaner(){

		$all_metabox = array();
 		if( is_array( $this->Wpextend_Custom_Field ) ){

			// #1. Clean list_type

 			// Each Post Type
 			foreach( $this->Wpextend_Custom_Field as $post_type => $list_category ) {

 				if( is_array($list_category) ){

 					// Each Category : default = default
 					foreach($list_category as $key_category => $list_type ){

 						if( is_array($list_type) ){

 							// Each Type : default = default
 							foreach( $list_type as $key_type => $list_metabox ){

 								if( !is_array($list_metabox) || count($list_metabox) == 0 ){

 									unset( $this->Wpextend_Custom_Field[$post_type][$key_category][$key_type] );
 								}
 							}
 						}
 					}
 				}
			}


			// #2. Clean list_category

 			// Each Post Type
 			foreach( $this->Wpextend_Custom_Field as $post_type => $list_category ) {

 				if( is_array($list_category) ){

 					// Each Category : default = default
 					foreach($list_category as $key_category => $list_type ){

						if( !is_array($list_type) || count($list_type) == 0 ){

							unset( $this->Wpextend_Custom_Field[$post_type][$key_category] );
						}
					}
 				}
 			}

			// #3. Clean post_type

 			// Each Post Type
 			foreach( $this->Wpextend_Custom_Field as $post_type => $list_category ) {

 				if( !is_array($list_category) || count($list_category) == 0 ){

					unset( $this->Wpextend_Custom_Field[$post_type] );
 				}
 			}

		}
	}



    /**
    * Global function save Wpextend_Custom_Field in database
	 *
    */
    public function save(){

		 // Clean attribute Wpextend_Custom_Field before save in database
		 $this->cleaner();

		 return update_option( $this->name_option_in_database, $this->Wpextend_Custom_Field );
    }

}
