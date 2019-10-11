<?php

namespace Wpextend;

/**
*
*/
class Settings {

    private static $_instance;
    
    static public $admin_url = '_settings';



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

		// Configure hooks
		$this->create_hooks();
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
        $retour_html .= TypeField::render_input_text( 'Site settings name', 'wpextend_settings[site_settings_name]' );
        $retour_html .= TypeField::render_input_checkbox( 'Enable custom post type' , 'wpextend_settings[enable_custom_post_type]', [ '1' => 'Yes' ], [ WPEXTEND_ENABLE_CUSTOM_POST_TYPE ] );
        $retour_html .= TypeField::render_input_checkbox( 'Enable Gutenberg support' , 'wpextend_settings[enable_gutenberg_support]', [ '1' => 'Yes' ], [ WPEXTEND_ENABLE_GUTENBERG ] );
        $retour_html .= TypeField::render_input_checkbox( 'Enable thumbnail API' , 'wpextend_settings[enable_custom_post_type]', [ '1' => 'Yes' ], [ WPEXTEND_PATH_THUMBNAIL_API ] );
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

        pre($_POST);

        // $goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
        // wp_safe_redirect( $goback );
        exit;
    }


    
}

