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
    $timber_theme_location = 'gutenberg-blocks/views/',
    $timber_theme_location_sections = 'sections/',
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

        // Try to get ENV timber_theme_location & timber_theme_location_sections
        self::$timber_theme_location = ( defined('TIMBER_THEME_LOCATION') && TIMBER_THEME_LOCATION ) ? TIMBER_THEME_LOCATION : self::$timber_theme_location;
        self::$timber_theme_location_sections = ( defined('TIMBER_THEME_LOCATION_SECTIONS') && TIMBER_THEME_LOCATION_SECTIONS ) ? TIMBER_THEME_LOCATION_SECTIONS : self::$timber_theme_location_sections;
        self::$timber_controllers_string_output = ( defined('TIMBER_CONTROLLERS_STRING_OUTPUT') && TIMBER_CONTROLLERS_STRING_OUTPUT ) ? TIMBER_CONTROLLERS_STRING_OUTPUT : false;

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



    /**
     * Render Timber view
     * 
     */
    public static function render_view($twig_view, $data) {

        if( class_exists("\Timber", false) ) {

            $path_view = ( strpos($twig_view, '.twig') !== false ) ? $twig_view : self::$timber_theme_location_sections . $twig_view . '/' . $twig_view . '.twig';
            
            if ( self::$timber_controllers_string_output ) {
               return \Timber::compile( $path_view, $data );
            }
            else {
               \Timber::render( $path_view, $data );
            }
         }
    }


}