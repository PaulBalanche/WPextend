<?php

namespace Wpextend;

use \Wpextend\Package\AdminNotice;
use \Wpextend\Package\RenderAdminHtml;
use \Wpextend\Package\TypeField;

/**
 * Gutenberg support 
 * 
 */
class GutenbergBlock {

    /**
     * Properties declaration
     */
    private static $_instance;

    public static $json_file_name = 'gutenberg_block.json',
        $admin_url = '_gutenberg_block',
        $wp_default_blocks = [
            'core' => [
                'paragraph', 'list', 'heading', 'quote', 'audio', 'image', 'cover', 'video', 'gallery', 'file', 'html', 'preformatted', 'code', 'verse', 'pullquote', 'table', 'columns', 'column', 'group', 'button', 'more', 'nextpage', 'media-text', 'spacer', 'separator', 'calendar', 'shortcode', 'archives', 'categories', 'latest-comments', 'latest-posts', 'rss', 'search', 'tag-cloud', 'embed',
            ],
            'core-embed' => [
                'twitter', 'youtube', 'facebook', 'instagram', 'wordpress', 'soundcloud', 'spotify', 'flickr', 'vimeo', 'animoto', 'cloudup', 'collegehumor', 'crowdsignal', 'polldaddy', 'dailymotion', 'hulu', 'imgur', 'issuu', 'kickstarter', 'meetup-com', 'mixcloud', 'reddit', 'reverbnation', 'screencast', 'scribd', 'slideshare', 'smugmug', 'speaker', 'speaker-deck', 'ted', 'tumblr', 'videopress', 'wordpress-tv', 'amazon-kindle'
            ],
            'woocommerce' => [
                'handpicked-products', 'all-reviews', 'featured-category', 'featured-product', 'product-best-sellers', 'product-categories', 'product-category', 'product-new', 'product-on-sale', 'products-by-attribute', 'product-top-rated', 'reviews-by-product', 'reviews-by-category', 'product-search', 'product-tag', 'all-products', 'price-filter', 'attribute-filter', 'active-filters'
            ]
        ],
        $theme_blocks_path = '/wpextend/blocks',
        $theme_patterns_path = '/wpextend/patterns';

    public $allowed_block_types = null,
        $theme_blocks = [],
        $all_blocks = [];


    
    /**
	* First instance of class GutenbergBlock
	*
	*/
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new GutenbergBlock();
        }

        return self::$_instance;
   }



   /**
    * Construct
    */
    private function __construct() {
        
        if( function_exists('acf_register_block') && Options::getInstance()->get_option('enable_gutenberg_acf') ) {
            AcfGutenbergBlock::getInstance();
        }

        $this->load_theme_blocks();
        $this->load_all_blocks();
        $this->load_allowed_block();

        // Configure hooks
        $this->create_hooks();
    }



    /**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

        // Register custom blocks & patterns
        add_action( 'init', array($this, 'register_custom_block'), 99 );
        add_action( 'admin_init', array($this, 'register_patterns') );

        // Update Gutenberg blocks abnd block categories
        add_filter( 'allowed_block_types', array($this, 'allowed_specifics_block_types'), 10, 2 );

        // Create autoload_json_file if mnissing
        add_action( 'wpextend_generate_autoload_json_file', array($this, 'generate_autoload_json_file') );

        // Add sub-menu page into WPExtend menu
        add_action( 'wpextend_define_admin_menu', array($this, 'define_admin_menu') );
        
        // Action to save allowed_block_types
        add_action( 'admin_post_update_allowed_block_types', array($this, 'update_allowed_block_types') );
	}



    /**
     * Add sub-menu page into WPExtend menu
     * 
     */
    public function define_admin_menu() {

        add_submenu_page(WPEXTEND_MAIN_SLUG_ADMIN_PAGE, 'WP Extend - Gutenberg', 'Gutenberg', 'manage_options', WPEXTEND_MAIN_SLUG_ADMIN_PAGE . self::$admin_url, array( $this, 'render_admin_page' ) );
    }



    /**
	* Render HTML admin page
	*
	* @return string
	*/
	public function render_admin_page() {

		// Header page & open form
        $retour_html = RenderAdminHtml::header('Gutenberg');

        $retour_html .= '<div id="container_allowed_gutenberg_block_types"><a href="" class="button">Check all</a> <a href="" class="button">Uncheck all</a></div>';

        $retour_html .= '<div class="mt-1 white">';
        
        $retour_html .= RenderAdminHtml::form_open( admin_url( 'admin-post.php' ), 'update_allowed_block_types', 'form_allowed_block_types' );

        $retour_html .= RenderAdminHtml::table_edit_open();

        foreach( $this->all_blocks as $namespace_block => $blocks ) {

            $blocks_temp = [];
            foreach( $blocks as $key => $block ) {
                $blocks_temp[$namespace_block . '/' . $block] = $block;
            }

            $default_value = ( ! is_null($this->allowed_block_types) ) ? $this->allowed_block_types : $blocks_temp;
            $retour_html .= TypeField::render_input_checkbox( $namespace_block, "allowed_block_types", $blocks_temp, $default_value, false, '', false, true );
        }
        $retour_html .= TypeField::render_input_hidden( 'allowed_block_types[]', 'null' );
        $retour_html .= RenderAdminHtml::table_edit_close();
        $retour_html .= RenderAdminHtml::form_close( 'Submit', true );

        $retour_html .= '</div>';

		// return
		echo $retour_html;
    }
    


    /**
     * Action to save allowed_block_types
     * 
     */
    public function update_allowed_block_types() {

        // Check valid nonce
		check_admin_referer($_POST['action']);

		if( isset($_POST['allowed_block_types']) && is_array($_POST['allowed_block_types']) ) {

            $allowed_block_types = $_POST['allowed_block_types'];
            foreach( $allowed_block_types as $key => $val ) {
                if( $val == 'null' )
                    unset($allowed_block_types[$key]);
            }

            // Save data into JSON file
            $this->save_json( 'allowed_block_types', $allowed_block_types );

			if( !isset( $_POST['ajax'] ) ) {
				
				AdminNotice::add_notice( '003', 'Category successfully added.', 'success', true, true, AdminNotice::$prefix_admin_notice );

				wp_safe_redirect( wp_get_referer() );
				exit;
			}
		}
    }



    /**
     * Load blocks defined in the current theme
     * 
     */
    public function load_theme_blocks() {

        if( file_exists( get_stylesheet_directory() . self::$theme_blocks_path ) ) {
            $blocks_dir = scandir( get_stylesheet_directory() . self::$theme_blocks_path );
            foreach( $blocks_dir as $namespace_blocks ) {
                
                if( ! is_dir( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks) || $namespace_blocks == '..' || $namespace_blocks == '.' )
                    continue;
                    
                $this->theme_blocks[$namespace_blocks] = [];

                $blocks = scandir( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks );
                foreach( $blocks as $block ) {

                    if( ! is_dir( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block ) || $block == '..' || $block == '.' )
                        continue;
                        
                    $this->theme_blocks[$namespace_blocks][] = $block;
                }

                if( count($this->theme_blocks[$namespace_blocks]) == 0 ) {
                    unset( $this->theme_blocks[$namespace_blocks] );
                }
            }
        }
    }



    /**
     * Load all blocks. Merge default WP Core blocks and them defined in the current theme
     * 
     */
    public function load_all_blocks() {

        $this->all_blocks = self::$wp_default_blocks;
        foreach( $this->theme_blocks as $block_namespace => $blocks ) {
            if( is_array($blocks) ) {
                foreach( $blocks as $block ) {
                    if( isset( $this->all_blocks[$block_namespace] ) ) {
                        if( ! in_array($block, $this->all_blocks[$block_namespace]) ) {
                            $this->all_blocks[$block_namespace][] = $block;
                        }
                    }
                    else {
                        $this->all_blocks[$block_namespace] = [ $block ];
                    }
                }
            }
        }

        $this->all_blocks = apply_filters( 'wpextend_load_all_gutenberg_blocks', $this->all_blocks);
    }



    /**
	 * Load allowed blocks
	 * 
	 */
    public function load_allowed_block() {

        $tab_json_imported = $this->load_json();

        if( $tab_json_imported && is_array($tab_json_imported) && isset($tab_json_imported['allowed_block_types']) && is_array($tab_json_imported['allowed_block_types']) ) {
            $this->allowed_block_types = $tab_json_imported['allowed_block_types'];
        }
    }
    


    /**
	 * Load JSON file
	 * 
	 */
	public function load_json() {

        if( file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {

            $data_json_file = file_get_contents( WPEXTEND_JSON_DIR . self::$json_file_name );
            return json_decode( $data_json_file, true );
        }
        else
            Main::add_notice_json_file_missing();

        return false;
    }



    /**
	 * Save data into JSON file
	 * 
	 */
	public function save_json( $key, $new_data, $options = JSON_PRETTY_PRINT ) {
        
        // Get actual content in order to replace it
        $data_json_file = file_get_contents( WPEXTEND_JSON_DIR . self::$json_file_name );
        $data_json_file_decode = json_decode( $data_json_file, true );
        if( is_array($data_json_file_decode) )
            $data_json_file_decode[$key] = $new_data;
        else
            $data_json_file_decode = [ $key => $new_data ];

        return file_put_contents( WPEXTEND_JSON_DIR . self::$json_file_name, json_encode( $data_json_file_decode, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES ) );
    }



    /**
	 * Create JSON file if doesn't exist
	 * 
	 */
	public function generate_autoload_json_file() {

		if( ! file_exists(WPEXTEND_JSON_DIR . self::$json_file_name) ) {
			if( touch(WPEXTEND_JSON_DIR . self::$json_file_name) )
				AdminNotice::add_notice( '017', self::$json_file_name .' file successfully created.', 'success', true, true, AdminNotice::$prefix_admin_notice );
			else
				AdminNotice::add_notice( '018', 'unable to create ' . self::$json_file_name, 'error', true, true, AdminNotice::$prefix_admin_notice );
		}
    }



    /**
     * Register theme Custom Blocks
     * 
     */
    public function register_custom_block() {

        foreach( $this->theme_blocks as $namespace_blocks => $blocks ) {
            if( is_array($blocks) ) {
                foreach( $blocks as $block ) {
                    
                    $args_register = [];

                    // editor_script
                    if( file_exists( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/build/index.js' ) && file_exists( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/build/index.asset.php' ) ) {
                        $asset_file = include( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/build/index.asset.php' );

                        wp_register_script(
                            $namespace_blocks . '-' . $block,
                            get_stylesheet_directory_uri() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/build/index.js',
                            $asset_file['dependencies'],
                            $asset_file['version']
                        );

                        $args_register = [
                            'editor_script' => $namespace_blocks . '-' . $block,
                        ];
                    }

                    // render_callback
                    if( file_exists( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/render.php' ) ) {

                        include( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/render.php' );
                        if( function_exists( $namespace_blocks . '_' . str_replace('-', '_', $block) . '_render_callback' ) )
                            $args_register['render_callback'] = $namespace_blocks . '_' . str_replace('-', '_', $block) . '_render_callback';
                    }

                    // editor_style
                    if( file_exists( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/assets/style/editor.min.css' ) ) {

                        wp_register_style(
                            $namespace_blocks . '-' . $block . 'editor-style',
                            get_stylesheet_directory_uri() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/assets/style/editor.min.css',
                            array( 'wp-edit-blocks' ),
                            filemtime( get_stylesheet_directory() . self::$theme_blocks_path . '/' . $namespace_blocks . '/' . $block . '/assets/style/editor.min.css' )
                        );
 
                        $args_register['editor_style'] = $namespace_blocks . '-' . $block . 'editor-style';
                    }
                    
                    $registry = \WP_Block_Type_Registry::get_instance();
                    if ( $registry->is_registered( $namespace_blocks . '/' . $block ) ) {
                        $registry->unregister( $namespace_blocks . '/' . $block );
                    }
                    register_block_type( $namespace_blocks . '/' . $block, $args_register );
                }
            }
        }
    }



    /**
     * Register theme Gutenberg patterns
     * 
     */
    public function register_patterns() {

        if( class_exists( '\WP_Block_Patterns_Registry' ) && file_exists( get_stylesheet_directory() . self::$theme_patterns_path ) ) {
            
            $patterns_dir = scandir( get_stylesheet_directory() . self::$theme_patterns_path );
            foreach( $patterns_dir as $namespace_patterns ) {

                if( ! is_dir( get_stylesheet_directory() . self::$theme_patterns_path . '/' . $namespace_patterns ) || $namespace_patterns == '..' || $namespace_patterns == '.' )
                    continue;

                $patterns = scandir( get_stylesheet_directory() . self::$theme_patterns_path . '/' . $namespace_patterns );
                foreach( $patterns as $pattern ) {

                    if( is_dir( get_stylesheet_directory() . self::$theme_patterns_path . '/' . $namespace_patterns . '/' . $pattern ) || $pattern == '..' || $pattern == '.' )
                        continue;
                        
                    // Get info file
                    $file_pathinfo = pathinfo( get_stylesheet_directory() . self::$theme_patterns_path . '/' . $namespace_patterns . '/' . $pattern );
                    if( $file_pathinfo['extension'] == 'json'  && ! \WP_Block_Patterns_Registry::get_instance()->is_registered( $file_pathinfo['filename'] ) ) {

                        register_block_pattern(
                            $namespace_patterns . '/' . $file_pathinfo['filename'],
                            json_decode( file_get_contents( get_stylesheet_directory() . self::$theme_patterns_path . '/' . $namespace_patterns . '/' . $pattern ), true )
                        );
                    }
                }
            }
        }
    }



    /**
     * Allow some Gutenberg blocks
     * 
     */
    public function allowed_specifics_block_types( $allowed_block_types, $post ) {

        if( ! is_null($this->allowed_block_types) ) {
            return $this->allowed_block_types;
        }

        return $allowed_block_types;
    }



}