<?php
/**
*
*/
class Wpextend_Auto_Load {


	/**
	 * Initialize autoload register
	 */
	static function register() {

		spl_autoload_register( array( __CLASS__ , 'autoload' ) );
	}



	/**
	 * Include classes files if necessary
	 */
	static function autoload($class_name) {

		$class_name = str_replace( '_' ,  '-' , strtolower( $class_name ) );

		$file_to_include =  WPEXTEND_CLASSES_DIR . WPEXTEND_PREFIX_FILE_CLASS . $class_name . '.php';
		if( file_exists($file_to_include) ) {
			require( $file_to_include );
		}
	}

}
