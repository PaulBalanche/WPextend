<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title'			=> $data_section->post_title,
		'bkg_class'		=> $background_class_section,
		'medias'			=> (object) array(
									'title'		=> null,
									'type'		=> null,
									'url'			=> null
								)
	);

	// Images
	if( array_key_exists('list_sections', $data_section->meta_data) && is_array($data_section->meta_data->list_sections) && count($data_section->meta_data->list_sections) > 0){

		$args_blade['medias']	 = array();
		foreach( $data_section->meta_data->list_sections as $id_section_image ){

			$object_image = array(
				'title'		=> null,
				'type'		=> null,
				'url'			=> null
			);
			$data_image = get_post( $id_section_image );
			$object_image['title'] = $data_image->post_title;

			$image_type = get_post_meta( $id_section_image, WPEXTEND_PREFIX_DATA_IN_DB . 'configuration', true );
			$object_image['type'] = $image_type['type'];

			if( has_post_thumbnail( $id_section_image ) ){
				$object_image['url'] = get_src_image_post_thumbnail( $id_section_image );
			}
			$args_blade['medias'][] = (object) $object_image;
		}
	}

	print t_render_blade('components/medias-gallery', $args_blade);

?>
