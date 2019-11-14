<?php

namespace Wpextend;

/**
 * ThumbnailApi support
 * Add env variable to enable ThumbnailApi feature
 * 
 */
class ThumbnailApi {

    /**
     * Properties declaration
     */
    private static $_instance;

    public $path_base_thumbnail_api = 'images',
        $path_generated_images = 'images-generated';


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
        // add_action( 'init', array($this, 'rewrite_thumbnail_url') );
        add_filter( 'mod_rewrite_rules', array($this, 'rewrite_thumbnail_url') );

        // Attach admin post action to catch thumbnail_url rewritted
        add_action( 'admin_post_' . WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_thumbnail_api', array($this, 'catch_thumbnail_url_rewritted') );
        add_action( 'admin_post_nopriv_' . WPEXTEND_MAIN_SLUG_ADMIN_PAGE . '_thumbnail_api', array($this, 'catch_thumbnail_url_rewritted') );
    }


    /**
     * Add new rewrite rule to allow thumbnail api
     * 
     */
    function rewrite_thumbnail_url( $rewrite ) {

        // Step 1
        $new_rules = "\n# WP Extend ThumbnailApi support step 1\n";
        $new_rules .= "<IfModule mod_rewrite.c>\n";

        $new_rules .= "RewriteEngine On\n";
        $new_rules .= "RewriteBase /\n";

        $new_rules .= "# URL contains width and height query string\n";
        $new_rules .= "RewriteCond %{REQUEST_URI} ^/" . $this->path_base_thumbnail_api . "/\n";
        $new_rules .= "RewriteCond %{QUERY_STRING} ^w=([0-9]*)&h=([0-9]*)$\n";
        $new_rules .= "RewriteRule ^" . $this->path_base_thumbnail_api . "/(.*)$ /" . $this->path_generated_images . "/w%1-h%2-$1? [QSA,L]\n";

        $new_rules .= "# URL contains width only query string\n";
        $new_rules .= "RewriteCond %{REQUEST_URI} ^/" . $this->path_base_thumbnail_api . "/\n";
        $new_rules .= "RewriteCond %{QUERY_STRING} ^w=([0-9]*)$\n";
        $new_rules .= "RewriteRule ^" . $this->path_base_thumbnail_api . "/(.*)$ /" . $this->path_generated_images . "/w%1-h0-$1? [QSA,L]\n";

        $new_rules .= "# URL contains height only query string\n";
        $new_rules .= "RewriteCond %{REQUEST_URI} ^/" . $this->path_base_thumbnail_api . "/\n";
        $new_rules .= "RewriteCond %{QUERY_STRING} ^h=([0-9]*)$\n";
        $new_rules .= "RewriteRule ^" . $this->path_base_thumbnail_api . "/(.*)$ /" . $this->path_generated_images . "/w0-h%1-$1? [QSA,L]\n";

        $new_rules .= "# URL does not contains any query string\n";
        $new_rules .= "RewriteCond %{REQUEST_URI} ^/" . $this->path_base_thumbnail_api . "/\n";
        $new_rules .= "RewriteRule ^" . $this->path_base_thumbnail_api . "/(.*)$ /" . $this->path_generated_images . "/w0-h0-$1? [QSA,L]\n";

        $new_rules .= "</IfModule>\n\n";

        // Step 2
        $new_rules .= "# WP Extend ThumbnailApi support step 2\n";
        $new_rules .= "<IfModule mod_rewrite.c>\n";

        $new_rules .= "RewriteEngine On\n";
        $new_rules .= "RewriteBase /" . $this->path_generated_images . "/\n";
  
        $new_rules .= "# redirect all requests to non-existing resources to special handler\n";
        $new_rules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
        $new_rules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
        $new_rules .= "RewriteRule ^" . $this->path_generated_images . "/(.+)$ /" . str_replace(home_url() . '/', '', admin_url()) . "admin-post.php?action=" . WPEXTEND_MAIN_SLUG_ADMIN_PAGE . "_thumbnail_api&name_image=$1 [QSA,L]\n";

        $new_rules .= "</IfModule>\n\n";

        return $new_rules . $rewrite;
    }



    /**
     * Attach admin post action to catch thumbnail_url rewritted
     * 
     */
    public function catch_thumbnail_url_rewritted() {

        if( isset($_GET['name_image']) && !empty($_GET['name_image']) && preg_match('/^w(\d+)-h(\d+)-(\d+)-(.*)$/', $_GET['name_image'], $matches) && is_array($matches) && count($matches) > 1 && is_numeric($matches[3]) ) {

            $size_image = 'large';

            $consider_width = ( isset($_GET['w']) && is_numeric($_GET['w']) );
            $consider_height = ( isset($_GET['h']) && is_numeric($_GET['h']) );
            
            if( $consider_width || $consider_height ) {
                
                $attachment_metadata = wp_get_attachment_metadata($matches[3]);
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
            $data_image = wp_get_attachment_image_src($matches[3], $size_image);
            if( $data_image && is_array($data_image) && count($data_image) > 0 ) {

                // Get image PATH
                $wp_upload_dir = wp_upload_dir();
                $path_image = str_replace($wp_upload_dir['baseurl'], $wp_upload_dir['basedir'], $data_image[0]);
                if( file_exists($path_image) ) {

                    // Copy image
                    $name_directory_image_copy = dirname(ABSPATH) . '/' . $this->path_generated_images . '/';
                    if( !file_exists($name_directory_image_copy) )
                        mkdir($name_directory_image_copy);
                    copy( $path_image, $name_directory_image_copy . $matches[0] );

                    $last_modified_time = filemtime($path_image);
                    $cache_duration = 3600 * 24 * 30; // 30 days...
                    $etag = 'W/"' . md5($last_modified_time) . '"';

                    // Define some header
                    header('Content-Type: image/jpeg');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified_time) . ' GMT');
                    header("Cache-Control: public, max-age=" . $cache_duration);
                    header("Etag: $etag");

                    // Send 304 if no update
                    if (
                        (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $last_modified_time) ||
                        (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag === trim($_SERVER['HTTP_IF_NONE_MATCH']))
                        ) {
                            header('HTTP/1.1 304 Not Modified');
                            exit();
                    }
                    else {
                        // Else generate PHP image and return
                        readfile($path_image, false, stream_context_create([
                            'ssl' => [
                                'verify_peer'   => false
                            ]
                        ]));
                    }
                }
            }
        }
    }



}