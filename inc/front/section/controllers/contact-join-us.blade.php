<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title' => $data_section->post_title,
		'body' => apply_filters('the_content', $data_section->post_content, false),
		'jobs' => null,
		'bkg_url' => null,
		'bkg_class' => $background_class_section
	);

	$all_jobs = get_posts( array(
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
		'post_type'       	=> 'jobs',
		'post_mime_type'  	=> '',
		'post_parent'     	=> '',
		'author'					=> '',
		'author_name'	  		=> '',
		'post_status'     	=> 'publish',
		'suppress_filters'	=> true
	));

	if( is_array($all_jobs) && count($all_jobs) > 0 ){

		$args_blade['jobs'] = array();
		foreach( $all_jobs as $job ){

			$tab_job = array(
				'title' => $job->post_title,
				'url' => 'https://google.com'
			);

			$meta_data_job = get_metadata( 'post', $job->ID );
			foreach( $meta_data_job as $key_data => $val_data ){
				if( strpos( $key_data, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
					$meta_data_job[ $key_data ] = get_post_meta( $job->ID, $key_data, true );
				}
			}
			if( array_key_exists('link', $meta_data_job[ WPEXTEND_PREFIX_DATA_IN_DB . 'data']) ){ $args_social['url'] = $meta_data_job['meta_buzzpress_data']['link']; }

			$args_blade['jobs'][] = (object) $tab_job;
		}
	}

	print t_render_blade( 'components/contact-join-us', $args_blade );

?>
