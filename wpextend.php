<?php
/**
* Plugin Name: WP Extend
* Plugin URI: https://github.com/PaulBalanche/WPextend
* Description: Extends basic Wordpress features such as add general settings, easy creating custom post type, ...
* Text Domain: wp-extend
* Version: 2.1.9
* Author: Paul Balanche
**/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

error_reporting(E_ALL | E_STRICT);



/**
* Define variables
*
*/
define( 'WPEXTEND_DIR'							, plugin_dir_path( __FILE__ ) );
define( 'WPEXTEND_CLASSES_DIR'					, realpath( WPEXTEND_DIR . '/inc/classes' ) . '/' );
define( 'WPEXTEND_PLUGIN_URL'					, plugins_url('', __FILE__) . '/' );
define( 'WPEXTEND_ASSETS_URL'					, WPEXTEND_PLUGIN_URL . 'assets' . '/' );
define( 'WPEXTEND_IMPORT_DIR'					, get_stylesheet_directory() . '/wpextend/import/' );
define( 'WPEXTEND_PREFIX_DATA_IN_DB'			, 'meta_wpextend_' );
define( 'WPEXTEND_TEXTDOMAIN'					, 'wp-extend' );

if( !defined('WPEXTEND_MAIN_SLUG_ADMIN_PAGE') ){
	define( 'WPEXTEND_MAIN_SLUG_ADMIN_PAGE' , 'wpextend' );
}

// Enable or disable CUSTOM POST TYPE feature
if( !defined('WPEXTEND_ENABLE_CUSTOM_POST_TYPE') ){
	define( 'WPEXTEND_ENABLE_CUSTOM_POST_TYPE' , TRUE );
}

// Enable or disable Gutenberg feature
if( !defined('WPEXTEND_ENABLE_GUTENBERG') ){
	define( 'WPEXTEND_ENABLE_GUTENBERG' , TRUE );
}

// MultiPostThumbnails plugin integration
define( 'WPEXTEND_MultiPostThumbnails' , ( class_exists('MultiPostThumbnails') ) ? TRUE : FALSE );



/**
* Initialize WPextend plugin
*
*/
add_action( 'plugins_loaded', '_wpextend_init' );
function _wpextend_init() {

	// Load text domain
	load_plugin_textdomain( WPEXTEND_TEXTDOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );
	
	// Functions
	require( WPEXTEND_DIR . '/inc/functions/basic-functions.php' );

	// WP-Extend vendor autoloader
	require( WPEXTEND_DIR . '/vendor/autoload.php' );

	// Load Main instance
	$instance_Wpextend_Main = Wpextend\Main::getInstance();
}