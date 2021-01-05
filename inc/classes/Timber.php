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
    $timber,
    $timber_theme_location = 'gutenberg-blocks/views/',
    $timber_controllers_string_output;



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
        self::$timber_controllers_string_output = ( defined('TIMBER_CONTROLLERS_STRING_OUTPUT') && TIMBER_CONTROLLERS_STRING_OUTPUT ) ? TIMBER_CONTROLLERS_STRING_OUTPUT : false;

        // Load Timber
        self::$timber = new \Timber\Timber();
        \Timber\Timber::$locations = [
            get_theme_file_path( self::$timber_theme_location ),
            get_theme_file_path( 'wpextend/views' )
        ];
    }



    /**
     * Render Timber view
     * 
     */
    public static function render_view($twig_view, $data) {

        if( class_exists("\Timber\Timber", false) ) {

            $path_view = ( strpos($twig_view, '.twig') !== false ) ? $twig_view : $twig_view . '/' . $twig_view . '.twig';
            
            if ( self::$timber_controllers_string_output ) {
               return \Timber\Timber::compile( $path_view, $data );
            }
            else {
                \Timber\Timber::render( $path_view, $data );
            }
        }
    }


}