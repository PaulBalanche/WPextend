<?php

namespace Wpextend;

/**
 * Gutenberg support 
 * 
 */
class AcfGutenbergBlock {

    /**
     * Properties declaration
     */
    private static $_instance,
        $path_gutenberg_theme_controllers = 'gutenberg-blocks/controllers/',
        $name_default_block_category = [ 'slug' => 'default', 'title' => 'Default' ],
        $wp_icon = [ 'dashicons-menu', 'dashicons-admin-site', 'dashicons-dashboard', 'dashicons-admin-post', 'dashicons-admin-media', 'dashicons-admin-links', 'dashicons-admin-page', 'dashicons-admin-comments', 'dashicons-admin-appearance', 'dashicons-admin-plugins', 'dashicons-admin-users', 'dashicons-admin-tools', 'dashicons-admin-settings', 'dashicons-admin-network', 'dashicons-admin-home', 'dashicons-admin-generic', 'dashicons-admin-collapse', 'dashicons-filter', 'dashicons-admin-customizer', 'dashicons-admin-multisite', 'dashicons-welcome-write-blog', 'dashicons-welcome-add-page', 'dashicons-welcome-view-site', 'dashicons-welcome-widgets-menus', 'dashicons-welcome-comments', 'dashicons-welcome-learn-more', 'dashicons-format-aside', 'dashicons-format-image', 'dashicons-format-gallery', 'dashicons-format-video', 'dashicons-format-status', 'dashicons-format-quote', 'dashicons-format-chat', 'dashicons-format-audio', 'dashicons-camera', 'dashicons-images-alt', 'dashicons-images-alt2', 'dashicons-video-alt', 'dashicons-video-alt2', 'dashicons-video-alt3', 'dashicons-media-archive', 'dashicons-media-audio', 'dashicons-media-code', 'dashicons-media-default', 'dashicons-media-document', 'dashicons-media-interactive', 'dashicons-media-spreadsheet', 'dashicons-media-text', 'dashicons-media-video', 'dashicons-playlist-audio', 'dashicons-playlist-video', 'dashicons-controls-play', 'dashicons-controls-pause', 'dashicons-controls-forward', 'dashicons-controls-skipforward', 'dashicons-controls-back', 'dashicons-controls-skipback', 'dashicons-controls-repeat', 'dashicons-controls-volumeon', 'dashicons-controls-volumeoff', 'dashicons-image-crop', 'dashicons-image-rotate', 'dashicons-image-rotate-left', 'dashicons-image-rotate-right', 'dashicons-image-flip-vertical', 'dashicons-image-flip-horizontal', 'dashicons-image-filter', 'dashicons-undo', 'dashicons-redo', 'dashicons-editor-bold', 'dashicons-editor-italic', 'dashicons-editor-ul', 'dashicons-editor-ol', 'dashicons-editor-quote', 'dashicons-editor-alignleft', 'dashicons-editor-aligncenter', 'dashicons-editor-alignright', 'dashicons-editor-insertmore', 'dashicons-editor-spellcheck', 'dashicons-editor-expand', 'dashicons-editor-contract', 'dashicons-editor-kitchensink', 'dashicons-editor-underline', 'dashicons-editor-justify', 'dashicons-editor-textcolor', 'dashicons-editor-paste-word', 'dashicons-editor-paste-text', 'dashicons-editor-removeformatting', 'dashicons-editor-video', 'dashicons-editor-customchar', 'dashicons-editor-outdent', 'dashicons-editor-indent', 'dashicons-editor-help', 'dashicons-editor-strikethrough', 'dashicons-editor-unlink', 'dashicons-editor-rtl', 'dashicons-editor-break', 'dashicons-editor-code', 'dashicons-editor-paragraph', 'dashicons-editor-table', 'dashicons-align-left', 'dashicons-align-right', 'dashicons-align-center', 'dashicons-align-none', 'dashicons-lock', 'dashicons-unlock', 'dashicons-calendar', 'dashicons-calendar-alt', 'dashicons-visibility', 'dashicons-hidden', 'dashicons-post-status', 'dashicons-edit', 'dashicons-trash', 'dashicons-sticky', 'dashicons-external', 'dashicons-arrow-up', 'dashicons-arrow-down', 'dashicons-arrow-right', 'dashicons-arrow-left', 'dashicons-arrow-up-alt', 'dashicons-arrow-down-alt', 'dashicons-arrow-right-alt', 'dashicons-arrow-left-alt', 'dashicons-arrow-up-alt2', 'dashicons-arrow-down-alt2', 'dashicons-arrow-right-alt2', 'dashicons-arrow-left-alt2', 'dashicons-sort', 'dashicons-leftright', 'dashicons-randomize', 'dashicons-list-view', 'dashicons-exerpt-view', 'dashicons-grid-view', 'dashicons-move', 'dashicons-share', 'dashicons-share-alt', 'dashicons-share-alt2', 'dashicons-twitter', 'dashicons-rss', 'dashicons-email', 'dashicons-email-alt', 'dashicons-facebook', 'dashicons-facebook-alt', 'dashicons-googleplus', 'dashicons-networking', 'dashicons-hammer', 'dashicons-art', 'dashicons-migrate', 'dashicons-performance', 'dashicons-universal-access', 'dashicons-universal-access-alt', 'dashicons-tickets', 'dashicons-nametag', 'dashicons-clipboard', 'dashicons-heart', 'dashicons-megaphone', 'dashicons-schedule', 'dashicons-wordpress', 'dashicons-wordpress-alt', 'dashicons-pressthis', 'dashicons-update', 'dashicons-screenoptions', 'dashicons-info', 'dashicons-cart', 'dashicons-feedback', 'dashicons-cloud', 'dashicons-translation', 'dashicons-tag', 'dashicons-category', 'dashicons-archive', 'dashicons-tagcloud', 'dashicons-text', 'dashicons-yes', 'dashicons-no', 'dashicons-no-alt', 'dashicons-plus', 'dashicons-plus-alt', 'dashicons-minus', 'dashicons-dismiss', 'dashicons-marker', 'dashicons-star-filled', 'dashicons-star-half', 'dashicons-star-empty', 'dashicons-flag', 'dashicons-warning', 'dashicons-location', 'dashicons-location-alt', 'dashicons-vault', 'dashicons-shield', 'dashicons-shield-alt', 'dashicons-sos', 'dashicons-search', 'dashicons-slides', 'dashicons-analytics', 'dashicons-chart-pie', 'dashicons-chart-bar', 'dashicons-chart-line', 'dashicons-chart-area', 'dashicons-groups', 'dashicons-businessman', 'dashicons-id', 'dashicons-id-alt', 'dashicons-products', 'dashicons-awards', 'dashicons-forms', 'dashicons-testimonial', 'dashicons-portfolio', 'dashicons-book', 'dashicons-book-alt', 'dashicons-download', 'dashicons-upload', 'dashicons-backup', 'dashicons-clock', 'dashicons-lightbulb', 'dashicons-microphone', 'dashicons-desktop', 'dashicons-laptop', 'dashicons-tablet', 'dashicons-smartphone', 'dashicons-phone', 'dashicons-index-card', 'dashicons-carrot', 'dashicons-building', 'dashicons-store', 'dashicons-album', 'dashicons-palmtree', 'dashicons-tickets-alt', 'dashicons-money', 'dashicons-smiley', 'dashicons-thumbs-up', 'dashicons-thumbs-down', 'dashicons-layout', 'dashicons-paperclip' ];

    public static $gutenberg_name_custom_post_type = 'gutenberg_block';


    
    /**
	* First instance of class GutenbergBlock
	*
	*/
	public static function getInstance() {

        if (is_null(self::$_instance)) {
             self::$_instance = new AcfGutenbergBlock();
        }

        return self::$_instance;
   }



   /**
    * Construct
    */
    private function __construct() {

        // Configure hooks
        $this->create_hooks();
    }



    /**
	* Register some Hooks
	*
	* @return void
	*/
	public function create_hooks() {

        // Automatically add gutenberg blocks custom_post_type        
        add_filter( 'load_custom_post_type_wpextend', array($this, 'add_gutenberg_acf_block_to_intial_custom_post_type'), 10, 1);

        // Add custom fields to Gutenberg blocks
        add_action( 'acf/init', array($this, 'acf_add_custom_fieds_to_gutenberg_block') );

        // Register ACF Gutenberg blocks
        add_action( 'acf/init', array($this, 'acf_init_register_gutenberg_blocks') );

        // Update Gutenberg blocks abnd block categories
        add_filter( 'wpextend_load_all_gutenberg_blocks', array($this, 'load_all_blocks') );
        add_filter( 'block_categories_all', array($this, 'update_block_categories'), 10, 2 );

        // Create controller file if missing when Gutenberg block post is saved
        add_action( 'save_post', array($this, 'on_saving_block'), 10, 3 );
        add_action( 'trashed_post', array($this, 'on_trashed_post') );

        add_action( 'current_screen', array($this, 'load_custom_blocks') );
	}



    /**
	 * Load custom blocks from JSON file
	 * 
	 */
	public function load_custom_blocks( $current_screen ) {

        if( 'gutenberg_block' == $current_screen->post_type || strpos($current_screen->base, 'wpextend') !== FALSE ) {

            $tab_json_imported = GutenbergBlock::getInstance()->load_json();
    
            // Save in Wordpress post
            if( $tab_json_imported && is_array($tab_json_imported) && isset($tab_json_imported['custom']) && is_array($tab_json_imported['custom']) ){

                foreach( $tab_json_imported['custom'] as $block ) {
                    $this->add_or_update_block($block);
                }
            }
        }
    }



    /**
     * Abstact function to get all Gutenberg blocks saved in post
     * 
     */
    public function get_all_blocks_saved(){

        $gutenberg_blocks_saved = get_posts([
            'posts_per_page'   => -1,
            'post_type'        => self::$gutenberg_name_custom_post_type,
            'post_status'      => 'publish',
            'suppress_filters' => true
        ]);

        return ( is_array($gutenberg_blocks_saved) ) ? $gutenberg_blocks_saved : [];
    }



    /**
     * Function to get the Gutenberg block category
     * 
     */
    public function get_category_block($block_id){

        $tax_block = get_the_terms($block_id, self::$gutenberg_name_custom_post_type . '_category');
        return ( is_array($tax_block) && count($tax_block) > 0 ) ? [ 'slug' => $tax_block[0]->slug, 'title' => $tax_block[0]->name ] : self::$name_default_block_category;
    }



    /**
     * Convert name "acf/XXX" into path friendly slug ("XXX")
     * 
     */
    public function acf_convert_to_friendly_slug($acf_full_name){
        
        return str_replace( [
            'acf/',
            'core/'
        ], '', $acf_full_name);
    }
    
    

    /**
     * Automatically add ACF gutenberg blocks custom_post_type
     * 
     */
    public function add_gutenberg_acf_block_to_intial_custom_post_type($custom_post_type_wpextend){
        
        $custom_post_type_wpextend[ self::$gutenberg_name_custom_post_type ] = [
            'labels' => [
                'name'                  => 'Gutenberg block',
                'singular_name'         => 'Gutenberg block',
                'add_new'               => 'Add',
                'add_new_item'          => 'Add new Gutenberg block',
                'new_item'              => 'New',
                'edit_item'             => 'Edit Gutenberg block',
                'view_item'             => 'View Gutenberg block',
                'all_items'             => 'All Gutenberg blocks',
                'search_items'          => 'Search Gutenberg block',
                'parent_item_colon'     => 'Custom Gutenberg block parent',
                'not_found'             => 'None Gutenberg block',
                'not_found_in_trash'    => 'None Gutenberg block deleted'
            ],
            'args' => [
                'description'       => 'Gutenberg block description',
                'public'            => true,
                'capability_type'   => 'post',
                // 'capabilities'      => [
                //     'edit_post'                 => "edit_{$capability_type}",
                //     'read_post'                 => "read_{$capability_type}",
                //     'delete_post'               => "delete_{$capability_type}",
                //     'edit_posts'                => "edit_{$capability_type}s",
                //     'edit_others_posts'         => "edit_others_{$capability_type}s",
                //     'publish_posts'             => "publish_{$capability_type}s",
                //     'read_private_posts'        => "read_private_{$capability_type}s",
                //     'read'                      => "read",
                //     'delete_posts'              => "delete_{$capability_type}s",
                //     'delete_private_posts'      => "delete_private_{$capability_type}s",
                //     'delete_published_posts'    => "delete_published_{$capability_type}s",
                //     'delete_others_posts'       => "delete_others_{$capability_type}s",
                //     'edit_private_posts'        => "edit_private_{$capability_type}s",
                //     'edit_published_posts'      => "edit_published_{$capability_type}s",
                //     'create_posts'              => "edit_{$capability_type}s"
                // ],
                'hierarchical'      => false,
                'show_in_menu'      => false,
                'menu_position'     => 'null',
                'rewrite'           => false,
                'has_archive'       => false,
                'show_in_rest'      => true,
                'supports'          => [ 'title', 'thumbnail', 'excerpt' ]
            ],
            'taxonomy' => [
                'label'         => 'Categories',
                'slug'          => self::$gutenberg_name_custom_post_type . '_category',
                'hierarchical'  => false
            ]
        ];

        return $custom_post_type_wpextend;
    }



    /**
     * Add custom fields to Gutenberg blocks
     * 
     */
    public function acf_add_custom_fieds_to_gutenberg_block(){

        if( function_exists('acf_add_local_field_group') ) {

            $choices_radio_icon = [ 'null' => '' ];
            foreach( self::$wp_icon as $icon ) {
                $icon_formatted = str_replace('dashicons-', '', $icon);
                $choices_radio_icon[$icon_formatted] = $icon_formatted;
            }

            acf_add_local_field_group([
                'key' => 'gutenberg_block_settings',
                'title' => 'Block settings',
                'fields' => [
                    [
                        'key' => 'icon_gutenberg_block',
                        'label' => 'Icon',
                        'name' => 'icon',
                        'type' => 'select',
                        'choices' => $choices_radio_icon
                    ]
                ],
                'location' => [
                    [
                        [
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => self::$gutenberg_name_custom_post_type,
                        ]
                    ]
                ]
            ]);
        }
    }



    /**
     * Register ACF Gutenberg blocks
     * 
     */
    public function acf_init_register_gutenberg_blocks() {
        
        if( function_exists('acf_register_block') ) {
            
            foreach($this->get_all_blocks_saved() as $block) {

                acf_register_block([
                    'name'				=> $block->post_name,
                    'title'				=> $block->post_title,
                    'description'		=> $block->post_excerpt,
                    'render_callback'	=> array($this, 'acf_gutenberg_block_render_callback'),
                    'category'			=> $this->get_category_block($block->ID)['slug'],
                    'icon'				=> get_field('icon_gutenberg_block', $block->ID),
                    'keywords'			=> [],
                    'mode'	            => 'auto'
                ]);
            }
        }
    }



    /**
     * Filter all blocks in order
     * 
     */
    public function load_all_blocks( $all_blocks ) {

        foreach( $this->get_all_blocks_saved() as $block_saved ){
            if( ! isset($all_blocks['acf']) )
                $all_blocks['acf'] = [];
            
            if( ! in_array($block_saved->post_name, $all_blocks['acf']) )
                $all_blocks['acf'][] = $block_saved->post_name;
        }

        return $all_blocks;
    }



    /**
     * Update Gutenberg blocks categories display on Gutenberg
     * 
     */
    public function update_block_categories( $categories, $post ) {

        // Reset all block categories
        // $categories = [
        //     // [
        //     //     'slug' => 'common',
        //     //     'title' => 'Common Blocks'
        //     // ],
        //     // [
        //     //     'slug' => 'formatting',
        //     //     'title' => 'Formatting'
        //     // ]
        // ];

        // Get all Gutenberg block taxonomies
        $gutenberg_block_categories = get_terms( [
            'taxonomy' => self::$gutenberg_name_custom_post_type . '_category',
            'hide_empty' => false
        ] );
        if( is_array($gutenberg_block_categories) && count($gutenberg_block_categories) > 0 ) {
            foreach($gutenberg_block_categories as $tax) {
                $categories[] = [
                    'slug' => $tax->slug,
                    'title' => $tax->name
                ];
            }
        }

        // Add Default categorie
        $categories[] = self::$name_default_block_category;

        // Return new categories
        return $categories;
    }

    

    /**
     * Render function for each Gutenberg blocks
     * 
     */
    public function acf_gutenberg_block_render_callback( $block, $content = '', $is_preview = false ) {

        // Create friendly slug based on block name
        $label_block_name = ( isset($block['name']) ) ? 'name' : 'blockName';
        $friendly_slug = $this->acf_convert_to_friendly_slug($block[$label_block_name]);

        // If controller exists
        if( file_exists( get_theme_file_path(self::$path_gutenberg_theme_controllers . $friendly_slug . '/' . $friendly_slug . '.php') ) ) {

            // Initialize context empty array
            $context_gutenberg_block = [];

            // Store block values.
            $context_gutenberg_block['block'] = $block;

            // Store field values.
            $context_gutenberg_block['fields'] = ( function_exists('get_fields') ) ? get_fields() : [];

            // Preview mode
            if( is_admin() || $is_preview ){

                $post_gutenberg_block = get_posts([
                    'name'           => $friendly_slug,
                    'post_type'      => self::$gutenberg_name_custom_post_type,
                    'post_status'    => 'publish',
                    'posts_per_page' => 1
                ]);
                if( $post_gutenberg_block && is_array($post_gutenberg_block) && count($post_gutenberg_block) == 1 && has_post_thumbnail($post_gutenberg_block[0]->ID) ) {
                    echo get_the_post_thumbnail($post_gutenberg_block[0]->ID, 'medium_large');
                }
                elseif( file_exists( get_theme_file_path(self::$path_gutenberg_theme_controllers . $friendly_slug . '/screenshot.png') ) ) {
                    echo '<img width="100%" src="' . get_stylesheet_directory_uri() . '/' . self::$path_gutenberg_theme_controllers . $friendly_slug . '/screenshot.png" />';
                }
                else {
                    echo $friendly_slug;
                }
            }
            else{
                // Include controller part
                include( get_theme_file_path(self::$path_gutenberg_theme_controllers . $friendly_slug . '/' . $friendly_slug . '.php') );
            }
        }
    }



    /**
     * Function to export Guntenberg blocks to JSON format
     * 
     */
    public function export_blocks_saved(){

        $tab_gutenberg_blocks_to_export = [];

        foreach($this->get_all_blocks_saved() as $block) {

            $tab_gutenberg_blocks_to_export[] = [
                'post_title'    => $block->post_title,
                'post_name'     => $block->post_name,
                'post_excerpt'  => $block->post_excerpt,
                'custom_data'   => [
                    'taxonomy'      => $this->get_category_block($block->ID)['title'],
                    'acf_data'      => ( function_exists('get_fields') ) ? get_fields($block->ID) : []
                ]
            ];
        }

        // Save data into JSON file
        return GutenbergBlock::getInstance()->save_json( 'custom', $tab_gutenberg_blocks_to_export );
    }



    /**
     * Abstract function to insert / update Gutenberg block
     * 
     */
    public function add_or_update_block($block){

        if( is_array($block) ) {

            $the_block = get_posts([
                'name'           => $block['post_name'],
                'post_type'      => self::$gutenberg_name_custom_post_type,
                'post_status'    => 'any',
                'posts_per_page' => 1
            ]);
            $current_block_id = ( is_array($the_block) && count($the_block) == 1 ) ? $the_block[0]->ID : 0;

            return wp_insert_post(array_merge(
                $block,
                [
                    'ID'            => $current_block_id,
                    'post_type'     => self::$gutenberg_name_custom_post_type,
                    'post_status'   => 'publish',
                    'tax_input'     => [
                        self::$gutenberg_name_custom_post_type . '_category' => $block['custom_data']['taxonomy']
                    ],
                    'meta_input'    => ( isset($block['custom_data']) && is_array($block['custom_data']) && isset($block['custom_data']['acf_data']) && is_array($block['custom_data']['acf_data']) ) ? $block['custom_data']['acf_data'] : null
                ]
            ));
        }

        return false;
    }



    /**
     * Create controller file if missing when Gutenberg block post is saved
     * 
     */
    public function on_saving_block( $post_id, $block, $update ){

        $post_type = get_post_type($post_id);

        if( $post_type == self::$gutenberg_name_custom_post_type && get_post_status($post_id) == 'publish' && get_current_screen()->base == 'post' ){

            // Create friendly slug based on block name
            $friendly_slug = $this->acf_convert_to_friendly_slug($block->post_name);

            // Verify if directory controller exists
            if( !is_dir( get_theme_file_path(self::$path_gutenberg_theme_controllers . $friendly_slug . '/') ) ) {
                mkdir( get_theme_file_path(self::$path_gutenberg_theme_controllers . $friendly_slug . '/'), 0777, true );
            }

            // Verify if file controller exists
            if( !file_exists( get_theme_file_path(self::$path_gutenberg_theme_controllers . $friendly_slug . '/' . $friendly_slug . '.php') ) ) {

                $content = '<?php' . PHP_EOL . '/**' . PHP_EOL . ' * "' . $block->post_title . '" controller' . PHP_EOL . ' *' . PHP_EOL . ' * Get block values : $context_gutenberg_block["block"]' . PHP_EOL . ' * Get field values : $context_gutenberg_block["fields"]' . PHP_EOL . ' *' . PHP_EOL . ' */' . PHP_EOL . PHP_EOL . '// Timber context global scope & initialize return HTML variable' . PHP_EOL . 'global $context;' . PHP_EOL . '$return_html_by_reference = null;' . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . '/**' . PHP_EOL . ' * It\'s your turn to write something :' . PHP_EOL . ' *' . PHP_EOL . ' */' . PHP_EOL . '$controller_temp_data = $context;' . PHP_EOL  . PHP_EOL . PHP_EOL . PHP_EOL . '/**' . PHP_EOL . ' * ----------------------------------------' . PHP_EOL . ' * That\'s all, stop editing! Happy blogging.' . PHP_EOL . ' * One last thing to do: Timber render function' . PHP_EOL . ' *' . PHP_EOL . ' */' . PHP_EOL . '$return_html_by_reference .= Wpextend\Timber::render_view("' . $friendly_slug . '", $controller_temp_data);';
                file_put_contents( get_theme_file_path(self::$path_gutenberg_theme_controllers . $friendly_slug . '/' . $friendly_slug . '.php'), $content, FILE_APPEND );
            }

            $this->export_blocks_saved();
        }
    }
    
    
    
    /**
     * Save JSON file config while delete_post
     * 
     */
    public function on_trashed_post( $post_id ){

        $post_type = get_post_type($post_id);
        if( $post_type == self::$gutenberg_name_custom_post_type ){

            $this->export_blocks_saved();
        }
    }



}