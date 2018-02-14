<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title'					=> null,
		'body'					=> null,
		'shares'					=> null,
		'media_url'				=> null,
		'poster_url'			=> null,
		'vimeo_url'				=> null,
		'bkg_class'				=> $background_class_section,
		'autoplay'				=> $data_section->meta_data->configuration['autoplay'],
		'loop'					=> $data_section->meta_data->configuration['loop'],
		'muted'					=> $data_section->meta_data->configuration['muted'],
		'in_background'		=> $data_section->meta_data->configuration['in-background']
	);

	// File Video
	if( !empty($data_section->meta_data->configuration['video']) && is_numeric($data_section->meta_data->configuration['video']) && $data_section->meta_data->configuration['video'] > -1 ){
		$video_url = get_post($data_section->meta_data->configuration['video']);
		if( $video_url )
			$args_blade['media_url'] = $video_url->guid;
	}

	// File image
	if( has_post_thumbnail( $data_section->ID ) ){
		$args_blade['poster_url'] = get_src_image_post_thumbnail( $data_section->ID );
	}

	print t_render_blade('components/video', $args_blade);
?>
