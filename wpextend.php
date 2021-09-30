<?php
/**
 * Plugin Name: 		WP Extend
 * Plugin URI: 			https://github.com/PaulBalanche/WPextend
 * Description: 		Extends basic Wordpress features such as add general settings, easy creating custom post type, ...
 * Version: 			3.2.6
 * Requires at least: 	5.6
 * Requires PHP:      	7.2
 * Author: 				Paul Balanche
 * Author URI:      	https://github.com/PaulBalanche/
 * Text Domain:			wp-extend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

error_reporting(E_ALL | E_STRICT);

// Start new or resume existing session
if (session_status() == PHP_SESSION_NONE)
	session_start();

/**
* Define variables
*
*/
define( 'WPEXTEND_DIR'							, plugin_dir_path( __FILE__ ) );
define( 'WPEXTEND_CLASSES_DIR'					, realpath( WPEXTEND_DIR . '/inc/classes' ) . '/' );
define( 'WPEXTEND_PLUGIN_URL'					, plugins_url('', __FILE__) . '/' );
define( 'WPEXTEND_ASSETS_URL'					, WPEXTEND_PLUGIN_URL . 'assets' . '/' );
define( 'WPEXTEND_IMPORT_DIR'					, get_stylesheet_directory() . '/wpextend/import/' );
define( 'WPEXTEND_JSON_DIR'						, get_stylesheet_directory() . '/wpextend/json/' );
define( 'WPEXTEND_PREFIX_DATA_IN_DB'			, 'wpe_' );
define( 'WPEXTEND_TEXTDOMAIN'					, 'wp-extend' );

if( ! defined('WPEXTEND_MAIN_SLUG_ADMIN_PAGE') ){
	define( 'WPEXTEND_MAIN_SLUG_ADMIN_PAGE' , 'wpextend' );
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
	require( WPEXTEND_DIR . 'inc/functions/basic-functions.php' );

	// WP-Extend vendor autoloader
	require( WPEXTEND_DIR . 'vendor/autoload.php' );

	// Load Main instance
	$instance_Wpextend_Main = Wpextend\Main::getInstance();
}