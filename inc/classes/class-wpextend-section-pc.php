<?php


/**
*
*/
class Wpextend_Section_Pc {

	private static $_instance;
	public $Wpextend_Section_Pc;
	public $name_option_in_database = '_buzzpress_sections';

	static public $name_section_register_post_type = 'section-buzzpress';
	static public $admin_url = 'buzzpress_sections';
	static public $name_input_hidden_list_section_in_database = 'input_hidden_listing_sections';
	static public $key_list_section_in_database = 'meta_buzzpress_list_sections';



	/**
   * Static method which instance section_pc_wpextend
	*
   */
 	public static function getInstance() {
 		 if (is_null(self::$_instance)) {
 			  self::$_instance = new Wpextend_Section_Pc();
 		 }
 		 return self::$_instance;
 	}



    /**
    * The constructor
	*
    */
    private function __construct() {

		// Get option from database
  		$this->Wpextend_Section_Pc = get_option( $this->name_option_in_database );
  		if( !is_array( $this->Wpextend_Section_Pc ) )
  			$this->Wpextend_Section_Pc = array();
		else
			$this->traitement_des_alias();

		// Configure hooks
 		$this->create_hooks();

		// Add default keys post_type if no exists
		$all_custom_post = Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress();
		foreach($all_custom_post as $key_post_type => $post_type){
			if( !array_key_exists($key_post_type, $this->Wpextend_Section_Pc) ){
				$this->Wpextend_Section_Pc[$key_post_type] = array();
			}
		}
    }



	 /**
	 * Register some Hooks
	 *
	 * @return void
	 */
	 public function create_hooks() {

		// admin_enqueue_scripts
		add_action('admin_enqueue_scripts', array( __CLASS__, 'script_admin' ) );

	   // $_POST traitment if necessary
	   	add_action( 'admin_post_add_category_section_buzzpress', 'Wpextend_Category_Section_Pc::add_new' );
		add_action( 'admin_post_add_type_section_buzzpress', 'Wpextend_Type_Section_Pc::add_new' );
		add_action( 'admin_post_update_type_section_buzzpress', 'Wpextend_Type_Section_Pc::update' );

		add_action( 'admin_post_delete_section_buzzpress', 'Wpextend_Type_Section_Pc::delete' );
		add_action( 'admin_post_delete_category_section_buzzpress', 'Wpextend_Type_Section_Pc::delete' );

		add_action( 'admin_post_import_wpextend_section_pc', array($this, 'import') );

		add_action( 'save_post', array($this, 'save_meta_boxes') );
		add_action( 'add_meta_boxes', array($this, 'add_meta_boxes') );

		// Create section post_type
		add_filter( 'load_custom_post_type_wpextend', array( $this, 'load_custom_post_type' ) );
		// Update post_type during metabox's instancation to allow showing metabox
		add_filter( 'set_post_type_before_instance_metabox', array( $this, 'set_post_type_before_instance_metabox' ) );
		// Filter if metabox must be showed
		add_filter( 'show_metabox_wpextend', array( $this, 'check_show_metabox_wpextend' ), 10, 2 );
		// Filter le main content to add secton HTML
		add_filter( 'the_content', array( $this, 'filter_the_content' ), 10, 2 );

		add_filter( 'wpextend_custom_field_after_get_option', array( $this, 'add_section_posts_types_to_custom_field' ) );

		add_filter( 'admin_body_class', array( $this, 'update_admin_body_class' ) );

		add_filter( 'get_all_metabox_before_initialize_it', array( $this, 'add_alias_in_initialisation_of_metabox' ) );

		// Hook action duplicate_post_post_copy made by duplicate-post plugin : "Called right at the end of the cloning operation."
		add_action( 'dp_duplicate_page', array($this, 'post_duplicate_post'), 10, 3 );
		add_action( 'dp_duplicate_post', array($this, 'post_duplicate_post'), 10, 3 );

		// Hook current_screen to show element in section page edition
		add_action( 'current_screen', array($this, 'section_page_edition') );
	 }



	 /**
	 * Update body class > add iframe is page load in iframe
	 *
	 */
	 public function update_admin_body_class($class){

		 if(isset($_GET['iframe']))
		 	return 'iframe';
	 }



	 /**
	 * Wordpress Enqueues functions
	 *
	 */
	 public static function script_admin() {

		 wp_enqueue_style( 'style_admin_Wpextend_Section_Pc', WPEXTEND_ASSETS_URL . 'style/admin/section_pc.css', false, true );
		 wp_enqueue_script( 'script_admin_Wpextend_Section_Pc', WPEXTEND_ASSETS_URL . '/js/admin/section_pc.js', array('jquery'));
	 }



	 /**
	 * Add metaboxes in post_type concerning and in sections
	 *
	 */
	 public function add_meta_boxes(){

		 // Metabox listing section in each post_type concerned
		 add_meta_box( 'metabox'.$this->name_option_in_database, 'Sections (structure de la page)', array($this, 'show_metabox_listing_sections'), self::getInstance()->get_all_post_type_applied(), 'advanced', 'high' );

		 // Metabox configuration in section
		 add_meta_box( 'metabox_config'.$this->name_option_in_database, 'Configuration de la section', array($this, 'show_main_config_section_pc'), self::$name_section_register_post_type, 'side', 'high' );
	 }



	/**
	* Filter the general "load_custom_post_type_wpextend" function and add section post_type
	*
	*/
	public function load_custom_post_type($initial_custom_post_type){

		$defaut_custom_post_type = array(
			self::$name_section_register_post_type => array(
				'labels' => array(
					'name'						=> 'Sections',
					'singular_name'				=> 'Sections',
					'add_new'					=> 'Ajouter',
					'add_new_item'				=> 'Ajouter un nouvelle section',
					'new_item'					=> 'Nouvelle section',
					'edit_item'					=> 'Éditer la section',
					'view_item'					=> 'Voir la section',
					'all_items'					=> 'Toutes les sections',
					'search_items'				=> 'Chercher une section',
					'parent_item_colon'			=> 'Section parent',
					'not_found'					=> 'Aucune section',
					'not_found_in_trash' 		=> 'Aucune section supprimée'
				),
				'args' => array(
					'description'				=> 'Les sections sont les éléments qui composent une page',
					'public'						=> 'true',
					'capability_type'			=> 'post',
					'hierarchical'				=> false,
					'show_in_menu'				=> false,
					'menu_position'				=> null,
					'rewrite'					=> false,
					'supports'					=> Array( 'title', 'editor', 'thumbnail' )
				),
				'taxonomy' => array(
					'slug' => '',
					'label' => ''
				)
			)
		);

		$initial_custom_post_type = array_merge( $defaut_custom_post_type, $initial_custom_post_type );
		return $initial_custom_post_type;
	}



	/**
	* Filter the general "wpextend_custom_field_after_get_option" function and add section post_type
	*
	*/
	public function add_section_posts_types_to_custom_field($Wpextend_Custom_Field){

		// foreach post_type
		foreach( $this->Wpextend_Section_Pc as $post_type => $category ){

			if( count($category) > 0 ){

				$key_new_post_type = self::$name_section_register_post_type . '::' . $post_type;
				if( !array_key_exists($key_new_post_type, $Wpextend_Custom_Field) )
					$Wpextend_Custom_Field[$key_new_post_type] = array();

				// foreach category
				foreach( $category as $key_category => $data_category ){

					if( !array_key_exists($key_category, $Wpextend_Custom_Field[$key_new_post_type]) )
						$Wpextend_Custom_Field[$key_new_post_type][$key_category] = array();

					// foreach section
				   foreach( $data_category['sections'] as $key_section => $val_section ){

						if(
							!array_key_exists($key_section, $Wpextend_Custom_Field[$key_new_post_type][$key_category]) &&
							!array_key_exists( 'alias', $val_section)
						)
							$Wpextend_Custom_Field[$key_new_post_type][$key_category][$key_section] = array();
					}
				}
			}
		}
		return $Wpextend_Custom_Field;
	}



	/**
	* Filter the general "the_content" function and add sections HTML
	*
	*/
	public function filter_the_content( $content, $show_sections = true ){

		$post = get_post();
		if( $show_sections == true ){

			$instance_post = new Wpextend_Post( $post->ID );
			$tab_section_post = $instance_post->get_sections_pc_wpextend();

			if( is_array($tab_section_post) && count($tab_section_post) > 0 ){
				foreach( $tab_section_post as $section_id ){

					$instance_section_courante = new Wpextend_Post( $section_id );
					$content .= $this->render_front_html( $instance_section_courante );
				}
			}
		}
		return $content;
	}



	/**
	* Update post_type during metabox's instancation to allow showing metabox
	*
	*/
	public function set_post_type_before_instance_metabox($post_type){

		if( strpos($post_type, '::') !== false){
			$post_type = explode( '::', $post_type);
			$post_type = $post_type[0];
		}
		return $post_type;
	}



	/**
	* Filter the general "show_metabox_wpextend" function and test if metabox need to be showed
	*
	*/
	public function check_show_metabox_wpextend($post, $metabox_ID){

		$retour = true;

		if( $post != NULL && $post->post_type == self::$name_section_register_post_type ){

			$data_metabox = Wpextend_Custom_Field::getInstance()->get_informations_metabox($metabox_ID);
			if( is_array($data_metabox) && $data_metabox['key_category'] != 'default' && $data_metabox['key_type'] != 'default' ){

				$retour = false;

				$post_section = new Wpextend_Post($post->ID);
				$type_section = $post_section->get_type_section();

				// Check if post parent correspond to post_type_parent_accepted by this metabox
				$curent_type_parent_ok = true;
				if( strpos($data_metabox['post_type'], '::') !== false){

					$curent_type_parent_ok = false;
					$post_type_parent_accepted = explode( '::', $data_metabox['post_type']);
					$post_type_parent_accepted = $post_type_parent_accepted[1];
					if( get_post_type( $post_section->get_main_parent_id() ) == $post_type_parent_accepted )
						$curent_type_parent_ok = true;
				}

				if( $curent_type_parent_ok && $type_section == $data_metabox['key_category'] . '__' .$data_metabox['key_type'] ){
					$retour = true;
				}
			}
		}

		return $retour;
	}



	/**
	*
	*/
	 public function show_metabox_listing_sections($post){

		 $instance_post_parent = new Wpextend_Post( $post->ID );
		 $default_value_field = $instance_post_parent->get_sections_pc_wpextend();

		 echo self::listing_section( $post->ID, $default_value_field );
	 }



	 /**
	 *
	 */
	 public function show_main_config_section_pc($post){

		 $retour_html = '';

		 $config_section = get_post_meta( $post->ID, $this->name_option_in_database . '_config_section', true);
		 $main_parent_id = ( is_array($config_section) && isset($config_section['parent_id']) ) ? $config_section['parent_id'] : false;
		 $main_parent_id = ( isset($_GET['parent_id']) ) ? $_GET['parent_id'] : $main_parent_id;
		 $type_section = ( is_array($config_section) && isset($config_section['type_section']) ) ? $config_section['type_section'] : false;

		 $retour_html .= Wpextend_Render_Admin_Html::table_edit_open();

		 // Select Type of section
		 $retour_html .= Wpextend_Type_Field::render_input_text( 'Parent ID', 'config_section[parent_id]', $main_parent_id );

		 // Select Type of section
		 $options = $this->get_all_sections_by_post_type(get_post_type( $main_parent_id ));
		 $retour_html .= Wpextend_Type_Field::render_input_select( 'Type', 'config_section[type_section]', $options, $type_section );

		 $retour_html .= Wpextend_Render_Admin_Html::table_edit_close();

		 echo $retour_html;
	 }



	 /**
	 *
	 */
	 public function save_meta_boxes($post_id){

		// Save metabox if post_type is section
 		$post_type = get_post_type( $post_id );
 		if( $post_type == self::$name_section_register_post_type ){

 			if( isset($_POST['config_section']['parent_id']) || isset($_POST['config_section']['type_section']) ){

				$config_actuelle_section = get_post_meta( $post_id, $this->name_option_in_database . '_config_section', true );
				$new_config_section = $config_actuelle_section;

				if( isset($_POST['config_section']['parent_id']) ){
					$instance_post_parent = new Wpextend_Post($_POST['config_section']['parent_id']);
					$instance_post_parent->add_section($post_id);

					$new_config_section['parent_id'] = $_POST['config_section']['parent_id'];
				}

				if( isset($_POST['config_section']['type_section']) ){
					$new_config_section['type_section'] = $_POST['config_section']['type_section'];
				}

				update_post_meta( $post_id, $this->name_option_in_database . '_config_section', $new_config_section );
			}
 		}

		// Save listing section
		if( isset($_POST[self::$name_input_hidden_list_section_in_database]) ){

			$instance_post_parent = new Wpextend_Post($post_id);
			$instance_post_parent->update_sections( json_decode($_POST[self::$name_input_hidden_list_section_in_database]) );
		}
 	}



	/**
	* Render HTML admin page
	*
	* @return string
	*/
  public function render_admin_page(){

	  $retour_html = '';

	  // Header page & open form
	  $retour_html .= Wpextend_Render_Admin_Html::header('Sections');

	  // Render actual metabox and custom fields
	  $retour_html .= '<ul class="ulBuzzpressAdmin">';
	  foreach( $this->Wpextend_Section_Pc as $post_type => $list_category ) {

		  $retour_html .= '<li class="li_postType"><h2>'.$post_type.'</h2><ul>';
		  if( is_array($list_category) ){
			  foreach($list_category as $key_category => $list_section){
				  $retour_html .= '<li class="li_subPostType"><h4>'.$list_section['name'].' (<a href="'.add_query_arg( array( 'action' => 'delete_category_section_buzzpress', 'post_type' => $post_type, 'category' => $key_category), admin_url( 'admin-post.php' ) ).'">Delete</a>)</h4><ul>';
				  foreach( $list_section['sections'] as $key =>$val ){
					  $retour_html .= '<li class="liCustomField">';
					  if( array_key_exists( 'alias', $val ) && $val['alias'] != 'none' ){ $retour_html .= '<i>'; }
					  $retour_html .= str_replace('\"', '&quot;', $val['name']).' ('.$val['file'].') <a href="'.add_query_arg( array('type_post	' => $post_type, 'category' => $key_category, 'id' => $key ) ).'" >edit</a> | <a href="'.add_query_arg( array( 'action' => 'delete_section_buzzpress', 'post_type' => $post_type, 'category' => $key_category, 'id' => $key ), admin_url( 'admin-post.php' ) ).'">Delete</a></li>';
					  if( array_key_exists( 'alias', $val ) && $val['alias'] != 'none' ){ $retour_html .= '</i>'; }
				  }

				  if( isset( $_GET['type_post'], $_GET['category'], $_GET['id'] ) ){
		   		  $instance_Wpextend_Type_Section_Pc = new Wpextend_Type_Section_Pc( $_GET['type_post'], $_GET['category'], $_GET['id'], $this->Wpextend_Section_Pc[$_GET['type_post']][$_GET['category']]['sections'][$_GET['id']] );
		   		  $retour_html .= $instance_Wpextend_Type_Section_Pc->render_form_edit();
		   	  }
		   	  else{
		   		  $retour_html .= Wpextend_Type_Section_Pc::render_form_create($post_type.'__'.$key_category);
		   	  }

				  $retour_html .= '</ul></li>';
			  }
		  }
		  $retour_html .= Wpextend_Category_Section_Pc::render_form_create($post_type);
		  $retour_html .= '</ul></li>';
	  }
	  $retour_html .= '<div class="clear"></div></ul>';

	echo $retour_html;
  }



  /**
  * Import
  *
  */
  public function import() {

	 // Check valid nonce
	 check_admin_referer($_POST['action']);

	 if( isset( $_POST['wpextend_section_pc_to_import'] ) && !empty($_POST['wpextend_section_pc_to_import']) ) {

		 $this->Wpextend_Section_Pc = json_decode( stripslashes($_POST['wpextend_section_pc_to_import']), true );
		 if( is_array($this->Wpextend_Section_Pc) ){

			 // Save in Wordpress database
			 $this->save();

			 if( !isset( $_POST['ajax'] ) ) {
				 $goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				 wp_safe_redirect( $goback );
			 }
			 exit;
		}
	 }
  }



  /**
  * Update private variable Wpextend_Custom_Field to add new metabox
  */
  public function add_new_category($name, $post_type) {

	  if( !empty($name) && !empty($post_type) ) {

		  // Create ID
		  $id_new_category = sanitize_title($name);

		  // Test if no already exists
		  if( !array_key_exists($post_type, $this->Wpextend_Section_Pc) || !array_key_exists($id_new_category, $this->Wpextend_Section_Pc[$post_type]) ) {
			  $this->Wpextend_Section_Pc[$post_type][$id_new_category] = array( 'name' => $name, 'sections' => array() );
		  }
	  }
  }



  /**
  * Update private variable $Wpextend_Section_Pc to add new custom field
  */
  public function add_new_section($name, $description, $post_type, $category, $file, $alias = 'none'){

	  if(
		  array_key_exists( $post_type, Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress() ) &&
		  array_key_exists( $post_type.'__'.$category, $this->get_all_category() )
	  ){

		  if( $alias != 'none' ){

			  $alias_temp = explode( '__', $alias);
			  $post_type_alias = $alias_temp[0];
			  $cat_section_alias = $alias_temp[1];
			  $key_section = $alias_temp[2];

			  // Test if alias exists
			  if(
				  array_key_exists( $key_section, $this->Wpextend_Section_Pc[$post_type_alias][$cat_section_alias]['sections'] ) &&
				 !array_key_exists( $key_section, $this->Wpextend_Section_Pc[$post_type][$category]['sections'] )
			  ){
				  $this->Wpextend_Section_Pc[$post_type][$category]['sections'][$key_section] = array(
					  'alias' => $alias
					);
			  }
		  }
		  elseif( !empty($name) ){

			  // Create ID
			  $id_new_section = sanitize_title($name);

			  // Test if no already exists
			  if( !array_key_exists( $id_new_section, $this->Wpextend_Section_Pc[$post_type][$category]['sections'] ) ){
				  $this->Wpextend_Section_Pc[$post_type][$category]['sections'][$id_new_section] = array(
					  'name'				=> $name,
					  'description'	=> $description,
					  'file'				=> $file
					);
			  }
		  }
	  }
  }



  /**
  * Update private variable $Wpextend_Section_Pc to add new custom field
  */
  public function update_section($id_section, $name, $description, $post_type, $category, $file, $alias = 'none'){

	 if(
		 !empty($id_section) &&
		 array_key_exists( $post_type, Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress() ) &&
		 array_key_exists( $post_type.'__'.$category, $this->get_all_category() ) &&
		 array_key_exists( $id_section, $this->Wpextend_Section_Pc[$post_type][$category]['sections'] )
	 ) {

		 if($alias != 'none'){

			 $alias_temp = explode( '__', $alias);
			 $post_type_alias = $alias_temp[0];
			 $cat_section_alias = $alias_temp[1];
			 $key_section = $alias_temp[2];

			 // Test if alias exists
			 if( array_key_exists( $key_section, $this->Wpextend_Section_Pc[$post_type_alias][$cat_section_alias]['sections'] ) ){

				if( array_key_exists( $key_section, $this->Wpextend_Section_Pc[$post_type][$category]['sections'] ) ){
					unset( $this->Wpextend_Section_Pc[$post_type][$category]['sections'][$id_section] );
				}
				$this->Wpextend_Section_Pc[$post_type][$category]['sections'][$key_section] = array(
					'alias' => $alias
				);
			 }
		 }
		 elseif( !empty($id_section) && !empty($name) ){

			 $this->Wpextend_Section_Pc[$post_type][$category]['sections'][$id_section] = array(
				 'name'				=> $name,
				 'description'		=> $description,
				 'file'				=> $file
			  );
		}


	 }
  }



	/**
   * Update private variable $Wpextend_Section_Pc to add new custom field
   */
   public function delete_category_section_type($post_type, $category){

 	  unset( $this->Wpextend_Section_Pc[$post_type][$category] );
	  if( count($this->Wpextend_Section_Pc[$post_type]) == 0){
		  unset( $this->Wpextend_Section_Pc[$post_type] );
	  }
   }



  /**
  * Update private variable $Wpextend_Section_Pc to add new custom field
  */
  public function delete_section_type($post_type, $category, $id){

	  unset( $this->Wpextend_Section_Pc[$post_type][$category]['sections'][$id] );
  }



  /**
  * Retrieve all post type that section are applied
  *
  * @return array
  */
   public function get_all_post_type_applied(){

		$all_post_type = array();
		if( is_array($this->Wpextend_Section_Pc) ) {

			foreach( $this->Wpextend_Section_Pc as $key => $val ) {
				$all_post_type[] = $key;
			}
		}

		// Return categories
		return $all_post_type;
	}



   /**
	* Retrieve all metabox juste using loop in $this->Wpextend_Custom_Field
   *
   * @return array
	*/
	 public function get_all_category(){

		$all_category = array();
		if( is_array($this->Wpextend_Section_Pc) ) {

			$tab_all_custom_type = Wpextend_Post_Type::getInstance()->get_all_include_base_wordpress();

			foreach( $this->Wpextend_Section_Pc as $key => $val ) {
				foreach( $val as $key2 => $val2 ){
					$all_category[$key.'__'.$key2] = $tab_all_custom_type[$key].' > '.$val2['name'];
				}
			}
		}

		// Return categories
		return $all_category;
	 }



	 /**
 	 * Retrieve all metabox for specific post_type juste using loop in $this->Wpextend_Custom_Field
    *
    * @return array
 	 */
	 public function get_all_sections_by_post_type($post_type){

		 if( $post_type ){
			 $all_sections_by_post_type = array();
			 if( is_array( $this->Wpextend_Section_Pc ) && array_key_exists( $post_type, $this->Wpextend_Section_Pc ) ) {

				 foreach( $this->Wpextend_Section_Pc[$post_type] as $key => $val ) {
					 if( is_array($val['sections']) ){
						 foreach( $val['sections'] as $key2 => $val2 ){
							 $all_sections_by_post_type[$key.'__'.$key2] = stripslashes( $val['name'] ) .' > '. stripslashes( $val2['name'] );
						 }
					 }
				 }
			 }

			 // Return sections by post type
			 return $all_sections_by_post_type;
		 }
	 }



	 /**
	 *
	 */
	 public function save(){

		 foreach($this->Wpextend_Section_Pc as $post_type => $category_section){
			 if( count( $this->Wpextend_Section_Pc[$post_type] ) == 0 ){
				unset( $this->Wpextend_Section_Pc[$post_type] );
			 }
		 }

		  return update_option( $this->name_option_in_database, $this->Wpextend_Section_Pc);
	 }



	 /**
    * Show listing sections
    */
	 public static function listing_section($post_id, $tab_sections){

		 $retour_html = '';

		 $retour_html .= '<ul class="sortable browser_sortable">
			<li class="ui-state-default ui-state-disabled">HAUT DE PAGE</li>';
			if(is_array($tab_sections) && count($tab_sections) > 0){
				foreach($tab_sections as $val){

					$title_section = get_the_title($val);
					if( empty($title_section) ){ $title_section = 'No title...'; }

					$link_add_thickbox = '<a href="'.admin_url().'post.php?post='.$val.'&action=edit&iframe&TB_iframe=true&width=1000&height=550" class="thickbox">'.$title_section.'</a>';
					// $retour_html .= '<li class="ui-state-default" attr_id_sortable="'.$val.'"><a href="post.php?post='.$val.'&action=edit" >'.get_the_title($val).'</a>';
					$retour_html .= '<li class="ui-state-default" attr_id_sortable="'.$val.'">'.$link_add_thickbox;
					$retour_html .= '<span class="delete_panneau_in_config dashicons dashicons-no" onclick="remove_elt_sortable(this);"></span></li>';
				}
			}
		$retour_html .= '<li class="ui-state-default ui-state-disabled add_new_post"><a href="'.admin_url().'post-new.php?post_type='.self::$name_section_register_post_type.'&parent_id='.$post_id.'&iframe&TB_iframe=true&width=600&height=550" class="thickbox button button-primary">+ Nouvelle section</a></li>
		<li class="ui-state-default ui-state-disabled">BAS DE PAGE</li>
		</ul>
		<input type="hidden" name="'.self::$name_input_hidden_list_section_in_database.'" class="input_hidden_list_elt_sortable" value="'.json_encode($tab_sections, JSON_NUMERIC_CHECK).'" />';

		 return $retour_html;
	 }



	 /**
	 * Listing all files (controllers and views)
	 */
	 public function scan_views_controllers(){

		 $tab_files = array();

		 if( is_dir( WPEXTEND_SECTION_CONTROLLERS_DIR ) ){
			if( $dh = opendir( WPEXTEND_SECTION_CONTROLLERS_DIR ) ){
				while( ( $file = readdir($dh) ) !== false ){
					if( filetype(WPEXTEND_SECTION_CONTROLLERS_DIR . $file) == 'file' ){
						$tab_files[$file] = $file;
					}
				}
				closedir($dh);
			}
		}

		return $tab_files;
	 }



	 /**
	 *
	 */
	 public function render_front_html( $instance_section_courante ){

		 $post_type_parent = get_post_type( $instance_section_courante->get_main_parent_id() );
		 $type_section_temp = explode( '__', $instance_section_courante->get_type_section(), 2 );
		 $category_type_section = $type_section_temp[0];
		 $type_section = $type_section_temp[1];

		 $file_to_load = $this->Wpextend_Section_Pc[$post_type_parent][$category_type_section]['sections'][$type_section]['file'];

		 $data_section = get_post( $instance_section_courante->id );

		 // Get all metadata
		 $data_section->meta_data = (object) array();
		 $meta_data_section = get_metadata( 'post', $instance_section_courante->id );
		 foreach( $meta_data_section as $key_data => $val_data ){
			if( strpos( $key_data, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
				$data_section->meta_data->{str_replace( WPEXTEND_PREFIX_DATA_IN_DB, '', $key_data )} = get_post_meta( $instance_section_courante->id, $key_data, true );
			}
		 }

		 // Get all metadata post parent
		 $data_section->post_parent_meta_data = (object) array();
		 $data_section->post_parent_meta_data->ID = $instance_section_courante->get_main_parent_id();
		 $meta_data_section_post_parent = get_metadata( 'post', $data_section->post_parent_meta_data->ID );
		 foreach( $meta_data_section_post_parent as $key_data => $val_data ){
		   if( strpos( $key_data, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
		 	  $data_section->post_parent_meta_data->{str_replace( WPEXTEND_PREFIX_DATA_IN_DB, '', $key_data )} = get_post_meta( $data_section->post_parent_meta_data->ID, $key_data, true );
		   }
		 }

		 include( WPEXTEND_SECTION_CONTROLLERS_DIR . $file_to_load );
		//  include( WPEXTEND_SECTION_VIEWS_DIR . $file_to_load );
	 }



	 /**
	 *
	 */
	 public function get_all_type_section_pc($include_alias = false){

		 $return_get_all_type_section_pc = array();

		 foreach( $this->Wpextend_Section_Pc as $post_type => $category ){

			 foreach( $category as $key_category => $data_category ){

				 foreach( $data_category['sections'] as $key_section => $val_section ){
					 if(
						!$include_alias ||
						!array_key_exists('alias', $val_section) ||
						$val_section['alias'] == 'none'
					){
					 	$return_get_all_type_section_pc[$post_type.'__'.$key_category.'__'.$key_section] = $post_type.' > '.$data_category['name'].' > '.$val_section['name'];
				 	}
				 }
			 }
		 }
		 return $return_get_all_type_section_pc;
	 }



	 /**
	 * If section is an alias, set section informations in alias section
	 *
	 */
	 public function traitement_des_alias(){

		 foreach( $this->Wpextend_Section_Pc as $post_type => $category ){

			 foreach( $category as $key_category => $data_category ){

				 foreach( $data_category['sections'] as $key_section => $val_section ){

					 // Section is an alias > search et get section informations
					 if( array_key_exists('alias', $val_section ) ){
						 $alias_temp = explode( '__', $val_section['alias'] );
						 $post_type_alias = $alias_temp[0];
						 $cat_section_alias = $alias_temp[1];
						 $key_section_alias = $alias_temp[2];

						 if(
							 array_key_exists($post_type_alias, $this->Wpextend_Section_Pc) &&
							 array_key_exists($cat_section_alias, $this->Wpextend_Section_Pc[$post_type_alias]) &&
							 array_key_exists('sections', $this->Wpextend_Section_Pc[$post_type_alias][$cat_section_alias]) &&
							 array_key_exists($key_section_alias, $this->Wpextend_Section_Pc[$post_type_alias][$cat_section_alias]['sections'])
						){
							 $this->Wpextend_Section_Pc[$post_type][$key_category]['sections'][$key_section]['name'] = $this->Wpextend_Section_Pc[$post_type_alias][$cat_section_alias]['sections'][$key_section_alias]['name'];
							 $this->Wpextend_Section_Pc[$post_type][$key_category]['sections'][$key_section]['description'] = $this->Wpextend_Section_Pc[$post_type_alias][$cat_section_alias]['sections'][$key_section_alias]['description'];
							 $this->Wpextend_Section_Pc[$post_type][$key_category]['sections'][$key_section]['file'] = $this->Wpextend_Section_Pc[$post_type_alias][$cat_section_alias]['sections'][$key_section_alias]['file'];
						}
					 }
				 }
			 }
		 }

	 }



	 /**
	 * Add alias in all metabox listing to allow initiliase it
	 *
	 */
	 public function add_alias_in_initialisation_of_metabox( $all_metabox ){

		 $tab_alias = array();

		 foreach( $this->Wpextend_Section_Pc as $post_type => $category ){

			 foreach( $category as $key_category => $data_category ){

				 foreach( $data_category['sections'] as $key_section => $val_section ){

					 // Section is an alias > search et get section informations
					 if( array_key_exists('alias', $val_section ) ){
						 $tab_alias[$val_section['alias']] = array(
							 'post_type' => $post_type,
							 'category' => $key_category
						 );
					}
				}
			}
		}

		if( count($tab_alias) > 0 ){
			foreach( $all_metabox as $key_metbox => $val_metabox){

				$valeur_a_tester = str_replace( self::$name_section_register_post_type . '::', '', $val_metabox['post_type'] . '__' . $val_metabox['key_category'] . '__' . $val_metabox['key_type'] );
				if( array_key_exists($valeur_a_tester, $tab_alias) ){
					$all_metabox[ $key_metbox . '_alias_' . $valeur_a_tester ] = $val_metabox;
					$all_metabox[ $key_metbox . '_alias_' . $valeur_a_tester ]['post_type'] = self::$name_section_register_post_type . '::' . $tab_alias[$valeur_a_tester]['post_type'];
					$all_metabox[ $key_metbox . '_alias_' . $valeur_a_tester ]['key_category'] = $tab_alias[$valeur_a_tester]['category'];
				}
			}
		}

		return $all_metabox;
	 }



	 /**
	 * Hook action duplicate_post_post_copy made by duplicate-post plugin : "Called right at the end of the cloning operation."
	 *
	 */
	 public function post_duplicate_post( $new_post_id, $post, $status ){

	 	// get post ID
	 	if( isset($_GET['post']) ){

	 		$id = $_GET['post'];
	 		$_GET['post'] = null;
	 	}
	 	else if( isset($_POST['post']) ){
	 		
	 		$id = $_POST['post'];
			$_GET['post'] = null;
	 	}

	 	if( isset($id) && is_numeric($id) ){

		 	$instance_post_initial = new Wpextend_Post( $id );
			$tab_section_post = $instance_post_initial->get_sections_pc_wpextend();

			if( is_array($tab_section_post) && count($tab_section_post) > 0 ){

				$instance_post_duplique = new Wpextend_Post( $new_post_id );

				$tab_new_section_duplicate = array();
				foreach( $tab_section_post as $section_id ){
					
					$new_section_id = duplicate_post_create_duplicate( get_post($section_id) );
					if( $new_section_id ){

						// On update _config_section pour définir la nouvelle page parent principale
						$config_new_section = get_post_meta( $new_section_id, $this->name_option_in_database . '_config_section', true );
						$config_new_section['parent_id'] = $new_post_id;
						update_post_meta( $new_section_id, $this->name_option_in_database . '_config_section', $config_new_section );

						$tab_new_section_duplicate[] = $new_section_id;

						$instance_new_section_duplique = new Wpextend_Post( $new_section_id );
						$tab_section_in_new_section = $instance_new_section_duplique->get_sections_pc_wpextend();

						if( is_array($tab_section_in_new_section) && count($tab_section_in_new_section) > 0 ){

							$tab_new_section_duplicate_in_new_section = array();
							foreach( $tab_section_in_new_section as $section_id_in_new_section ){

								$new_section_id_in_new_section = duplicate_post_create_duplicate( get_post($section_id_in_new_section) );
								if( $new_section_id_in_new_section ){

									// On update _config_section pour définir la nouvelle page parent principale
									$config_new_section_in_new_section = get_post_meta( $new_section_id_in_new_section, $this->name_option_in_database . '_config_section', true );
									$config_new_section_in_new_section['parent_id'] = $new_section_id;
									update_post_meta( $new_section_id_in_new_section, $this->name_option_in_database . '_config_section', $config_new_section_in_new_section );

									$tab_new_section_duplicate_in_new_section[] = $new_section_id_in_new_section;
								}
							}
							$instance_new_section_duplique->update_sections($tab_new_section_duplicate_in_new_section);
						}
					}
				}
				$instance_post_duplique->update_sections($tab_new_section_duplicate);
			}
		}
	}



	/**
	* Hook admin init to show element in section page edition
	*
	*/
	public function section_page_edition(){

    	$screen = get_current_screen();
    	if( is_object($screen) && $screen->base == 'post' && $screen->post_type == self::$name_section_register_post_type ){

    		
    	}
		
	}


}
