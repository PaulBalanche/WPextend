<?php

namespace Wpextend;

/**
 * Gutenberg support 
 * 
 */
class GutenbergBlock {

    /**
     * Properties declaration
     */
    private static $_instance,
    $path_gutenberg_theme_controllers = 'gutenberg-blocks/controllers/',
    $path_gutenberg_theme_views = 'gutenberg-blocks/views/',
    $gutenberg_name_custom_post_type = 'gutenberg_blocks';



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

        // Automatically add gutenberg blocks custom_post_type
        add_filter( 'load_custom_post_type_wpextend', array($this, 'add_gutenberg_block_to_intial_custom_post_type'), 10, 1);

        // Register ACF Gutenberg blocks
        add_action( 'acf/init', array($this, 'acf_init_register_gutenberg_blocks') );

        add_filter( 'allowed_block_types', array($this, 'my_plugin_allowed_block_types'), 10, 2 );
        add_filter( 'block_categories', array($this, 'my_plugin_block_categories'), 10, 2 );
    }
    


    /**
     * Automatically add gutenberg blocks custom_post_type
     * 
     */
    public function add_gutenberg_block_to_intial_custom_post_type($custom_post_type_wpextend){

        $custom_post_type_wpextend[ self::$gutenberg_name_custom_post_type ] = [
            'labels' => [
                'name' => 'Gutenberg block',
                'singular_name' => 'Gutenberg block',
                'add_new' => 'Add',
                'add_new_item' => 'Add new Gutenberg block',
                'new_item' => 'New',
                'edit_item' => 'Edit Gutenberg block',
                'view_item' => 'View Gutenberg block',
                'all_items' => 'All Gutenberg blocks',
                'search_items' => 'Search Gutenberg block',
                'parent_item_colon' => 'Custom Gutenberg block parent',
                'not_found' => 'None Gutenberg block',
                'not_found_in_trash' => 'None Gutenberg block deleted'
            ],
            'args' => [
                'description' => 'Gutenberg block description',
                'public' => true,
                'capability_type' => 'post',
                'hierarchical' => false,
                'show_in_menu' => true,
                'menu_position' => 'null',
                'rewrite' => false,
                'has_archive' => false,
                'show_in_rest' => true,
                'supports' => [
                    'title',
                    'thumbnail',
                    'excerpt'

                ]
            ],
            'taxonomy' => [
                'label' => 'Categories',
                'slug' => self::$gutenberg_name_custom_post_type . '_category'
            ]
        ];

        return $custom_post_type_wpextend;
    }



    /**
     * Register ACF Gutenberg blocks
     * 
     */
    public static function acf_init_register_gutenberg_blocks() {
        
        // check function exists
        if( function_exists('acf_register_block') ) {
            
            $gutenberg_blocks_saved = get_posts([
                'posts_per_page'   => -1,
                'post_type'        => self::$gutenberg_name_custom_post_type,
                'post_status'      => 'publish',
                'suppress_filters' => true
            ]);
            if( is_array($gutenberg_blocks_saved) && count($gutenberg_blocks_saved) > 0 ) {
                foreach($gutenberg_blocks_saved as $block) {

                    // Returns "gutenberg_block_category"
                    $tax_block = get_the_terms($block->ID, self::$gutenberg_name_custom_post_type . '_category');
                    $category = ( is_array($tax_block) && count($tax_block) > 0 ) ? $tax_block[0]->slug : 'common';

                    acf_register_block([
                        'name'				=> $block->post_name,
                        'title'				=> $block->post_title,
                        'description'		=> $block->post_excerpt,
                        'render_callback'	=> array($this, 'acf_gutenberg_block_render_callback'),
                        'category'			=> $category,
                        'icon'				=> 'admin-comments',
                        'keywords'			=> []
                    ]);
                }
            }
        }
    }



    /**
     * Render function for each Gutenberg blocks
     * 
     */
    public function acf_gutenberg_block_render_callback($block) {

        // convert name ("acf/testimonial") into path friendly slug ("testimonial")
        $slug = str_replace('acf/', '', $block['name']);

        // If controller exists
        if( file_exists( get_theme_file_path(self::$path_gutenberg_theme_controllers . $slug . '.php') ) ) {

            // Get all bloc fields
            $blocs_fields = get_fields();
            
            // Include controller part
            include( get_theme_file_path(self::$path_gutenberg_theme_controllers . $slug . '.php') );
        }
    }

    
    
    public static function my_plugin_allowed_block_types( $allowed_block_types, $post ) {

        // if ( $post->post_type !== 'post' ) {
            return $allowed_block_types;
        // }
        // return array( 'core/paragraph' );
    }
    
    

    /**
     * 
     */
    public static function my_plugin_block_categories( $categories, $post ) {
        
        // Get all Gutenberg block taxonomies
        $gutenberg_block_categories = get_terms( array(
            'taxonomy' => self::$gutenberg_name_custom_post_type . '_category',
            'hide_empty' => false,
        ) );

        if ( $post->post_type !== 'post' ) {
            return $categories;
        }
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'my-category',
                    'title' => __( 'My category', 'my-plugin' ),
                    'icon'  => 'wordpress',
                ),
            )
        );
    }



}