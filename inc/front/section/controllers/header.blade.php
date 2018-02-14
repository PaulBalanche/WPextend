<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'slides' => null,
		'links' => null,
		'size' => $data_section->meta_data->configuration['size'],
		'video_url' => null,
		'vimeo_url' => $data_section->meta_data->configuration['vimeo-url'],
		'image_url' => null,
		'bkg_class' => $background_class_section
	);

	// Title slide
	if(is_array( $data_section->meta_data->configuration['titres']) && count($data_section->meta_data->configuration['titres']) > 0 ){
		foreach($data_section->meta_data->configuration['titres'] as $titre){
			if( !empty($titre) ){
				$args_blade['slides'][] = (object) array( 'title' => $titre, 'body' => '' );
			}
		}
	}

	// Links
	if(is_array( $data_section->meta_data->configuration['liens']) && count($data_section->meta_data->configuration['liens']) > 0 ){
		foreach($data_section->meta_data->configuration['liens'] as $liens){

			if( !empty($liens['label']) && !empty($liens['link']) ){
				$args_blade['links'][] = (object) array(
					'label' => $liens['label'],
					'url' => $liens['link']
				);
			}
		}
	}

	// File Video
	if( !empty($data_section->meta_data->configuration['video']) && is_numeric($data_section->meta_data->configuration['video']) && $data_section->meta_data->configuration['video'] > -1 ){
		$video_url = get_post($data_section->meta_data->configuration['video']);
		if( $video_url )
			$args_blade['video_url'] = $video_url->guid;
	}

	// File image
	if( has_post_thumbnail( $data_section->ID ) ){
		$args_blade['image_url'] = get_src_image_post_thumbnail( $data_section->ID );
	}

	print t_render_blade('components/header', $args_blade);
?>
