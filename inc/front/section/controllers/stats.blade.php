<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'bkg_class'		=> $background_class_section,
		'bkg_url'		=> null,
		'title'			=> $data_section->post_title,
		'media_url'		=> null,
		'stats'			=> null
	);

	// File image
	if( has_post_thumbnail( $data_section->ID ) ){
		$args_blade['media_url'] = get_src_image_post_thumbnail( $data_section->ID );
	}


	// Stats
	$instance_section = new Wpextend_Post($data_section->ID);
	$stats = $instance_section->get_sections_pc_buzzpress();
	if( is_array($stats) && count($stats) > 0){

		$args_blade['stats'] = array();

		foreach( $stats as $stat ){

			$data_stat = get_post($stat);
			$args_blade['stats'][] = (object) array(
				'label' => $data_stat->post_title,
				'value' => $data_stat->post_content
			);
		}
	}

	print t_render_blade( 'components/stats', $args_blade );

?>