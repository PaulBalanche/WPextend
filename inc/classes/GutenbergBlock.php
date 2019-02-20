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
    $gutenberg_name_custom_post_type = 'gutenberg_blocks',
    $name_default_block_category = [ 'slug' => 'default', 'title' => 'Default' ],
    $wp_icon = [
        'dashicons-menu',
        'dashicons-admin-site',
        'dashicons-dashboard',
        'dashicons-admin-post',
        'dashicons-admin-media',
        'dashicons-admin-links',
        'dashicons-admin-page',
        'dashicons-admin-comments',
        'dashicons-admin-appearance',
        'dashicons-admin-plugins',
        'dashicons-admin-users',
        'dashicons-admin-tools',
        'dashicons-admin-settings',
        'dashicons-admin-network',
        'dashicons-admin-home',
        'dashicons-admin-generic',
        'dashicons-admin-collapse',
        'dashicons-filter',
        'dashicons-admin-customizer',
        'dashicons-admin-multisite',
        'dashicons-welcome-write-blog',
        'dashicons-welcome-add-page',
        'dashicons-welcome-view-site',
        'dashicons-welcome-widgets-menus',
        'dashicons-welcome-comments',
        'dashicons-welcome-learn-more',
        'dashicons-format-aside',
        'dashicons-format-image',
        'dashicons-format-gallery',
        'dashicons-format-video',
        'dashicons-format-status',
        'dashicons-format-quote',
        'dashicons-format-chat',
        'dashicons-format-audio',
        'dashicons-camera',
        'dashicons-images-alt',
        'dashicons-images-alt2',
        'dashicons-video-alt',
        'dashicons-video-alt2',
        'dashicons-video-alt3',
        'dashicons-media-archive',
        'dashicons-media-audio',
        'dashicons-media-code',
        'dashicons-media-default',
        'dashicons-media-document',
        'dashicons-media-interactive',
        'dashicons-media-spreadsheet',
        'dashicons-media-text',
        'dashicons-media-video',
        'dashicons-playlist-audio',
        'dashicons-playlist-video',
        'dashicons-controls-play',
        'dashicons-controls-pause',
        'dashicons-controls-forward',
        'dashicons-controls-skipforward',
        'dashicons-controls-back',
        'dashicons-controls-skipback',
        'dashicons-controls-repeat',
        'dashicons-controls-volumeon',
        'dashicons-controls-volumeoff',
        'dashicons-image-crop',
        'dashicons-image-rotate',
        'dashicons-image-rotate-left',
        'dashicons-image-rotate-right',
        'dashicons-image-flip-vertical',
        'dashicons-image-flip-horizontal',
        'dashicons-image-filter',
        'dashicons-undo',
        'dashicons-redo',
        'dashicons-editor-bold',
        'dashicons-editor-italic',
        'dashicons-editor-ul',
        'dashicons-editor-ol',
        'dashicons-editor-quote',
        'dashicons-editor-alignleft',
        'dashicons-editor-aligncenter',
        'dashicons-editor-alignright',
        'dashicons-editor-insertmore',
        'dashicons-editor-spellcheck',
        'dashicons-editor-expand',
        'dashicons-editor-contract',
        'dashicons-editor-kitchensink',
        'dashicons-editor-underline',
        'dashicons-editor-justify',
        'dashicons-editor-textcolor',
        'dashicons-editor-paste-word',
        'dashicons-editor-paste-text',
        'dashicons-editor-removeformatting',
        'dashicons-editor-video',
        'dashicons-editor-customchar',
        'dashicons-editor-outdent',
        'dashicons-editor-indent',
        'dashicons-editor-help',
        'dashicons-editor-strikethrough',
        'dashicons-editor-unlink',
        'dashicons-editor-rtl',
        'dashicons-editor-break',
        'dashicons-editor-code',
        'dashicons-editor-paragraph',
        'dashicons-editor-table',
        'dashicons-align-left',
        'dashicons-align-right',
        'dashicons-align-center',
        'dashicons-align-none',
        'dashicons-lock',
        'dashicons-unlock',
        'dashicons-calendar',
        'dashicons-calendar-alt',
        'dashicons-visibility',
        'dashicons-hidden',
        'dashicons-post-status',
        'dashicons-edit',
        'dashicons-trash',
        'dashicons-sticky',
        'dashicons-external',
        'dashicons-arrow-up',
        'dashicons-arrow-down',
        'dashicons-arrow-right',
        'dashicons-arrow-left',
        'dashicons-arrow-up-alt',
        'dashicons-arrow-down-alt',
        'dashicons-arrow-right-alt',
        'dashicons-arrow-left-alt',
        'dashicons-arrow-up-alt2',
        'dashicons-arrow-down-alt2',
        'dashicons-arrow-right-alt2',
        'dashicons-arrow-left-alt2',
        'dashicons-sort',
        'dashicons-leftright',
        'dashicons-randomize',
        'dashicons-list-view',
        'dashicons-exerpt-view',
        'dashicons-grid-view',
        'dashicons-move',
        'dashicons-share',
        'dashicons-share-alt',
        'dashicons-share-alt2',
        'dashicons-twitter',
        'dashicons-rss',
        'dashicons-email',
        'dashicons-email-alt',
        'dashicons-facebook',
        'dashicons-facebook-alt',
        'dashicons-googleplus',
        'dashicons-networking',
        'dashicons-hammer',
        'dashicons-art',
        'dashicons-migrate',
        'dashicons-performance',
        'dashicons-universal-access',
        'dashicons-universal-access-alt',
        'dashicons-tickets',
        'dashicons-nametag',
        'dashicons-clipboard',
        'dashicons-heart',
        'dashicons-megaphone',
        'dashicons-schedule',
        'dashicons-wordpress',
        'dashicons-wordpress-alt',
        'dashicons-pressthis',
        'dashicons-update',
        'dashicons-screenoptions',
        'dashicons-info',
        'dashicons-cart',
        'dashicons-feedback',
        'dashicons-cloud',
        'dashicons-translation',
        'dashicons-tag',
        'dashicons-category',
        'dashicons-archive',
        'dashicons-tagcloud',
        'dashicons-text',
        'dashicons-yes',
        'dashicons-no',
        'dashicons-no-alt',
        'dashicons-plus',
        'dashicons-plus-alt',
        'dashicons-minus',
        'dashicons-dismiss',
        'dashicons-marker',
        'dashicons-star-filled',
        'dashicons-star-half',
        'dashicons-star-empty',
        'dashicons-flag',
        'dashicons-warning',
        'dashicons-location',
        'dashicons-location-alt',
        'dashicons-vault',
        'dashicons-shield',
        'dashicons-shield-alt',
        'dashicons-sos',
        'dashicons-search',
        'dashicons-slides',
        'dashicons-analytics',
        'dashicons-chart-pie',
        'dashicons-chart-bar',
        'dashicons-chart-line',
        'dashicons-chart-area',
        'dashicons-groups',
        'dashicons-businessman',
        'dashicons-id',
        'dashicons-id-alt',
        'dashicons-products',
        'dashicons-awards',
        'dashicons-forms',
        'dashicons-testimonial',
        'dashicons-portfolio',
        'dashicons-book',
        'dashicons-book-alt',
        'dashicons-download',
        'dashicons-upload',
        'dashicons-backup',
        'dashicons-clock',
        'dashicons-lightbulb',
        'dashicons-microphone',
        'dashicons-desktop',
        'dashicons-laptop',
        'dashicons-tablet',
        'dashicons-smartphone',
        'dashicons-phone',
        'dashicons-index-card',
        'dashicons-carrot',
        'dashicons-building',
        'dashicons-store',
        'dashicons-album',
        'dashicons-palmtree',
        'dashicons-tickets-alt',
        'dashicons-money',
        'dashicons-smiley',
        'dashicons-thumbs-up',
        'dashicons-thumbs-down',
        'dashicons-layout',
        'dashicons-paperclip'
    ];



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

        // Add custom fields to Gutenberg blocks
        add_action( 'acf/init', array($this, 'acf_add_custom_fieds_to_gutenberg_block') );

        // Register ACF Gutenberg blocks
        add_action( 'acf/init', array($this, 'acf_init_register_gutenberg_blocks') );

        // Update Gutenberg blocks categories
        add_filter( 'block_categories', array($this, 'update_block_categories'), 10, 2 );

        // Admin port to import Gutenberg blocks
        add_action( 'admin_post_import_wpextend_gutenberg_blocks', array($this, 'import') );

        // Create controller file if missing when Gutenberg block post is saved
        add_action( 'save_post', array($this, 'on_saving_block'), 10, 3 );
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
     * Automatically add gutenberg blocks custom_post_type
     * 
     */
    public function add_gutenberg_block_to_intial_custom_post_type($custom_post_type_wpextend){

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
                'hierarchical'      => false,
                'show_in_menu'      => true,
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
                    'keywords'			=> []
                ]);
            }
        }
    }



    /**
     * Update Gutenberg blocks categories display on Gutenberg
     * 
     */
    public function update_block_categories( $categories, $post ) {
        
        // Reset all block categories
        $categories = [];

        // Get all Gutenberg block taxonomies
        $gutenberg_block_categories = get_terms([
            'taxonomy' => self::$gutenberg_name_custom_post_type . '_category',
            'hide_empty' => false
        ]);
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
                    'icon'          => get_field('icon_gutenberg_block', $block->ID)
                ]
            ];
        }

        return json_encode( $tab_gutenberg_blocks_to_export, JSON_UNESCAPED_UNICODE );
    }



    /**
     * Admin port to import Gutenberg blocks
     * 
     */
    public function import(){

        // Check valid nonce
		$action_nonce = ( isset($_GET['action']) ) ? $_GET['action'] : $_POST['action'];
        check_admin_referer($action_nonce);
        
        if( isset( $_POST['wpextend_gutenberg_blocks_to_import'] ) && !empty($_POST['wpextend_gutenberg_blocks_to_import']) ) {

			$tab_gutenberg_blocks_to_import = json_decode( stripslashes($_POST['wpextend_gutenberg_blocks_to_import']), true );
		}
		elseif( isset($_GET['file']) && file_exists( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' ) ){

			$data_json_file = file_get_contents( WPEXTEND_IMPORT_DIR . $_GET['file'] . '.json' );
			$tab_gutenberg_blocks_to_import = json_decode( $data_json_file, true );
		}
		else{
			exit;
		}

		// Save in Wordpress post
		if( is_array($tab_gutenberg_blocks_to_import) ){

            foreach( $tab_gutenberg_blocks_to_import as $block ) {
                $this->add_or_update_block($block);
            }

			if( !isset( $_POST['ajax'] ) ) {
				$goback = add_query_arg( 'udpate', 'true', wp_get_referer() );
				wp_safe_redirect( $goback );
			}
			exit;
		}
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
                    'meta_input'    => [
                        'icon' => $block['custom_data']['icon']
                    ]
                ]
            ));
        }

        return false;
    }



    /**
     * Create controller file if missing when Gutenberg block post is saved
     * 
     */
    public function on_saving_block($post_id, $block, $update){

        $post_type = get_post_type($post_id);
        if( $post_type == self::$gutenberg_name_custom_post_type ){

            $slug = str_replace('acf/', '', $block->post_name);
            if( !file_exists( get_theme_file_path(self::$path_gutenberg_theme_controllers . $slug . '.php') ) ) {

                $content = '<?php' . PHP_EOL . '/**' . PHP_EOL . ' * "' . $block->post_title . '" controller' . PHP_EOL . ' *' . PHP_EOL . ' */' . PHP_EOL . PHP_EOL . '// echo "<pre>"; print_r( $blocs_fields );';
                file_put_contents( get_theme_file_path(self::$path_gutenberg_theme_controllers . $slug . '.php'), $content );
            }
        }
    }



}