<?php

namespace Wpextend;

/**
 * Timber support 
 * 
 */
class Timber {

    /**
     * Properties declaration
     */
    private static $_instance,
    $timber_theme_location = 'gutenberg-blocks/views/';



    /**
	* First instance of class GutenbergBlock
	 *
	 */
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new Timber();
        }

        return self::$_instance;
   }



    /**
     * Construct
     */
    private function __construct() {

        // Try to get ENV timber_theme_location
        self::$timber_theme_location = ( defined('TIMBER_THEME_LOCATION') && TIMBER_THEME_LOCATION ) ? TIMBER_THEME_LOCATION : self::$timber_theme_location;

        // Timber init template locations
        add_action( 'init', array($this, 'timber_init_template_locations') );
    }



    /**
     * Timber init template locations
     * 
     */
    public function timber_init_template_locations(){

        if( class_exists("\Timber", false) ) {
            \Timber::$locations = get_theme_file_path(self::$timber_theme_location);
        }
    }
}