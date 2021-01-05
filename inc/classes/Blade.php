<?php

namespace Wpextend;

/**
 * Blade support 
 * 
 */
class Blade {

    /**
     * Properties declaration
     */
    private static $_instance,
    $blade,
    $blade_theme_view_location = 'views',
    $blade_theme_cache_location = 'cache';
    
    /**
	 * First instance of class GutenbergBlock
	 *
	 */
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new Blade();
        }

        return self::$_instance;
   }



    /**
     * Construct
     */
    private function __construct() {

        // Try to get ENV timber_theme_location & timber_theme_location_sections
        self::$blade_theme_view_location = ( defined('BLADE_THEME_VIEW_LOCATION') && BLADE_THEME_VIEW_LOCATION ) ? BLADE_THEME_VIEW_LOCATION : self::$blade_theme_view_location;
        self::$blade_theme_cache_location = ( defined('BLADE_THEME_CACHE_LOCATION') && BLADE_THEME_CACHE_LOCATION ) ? BLADE_THEME_CACHE_LOCATION : self::$blade_theme_cache_location;

        // Load Blade
        self::$blade = new \Jenssegers\Blade\Blade(
            [
                get_theme_file_path( self::$blade_theme_view_location ),
                get_theme_file_path( 'wpextend/views' )
            ],
            get_theme_file_path( self::$blade_theme_cache_location )
        );
    }



    /**
     * Render Blade view
     * 
     */
    public static function render_view($blade_path, $data) {

        if( class_exists("\Jenssegers\Blade\Blade", false) ) {

            $blade_file = explode('/', $blade_path);
            $blade_file = $blade_file[count($blade_file)-1];
            
            return self::$blade->render($blade_path . '/' . $blade_file, $data);
        }
    }


}