<?php

namespace Wpextend;

/**
*
*/
class Settings {

    private static $_instance;
    
    static public $admin_url = '_settings',
        $wpe_settings_post_name = WPEXTEND_PREFIX_DATA_IN_DB . 'settings',
        $default_site_settings_name = 'WP Extend';

    public $enable_custom_post_type,
        $enable_custom_post_type_env,
        $enable_gutenberg,
        $enable_gutenberg_env,
        $enable_thumbnail_api;



	/**
	* First instance of class GutenbergBlock
	*
	*/
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new Settings();
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
    


    public function init_settings() {
        
        $this->enable_custom_post_type_env = ( defined('WPEXTEND_ENABLE_CUSTOM_POST_TYPE') ) ? true : false;
        $this->enable_custom_post_type = ( $this->enable_custom_post_type_env && WPEXTEND_ENABLE_CUSTOM_POST_TYPE ) ? true : ( get_option(self::$wpe_settings_post_name . '_' . 'enable_custom_post_type') == '1' ) ? true : false;
        
        $this->enable_gutenberg_env = ( defined('WPEXTEND_ENABLE_GUTENBERG') ) ? true : false;
        $this->enable_gutenberg = ( $this->enable_gutenberg_env && WPEXTEND_ENABLE_GUTENBERG ) ? true : ( get_option(self::$wpe_settings_post_name . '_' . 'enable_gutenberg_support') == '1' ) ? true : false;
        
        $this->enable_thumbnail_api = ( get_option(self::$wpe_settings_post_name . '_' . 'enable_thumbnail_api') == '1' ) ? true : false;
    }



    /**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

		add_action( 'admin_post_wpextend_settings_update', array($this, 'update') );
	}



   /**
	* Render HTML admin page
	*
	* @return string
	*/
	public function render_admin_page() {

		// Header page & open form
		$retour_html = RenderAdminHtml::header('Settings');

        $retour_html .= '<div class="mt-1 white">';
        $retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'wpextend_settings_update' );
        
        $retour_html .= RenderAdminHtml::table_edit_open();
        $retour_html .= TypeField::render_input_text( 'Site settings name', self::$wpe_settings_post_name . '[site_settings_name]', $this->get_site_settings_name('editor'), self::$default_site_settings_name );
        $retour_html .= TypeField::render_input_radio( 'Custom post type' , self::$wpe_settings_post_name . '[enable_custom_post_type]', [ '0' => 'Off', '1' => 'On' ], $this->enable_custom_post_type, false, '', $this->enable_custom_post_type_env );
        $retour_html .= TypeField::render_input_radio( 'Gutenberg support' , self::$wpe_settings_post_name . '[enable_gutenberg_support]', [ '0' => 'Off', '1' => 'On' ], $this->enable_gutenberg, false, '', $this->enable_gutenberg_env );
        $retour_html .= TypeField::render_input_radio( 'Thumbnail API' , self::$wpe_settings_post_name . '[enable_thumbnail_api]', [ '0' => 'Off', '1' => 'On' ], $this->enable_thumbnail_api );
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

        if( isset($_POST[self::$wpe_settings_post_name]) ) {
            foreach( $_POST[self::$wpe_settings_post_name] as $key => $val ) {
                
                if( $val == 'On' ) {
                    update_option(self::$wpe_settings_post_name . '_' . $key, '1');
                }
                elseif( $val == 'Off' ) {
                    update_option(self::$wpe_settings_post_name . '_' . $key, '0');
                }
                else{
                    update_option(self::$wpe_settings_post_name . '_' . $key, $val);
                }
            }
        }

        AdminNotice::add_notice( '008', 'The changes have been saved.', 'success' );

        wp_safe_redirect( wp_get_referer() );
        exit;
    }




    public function get_site_settings_name( $view = 'public' ) {
        
        $site_settings_name = get_option(self::$wpe_settings_post_name . '_' . 'site_settings_name');
        if( $site_settings_name && ! empty($site_settings_name) )
            return $site_settings_name;

        return ( $view == 'editor' ) ? '' : self::$default_site_settings_name;
    }


    
}

