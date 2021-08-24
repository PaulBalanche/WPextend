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
    $theme_view_root_location = 'gutenberg-blocks/views/';



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

        // Load Timber
        self::$timber = new \Timber\Timber();
        \Timber\Timber::$locations = [
            get_theme_file_path( self::get_theme_view_location() ),
            GutenbergBlock::get_gutenberg_plugin_path() . '/views'
        ];
    }



    public static function get_theme_view_location() {

        self::$theme_view_root_location = ( defined('THEME_VIEW_ROOT_LOCATION') && THEME_VIEW_ROOT_LOCATION ) ? THEME_VIEW_ROOT_LOCATION : self::$theme_view_root_location;
        return self::$theme_view_root_location;
    }


    public static function get_view_filename_extension() {

        return '.twig';
    }



    /**
     * Render Timber view
     * 
     */
    public static function render_view($twig_view, $data) {

        if( class_exists("\Timber\Timber", false) ) {

            $path_view = ( strpos($twig_view, '.twig') !== false ) ? $twig_view : $twig_view . '/' . $twig_view . '.twig';
            
            if ( defined('TIMBER_CONTROLLERS_STRING_OUTPUT') && TIMBER_CONTROLLERS_STRING_OUTPUT ) {
               return \Timber\Timber::compile( $path_view, $data );
            }
            else {
                \Timber\Timber::render( $path_view, $data );
            }
        }
    }


}