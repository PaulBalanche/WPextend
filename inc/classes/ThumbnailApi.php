<?php

namespace Wpextend;

/**
 * ThumbnailApi support
 * Add env variable to enable ThumbnailApi feature
 * Example : WPEXTEND_PATH_THUMBNAIL_API='images'
 * 
 */
class ThumbnailApi {

    /**
     * Properties declaration
     */
    private static $_instance,
    $path_base_thumbnail_api = WPEXTEND_PATH_THUMBNAIL_API;



    /**
	* First instance of class GutenbergBlock
	 *
	 */
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new ThumbnailApi();
        }

        return self::$_instance;
   }




    /**
     * Construct
     */
    private function __construct() {

        // Add new rewrite rule to allow thumbnail api
        add_action( 'init', array($this, 'rewrite_thumbnail_url') );

        // Attach admin post action to catch thumbnail_url rewritted
        add_action( 'admin_post_' . WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_thumbnail_api', array($this, 'catch_thumbnail_url_rewritted') );
        add_action( 'admin_post_nopriv_' . WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_thumbnail_api', array($this, 'catch_thumbnail_url_rewritted') );
    }



    /**
     * Add new rewrite rule to allow thumbnail api
     * 
     */
    public function rewrite_thumbnail_url() {

        add_rewrite_rule( '^' . self::$path_base_thumbnail_api . '/(.+)$', str_replace(home_url() . '/', '', admin_url()) . 'admin-post.php?action=' . WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_thumbnail_api&name_image=$1', 'top');
    }



    /**
     * Attach admin post action to catch thumbnail_url rewritted
     * 
     */
    public function catch_thumbnail_url_rewritted() {

        pre($_GET);die;
    }



}