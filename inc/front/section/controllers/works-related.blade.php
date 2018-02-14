<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title' => $data_section->post_title,
		'works' => null,
		'bkg_class' => $background_class_section
	);

	// related work
	$all_work = get_posts( array(
		'posts_per_page'  	=> 3,
		'offset'          	=> 0,
		'category'        	=> '',
		'category_name'   	=> '',
		'orderby'         	=> 'date',
		'order'           	=> 'DESC',
		'include'         	=> '',
		'exclude'         	=> '',
		'meta_key'        	=> '',
		'meta_value'      	=> '',
		'post_type'       	=> 'works',
		'post_mime_type'  	=> '',
		'post_parent'     	=> '',
		'author'					=> '',
		'author_name'	  		=> '',
		'post_status'     	=> 'publish',
		'suppress_filters'	=> true
	));
	if( is_array($all_work) && count($all_work) > 0 ){

		$args_blade['works'] = array();
		foreach( $all_work as $work ){

			// Get all metadata
			$meta_data_work = get_metadata( 'post', $work->ID );

			$tab_single_related_work = array(
				'title' => $work->post_title,
				'client' => (object) array(
					'name' => $meta_data_work[ WPEXTEND_PREFIX_DATA_IN_DB .'informations__client_indexable'][0]
				),
				'url' => $work->guid,
				'category' => null,
				'media_url' => null
			);

			// Categories
			$categories = wp_get_post_terms( $work->ID, 'works_type' );
			if( is_array($categories) && count($categories) > 0 ){
				$tab_single_related_work['category'] = (object) array(
					'name' => $categories[0]->name,
					'url' => '/works'
				);
			}

			// Image
			if( has_post_thumbnail( $work->ID ) ){
				$tab_single_related_work['media_url'] = get_src_image_post_thumbnail( $work->ID );
			}

			$args_blade['works'][] = (object) $tab_single_related_work;
		}
	}

	print t_render_blade( 'components/works-related', $args_blade );

?>
