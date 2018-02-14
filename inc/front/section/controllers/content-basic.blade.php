<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title' => $data_section->post_title,
		'body' => apply_filters('the_content', $data_section->post_content, false),
		'link_label' => null,
		'link_url' => null,
		'side' => $data_section->meta_data->data['side'],
		'bkg_url' => '',
		'bkg_class' => $background_class_section
	);

	// Links
	if(is_array( $data_section->meta_data->data['lien']) ){
		if( !empty($data_section->meta_data->data['lien']['label']) && !empty($data_section->meta_data->data['lien']['link']) ){
			$args_blade['link_label'] = $data_section->meta_data->data['lien']['label'];
			$args_blade['link_url'] = $data_section->meta_data->data['lien']['link'];
		}
	}

	// File image
	if( !empty($data_section->meta_data->data['background']) && is_numeric($data_section->meta_data->data['background']) && $data_section->meta_data->data['background'] > -1 ){
		$video_url = get_post($data_section->meta_data->data['background']);
		$args_blade['bkg_url'] = $video_url->guid;
	}

	print t_render_blade('components/content-basic', $args_blade);

?>
