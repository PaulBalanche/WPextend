<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title' => $data_section->post_title,
		'media_url' => null,
		'bkg_class' => $background_class_section,
		'img_url' => null,
		'client' => array(
			'name' => $data_section->post_parent_meta_data->informations__client_indexable,
			'logo_url' => null,
			'logo_icon' => null
		)
	);

	// File image
	if( has_post_thumbnail( $data_section->ID ) ){
		$args_blade['img_url'] = get_src_image_post_thumbnail( $data_section->ID );
	}

	// Logo client image
	if( !empty($data_section->post_parent_meta_data->informations['logo-client']) && is_numeric($data_section->post_parent_meta_data->informations['logo-client']) && $data_section->post_parent_meta_data->informations['logo-client'] > -1 ){
		$img_src = get_post($data_section->post_parent_meta_data->informations['logo-client']);
		$args_blade['client']['logo_url'] = $img_src->guid;
	}
	$args_blade['client'] = (object) $args_blade['client'];

	print t_render_blade('components/work-title', $args_blade);

?>
