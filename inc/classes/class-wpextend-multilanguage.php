<?php
/**
 *
 */
class Wpextend_Multilanguage {

    private static $_instance;



	/**
    * Static method which instance Multilanguage class
    */
    public static function getInstance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new Wpextend_Multilanguage();
        }
        return self::$_instance;
    }




	/**
    * Construct
    */
    private function __construct() {

    }


    /**
     * Retrieves the Wordpress default locale
     * 
     */
    static public function get_wplang() {

		// If multisite, check options.
		if ( is_multisite() ) {
			// Don't check blog option when installing.
			if ( wp_installing() || ( false === $ms_locale = get_option( 'WPLANG' ) ) ) {
				$ms_locale = get_site_option( 'WPLANG' );
			}

			if ( $ms_locale !== false ) {
				$locale = $ms_locale;
			}
		} else {
			$db_locale = get_option( 'WPLANG' );
			if ( $db_locale !== false ) {
				$locale = $db_locale;
			}
		}

		if ( empty( $locale ) ) {
			$locale = 'en_US';
        }
        
        return apply_filters( 'wpextend_wplang', $locale );
	}



}