<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title' => $data_section->post_title,
		'body' => apply_filters('the_content', $data_section->post_content, false),
		'textLeft' => $data_section->meta_data->configuration['texte-bottom-left'],
		'textRight' => $data_section->meta_data->configuration['texte-bottom-right'],
		'bkg_url' => null,
		'bkg_class' => $background_class_section
	);

	// File image
	if( has_post_thumbnail( $data_section->ID ) ){
		$args_blade['bkg_url'] = get_src_image_post_thumbnail( $data_section->ID );
	}

	print t_render_blade('components/about-drive-change', $args_blade);
?>
