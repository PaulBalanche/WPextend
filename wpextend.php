<?php
	/**
	* Plugin Name: "WPextend"
	* Plugin URI: https://github.com/PaulBalanche/WPextend
	* Version: 1.4.0
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
	define( 'WPEXTEND_SECTION_CONTROLLERS_DIR'		, get_stylesheet_directory() . '/wpextend/section/controllers/' );
	define( 'WPEXTEND_SECTION_VIEWS_DIR'			, get_stylesheet_directory() . '/app/views/components/' );

	define( 'WPEXTEND_IMPORT_DIR'					, get_stylesheet_directory() . '/wpextend/import/' );

	define( 'WPEXTEND_PREFIX_FILE_CLASS'			, 'class-' );

	define( 'WPEXTEND_PREFIX_DATA_IN_DB'			, 'meta_buzzpress_' );

	if( !defined('WPEXTEND_ENABLE_SECTION') ){
		define( 'WPEXTEND_ENABLE_SECTION'			, TRUE );
	}

	if( !defined('WPEXTEND_ENABLE_CUSTOM_FIELDS') ){
		define( 'WPEXTEND_ENABLE_CUSTOM_FIELDS'		, TRUE );
	}

	if( !defined('WPEXTEND_ENABLE_CUSTOM_POST_TYPE') ){
		define( 'WPEXTEND_ENABLE_CUSTOM_POST_TYPE'	, TRUE );
	}

	if( !defined('WPEXTEND_NAME_MENU_SETTINGS_EDITOR') ){
		define( 'WPEXTEND_NAME_MENU_SETTINGS_EDITOR', 'WP Extend' );
	}

	if( !defined('WPEXTEND_MAIN_SLUG_ADMIN_PAGE') ){
		define( 'WPEXTEND_MAIN_SLUG_ADMIN_PAGE'		, 'wpextend' );
	}

	define( 'WPEXTEND_MultiPostThumbnails'			, ( class_exists('MultiPostThumbnails') ) ? TRUE : FALSE );

	/**
	* Initialize WPextend plugin
	*
	*/
	add_action( 'plugins_loaded', '_wpextend_init' );
	function _wpextend_init() {

		// Functions
		require(WPEXTEND_DIR . '/inc/functions/basic-functions.php');

		// Autoloader
		require(WPEXTEND_CLASSES_DIR . 'class-wpextend-autoload.php');
		Wpextend_Auto_Load::register();

		// Main
		$instance_Wpextend_Main = Wpextend_Main::getInstance();
	}