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

        add_rewrite_rule( self::$path_base_thumbnail_api . '/(.+)$', str_replace(home_url() . '/', '', admin_url()) . 'admin-post.php?action=' . WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_thumbnail_api&name_image=$1', 'top');
    }



    /**
     * Attach admin post action to catch thumbnail_url rewritted
     * 
     */
    public function catch_thumbnail_url_rewritted() {

        if( isset($_GET['name_image']) && !empty($_GET['name_image']) && preg_match('/^(\d+)-(.*)$/', $_GET['name_image'], $matches) && is_array($matches) && count($matches) > 1 && is_numeric($matches[1]) ) {
            
            $size_image = 'large';

            $consider_width = ( isset($_GET['w']) && is_numeric($_GET['w']) );
            $consider_height = ( isset($_GET['h']) && is_numeric($_GET['h']) );
            
            if( $consider_width || $consider_height ) {
                
                $attachment_metadata = wp_get_attachment_metadata($matches[1]);
                $sizes_available = $attachment_metadata['sizes'];
                if( !isset($sizes_available['large']) ) {
                    $sizes_available['large'] = [
                        'width'     => $attachment_metadata['width'],
                        'height'    => $attachment_metadata['height']
                    ];
                }

                $nearby_size_w = null;
                $nearby_size_h = null;
                foreach( $sizes_available as $key_size => $size ) {
                    if(
                        $key_size != 'thumbnail' &&
                        (
                            (
                                $consider_width && 
                                $size['width'] >= $_GET['w'] && (
                                    is_null($nearby_size_w) || $size['width'] < $nearby_size_w
                                )
                            ) || !$consider_width
                        )
                        &&
                        (
                            (
                                $consider_height && 
                                $size['height'] >= $_GET['h'] && (
                                    is_null($nearby_size_h) || $size['height'] < $nearby_size_h
                                )
                            ) || !$consider_height
                        )
                    ){
                        $nearby_size_w = $size['width'];
                        $nearby_size_h = $size['height'];
                        $size_image = $key_size;
                    }
                }

                // If none size was selected, choose larger image
                if( is_null($nearby_size_w) && is_null($nearby_size_h) ) {
                    foreach( $sizes_available as $key_size => $size ) {
                        if(
                            $key_size != 'thumbnail' &&
                            (
                                (
                                    is_null($nearby_size_w) && is_null($nearby_size_h)
                                )
                                ||
                                (
                                    (
                                        ( $consider_width && $nearby_size_w < $size['width'] ) || !$consider_width
                                    )
                                    &&
                                    (
                                        ( $consider_height && $nearby_size_h < $size['height'] ) || !$consider_height
                                    )
                                )
                            )
                        ){
                            $nearby_size_w = $size['width'];
                            $nearby_size_h = $size['height'];
                            $size_image = $key_size;
                        }
                    }
                }
            }

            // Render image
            $data_image = wp_get_attachment_image_src($matches[1], $size_image);
            if( $data_image && is_array($data_image) && count($data_image) > 0 ) {

                // Get image PATH
                $wp_upload_dir = wp_upload_dir();
                $path_image = str_replace($wp_upload_dir['baseurl'], $wp_upload_dir['basedir'], $data_image[0]);

                // Define some header data...
                header("Cache-Control: private, max-age=10800, pre-check=10800");
                header("Pragma: private");
                header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

                // If browser has a cached version of this image and the image did not change, return it.
                if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&  (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($path_image)) ) {
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($path_image)).' GMT', 
                    true, 304);
                    exit;
                }

                // Else generate PHP image and return
                header('Content-type: image/jpeg');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path_image)) . ' GMT');
                echo file_get_contents($data_image[0], false, stream_context_create([
                    'ssl' => [
                        'verify_peer'   => false
                    ]
                ]));
            }
        }
    }



}