<?php

namespace Wpextend;

use \Wpextend\Package\AdminNotice;
use \Wpextend\Package\RenderAdminHtml;
use \Wpextend\Package\TypeField;

/**
*
*/
class Options {

    private static $_instance;
    
    static public $admin_url = '',
        $wpe_settings_post_name = WPEXTEND_PREFIX_DATA_IN_DB . 'options',
        $json_file_name = 'options.json',
        $default_site_settings_name = 'WP Extend';

    private $options = [
        'site_settings_name' => '',
        'enable_custom_post_type' => false,
        'enable_gutenberg' => false,
        'enable_gutenberg_acf' => false,
        'enable_thumbnail_api' => false
    ];



	/**
	* First instance of class GutenbergBlock
	*
	*/
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new Options();
        }

        return self::$_instance;
   }



   /**
	* The constructor.
	*
	* @return void
	*/
	private function __construct() {

        $this->init_settings();

		// Configure hooks
        $this->create_hooks();
    }
    


    /**
     * Load options
     * 
     */
    public function init_settings() {
        
        // Getting from JSON file
		if( file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {
            
            $options_loaded = json_decode(file_get_contents(WPEXTEND_JSON_DIR . self::$json_file_name), true);
			if( is_array( $options_loaded ) )
                $this->options = $options_loaded;
		}
		else    
            Main::add_notice_json_file_missing();
    }



    /**
     * Getter
     * 
     */
    public function get_option( $name_option ) {

        return ( isset($this->options[$name_option]) ) ? $this->options[$name_option] : null;
    }



    /**
     * Setter
     * 
     */
    public function set_option( $name_option, $val ) {

        $this->options[$name_option] = $val;
    }



    /**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

        add_action( 'admin_post_wpextend_settings_update', array($this, 'update') );

        // Add sub-menu page into WPExtend menu
        add_action( 'wpextend_define_admin_menu', array($this, 'define_admin_menu') );
        
        add_action( 'wpextend_generate_autoload_json_file', array($this, 'generate_autoload_json_file') );
    }
    


    /**
     * Add sub-menu page into WPExtend menu
     * 
     */
    public function define_admin_menu() {

        add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - General', 'General', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . self::$admin_url, array( $this, 'render_admin_page' ) );
    }



   /**
	* Render HTML admin page
	*
	* @return string
	*/
	public function render_admin_page() {

		// Header page & open form
		$retour_html = RenderAdminHtml::header('Options');

        $retour_html .= '<div class="mt-1 white">';
        $retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'wpextend_settings_update' );
        
        $retour_html .= RenderAdminHtml::table_edit_open();
        $retour_html .= TypeField::render_input_text( 'Site settings name', self::$wpe_settings_post_name . '[site_settings_name]', $this->get_site_settings_name('editor'), self::$default_site_settings_name );
        $retour_html .= TypeField::render_input_radio( 'Custom post type' , self::$wpe_settings_post_name . '[enable_custom_post_type]', [ '0' => 'Off', '1' => 'On' ], $this->get_option('enable_custom_post_type') );
        $retour_html .= TypeField::render_input_radio( 'Gutenberg support' , self::$wpe_settings_post_name . '[enable_gutenberg]', [ '0' => 'Off', '1' => 'On' ], $this->get_option('enable_gutenberg') );
        $retour_html .= TypeField::render_input_radio( 'Gutenberg ACF support' , self::$wpe_settings_post_name . '[enable_gutenberg_acf]', [ '0' => 'Off', '1' => 'On' ], $this->get_option('enable_gutenberg_acf') );
        $retour_html .= TypeField::render_input_radio( 'Thumbnail API' , self::$wpe_settings_post_name . '[enable_thumbnail_api]', [ '0' => 'Off', '1' => 'On' ], $this->get_option('enable_thumbnail_api') );
        $retour_html .= RenderAdminHtml::table_edit_close();

        $retour_html .= RenderAdminHtml::form_close( 'Save', true );
        $retour_html .= '</div>';

		// return
		echo $retour_html;
    }
    


    /**
     * Save post settings
     * 
     */
    public function update() {

        check_admin_referer($_POST['action']);

        if( isset($_POST[self::$wpe_settings_post_name]) && is_array($_POST[self::$wpe_settings_post_name]) ) {
            foreach( $_POST[self::$wpe_settings_post_name] as $key => $val ) {
                
                if( $val == 'On' )
                    $this->set_option($key, true);
                elseif( $val == 'Off' )
                    $this->set_option($key, false);
                else
                    $this->set_option($key, $val);
            }
        }

        $this->save();

        wp_safe_redirect( wp_get_referer() );
        exit;
    }



    public function save() {

        if( file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {

            if( file_put_contents( WPEXTEND_JSON_DIR . self::$json_file_name, json_encode($this->options, JSON_PRETTY_PRINT) ) )
                AdminNotice::add_notice( '008', 'The changes have been saved.', 'success', true, true, AdminNotice::$prefix_admin_notice );
        }
    }




    public function get_site_settings_name( $view = 'public' ) {
        
        if( ! empty($this->get_option('site_settings_name')) )
            return $this->get_option('site_settings_name');

        return ( $view == 'editor' ) ? '' : self::$default_site_settings_name;
    }

    


    /**
	 * Create JSON file if doesn't exist
	 * 
	 */
	public function generate_autoload_json_file() {

		if( ! file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {
			
			if( touch(WPEXTEND_JSON_DIR . self::$json_file_name) )
				AdminNotice::add_notice( '019', self::$json_file_name .' file successfully created.', 'success', true, true, AdminNotice::$prefix_admin_notice );
			else
				AdminNotice::add_notice( '020', 'unable to create ' . self::$json_file_name, 'error', true, true, AdminNotice::$prefix_admin_notice );
		}
    }



}

