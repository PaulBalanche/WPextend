<?php

namespace Wpextend;

/**
*
*/
class AdminNotice {

    private static $_instance;
    
    static public $session_name = WPEXTEND_PREFIX_DATA_IN_DB,
        $prefix_admin_notice = '<strong>WP Extend :</strong>';



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

                    printf( '<div class="%1$s"><p>' . self::$prefix_admin_notice . ' %2$s</p></div>', esc_attr( $class ), __( $admin_notice['message'], WPEXTEND_TEXTDOMAIN ) );

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
    public static function add_notice( $key, $message, $type = 'neutral', $dismissible = true, $single_display = true ) {

        // unset( $_SESSION[self::$session_name] );

        if( empty($key) || empty($message) )
            return;

        if( ! isset($_SESSION) || ! is_array($_SESSION[self::$session_name]) )
            $_SESSION[self::$session_name] = [];
        
        if( array_key_exists($key, $_SESSION[self::$session_name] ) )
            return;

        $_SESSION[self::$session_name][$key] = [
            'message' => $message,
            'type' => $type,
            'dismissible' => $dismissible,
            'single_display' => $single_display
        ];
    }
    


}