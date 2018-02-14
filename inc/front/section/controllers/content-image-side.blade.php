<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title' => $data_section->post_title,
		'body' => apply_filters('the_content', $data_section->post_content, false),
		'side' => $data_section->meta_data->configuration['side'],
		'media_url' => null,
		'link_label' => null,
		'link_url' => null,
		'bkg_class' => $background_class_section
	);

	// Links
	if(is_array( $data_section->meta_data->configuration['lien']) ){
		if( !empty($data_section->meta_data->configuration['lien']['label']) && !empty($data_section->meta_data->configuration['lien']['link']) ){
			$args_blade['link_label'] = $data_section->meta_data->configuration['lien']['label'];
			$args_blade['link_url'] = $data_section->meta_data->configuration['lien']['link'];
		}
	}

	// File image
	if( has_post_thumbnail( $data_section->ID ) ){
		$args_blade['media_url'] = get_src_image_post_thumbnail( $data_section->ID );
	}

	print t_render_blade('components/content-image-side', $args_blade);
?>
