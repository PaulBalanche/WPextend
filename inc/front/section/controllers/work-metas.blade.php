<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'bkg_class'		=> $background_class_section,
		'category'		=> (object) array( 'name' => null ),
		'client'			=> (object) array( 'name' => $data_section->post_parent_meta_data->informations__client_indexable ),
		'live_url'		=> null,
		'label_url'		=> 'See live',
		'body'			=> apply_filters('the_content', $data_section->post_content, false)
	);

	// Links
	if( is_array( $data_section->meta_data->informations['live-url']) ){
		if( !empty($data_section->meta_data->informations['live-url']['link']) ){
			$args_blade['live_url'] = $data_section->meta_data->informations['live-url']['link'];
		}
		if( !empty($data_section->meta_data->informations['live-url']['label']) ){
			$args_blade['label_url'] = $data_section->meta_data->informations['live-url']['label'];
		}
	}

	// Categories
	$categories = wp_get_post_terms( $data_section->post_parent_meta_data->ID, 'works_type' );
	if( is_array($categories) && count($categories) > 0 ){
		$args_blade['category'] = (object) array( 'name' => $categories[0]->name );
	}

	print t_render_blade( 'components/work-metas', $args_blade );

?>
