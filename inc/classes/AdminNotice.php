<?php

namespace Wpextend;

/**
*
*/
class AdminNotice {

    private static $_instance;
    
    static public $session_name = WPEXTEND_PREFIX_DATA_IN_DB,
        $prefix_admin_notice = 'WP Extend';



	/**
	* First instance of class AdminNotice
	*
	*/
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new AdminNotice();
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
        
        // Admin notice
		add_action( 'admin_notices', array($this, 'admin_notice') );
    }



    /**
	 * Admin notice
	 * 
	 */
	public function admin_notice() {

        // session_unset();
        
		if( isset($_SESSION[self::$session_name]) && is_array($_SESSION[self::$session_name]) && count($_SESSION[self::$session_name]) > 0 ) {
			
            foreach( $_SESSION[self::$session_name] as $key_admin_notice => $admin_notice ) {

                if( is_array($admin_notice) && isset($admin_notice['type'], $admin_notice['message']) ) {

                    switch( $admin_notice['type'] ) {

                        case 'neutral':
                            $class = 'notice';
                            break;

                        case 'info':
                            $class = 'notice notice-info';
                            break;

                        case 'success':
                            $class = 'notice notice-success';
                            break;

                        case 'warning':
                            $class = 'notice notice-warning';
                            break;

                        case 'error':
                            $class = 'notice notice-error';
                            break;
                    }

                    if( isset($admin_notice['dismissible']) && $admin_notice['dismissible'] )
                        $class.= ' is-dismissible';

                    printf( '<div class="%1$s"><p>%2$s%3$s</p></div>', esc_attr( $class ), ( ! empty($admin_notice['prefix']) ) ? '<strong>' . $admin_notice['prefix'] . '</strong> : ' : '', __( $admin_notice['message'], WPEXTEND_TEXTDOMAIN ) );

                    if( isset($admin_notice['single_display']) && $admin_notice['single_display'] )
                        unset( $_SESSION[self::$session_name][$key_admin_notice] );
                } 
            }
		}
    }


    /**
     * Add a notice to session
     * 
     */
    public static function add_notice( $key, $message, $type = 'neutral', $dismissible = true, $single_display = true, $prefix = '' ) {

        if( empty($key) || empty($message) )
            return;

        if( ! isset($_SESSION) || ! is_array($_SESSION[self::$session_name]) )
            $_SESSION[self::$session_name] = [];
        
        if( array_key_exists($key, $_SESSION[self::$session_name] ) )
            return;

        $_SESSION[self::$session_name][$key] = [
            'message'           => $message,
            'type'              => $type,
            'dismissible'       => $dismissible,
            'single_display'    => $single_display,
            'prefix'            => $prefix
        ];
    }



    /**
     * Add common missing file
     * 
     */
    public static function add_notice_json_file_missing() {
        
        AdminNotice::add_notice( '001', 'Some JSON configuration files do not exist yet. Click <a href="' . add_query_arg( array( 'action' => 'generate_autoload_json_file', '_wpnonce' => wp_create_nonce( 'generate_autoload_json_file' ) ), admin_url( 'admin-post.php' ) ) . '">here</a> to generate them.', 'warning', false, true, self::$prefix_admin_notice );
    }



}