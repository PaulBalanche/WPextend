<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'bkg_class' => $background_class_section,
		'title' => $data_section->post_title,
		'subtitle' => null,
		'claim' => null,
		'join' => null,
		'employees' => null
	);


	$meta_data_section = get_metadata( 'post', $data_section->ID );
	foreach( $meta_data_section as $key_data => $val_data ){
		if( strpos( $key_data, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
			$meta_data_section[ $key_data ] = get_post_meta( $data_section->ID, $key_data, true );
		}
	}

	if( array_key_exists('meta_buzzpress_configuration', $meta_data_section) &&
		array_key_exists('subtitle', $meta_data_section['meta_buzzpress_configuration']) ){
		$args_blade['subtitle'] = $meta_data_section['meta_buzzpress_configuration']['subtitle'];
	}
	if( array_key_exists('meta_buzzpress_configuration', $meta_data_section) &&
		array_key_exists('claim', $meta_data_section['meta_buzzpress_configuration']) ){
		$args_blade['claim'] = $meta_data_section['meta_buzzpress_configuration']['claim'];
	}

	if( array_key_exists('meta_buzzpress_join-box', $meta_data_section) ){
		$args_blade['join'] = (object) array(
			'title'			=> $meta_data_section['meta_buzzpress_join-box']['title'],
			'body'			=> apply_filters( 'the_content', $meta_data_section['meta_buzzpress_join-box']['text'], false),
			'link_label'	=> $meta_data_section['meta_buzzpress_join-box']['link']['label'],
			'link_url'		=> $meta_data_section['meta_buzzpress_join-box']['link']['link']
		);
	}


	// Team
	$all_team = get_posts( array(
		'posts_per_page'  	=> -1,
		'offset'          	=> 0,
		'category'        	=> '',
		'category_name'   	=> '',
		'orderby'         	=> 'menu_order',
		'order'           	=> 'ASC',
		'include'         	=> '',
		'exclude'         	=> '',
		'meta_key'        	=> '',
		'meta_value'      	=> '',
		'post_type'       	=> 'team',
		'post_mime_type'  	=> '',
		'post_parent'     	=> '',
		'author'			=> '',
		'author_name'	  	=> '',
		'post_status'     	=> 'publish',
		'suppress_filters'	=> true
	));

	if( is_array($all_team) && count($all_team) > 0 ){

		$args_blade['jobs'] = array();
		foreach( $all_team as $brother ){

			$args_team_temp = array(
				'name' => $brother->post_title,
				'function' => null,
				'media_url' => null
			);

			$meta_data_brother = get_metadata( 'post', $brother->ID );
			foreach( $meta_data_brother as $key_data => $val_data ){
				if( strpos( $key_data, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
					$meta_data_brother[ $key_data ] = get_post_meta( $brother->ID, $key_data, true );
				}
			}

			if( array_key_exists('meta_buzzpress_data', $meta_data_brother) &&
				array_key_exists('function', $meta_data_brother['meta_buzzpress_data']) ){ $args_team_temp['function'] = $meta_data_brother['meta_buzzpress_data']['function']; }

			// File image
			if( has_post_thumbnail( $brother->ID ) ){
				$args_team_temp['media_url'] = get_src_image_post_thumbnail( $brother->ID );
			}

			$args_blade['employees'][] = (object) $args_team_temp;
		}
	}

	print t_render_blade( 'components/team-list', $args_blade );

?>