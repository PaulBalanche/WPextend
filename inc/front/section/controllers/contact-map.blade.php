<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'title' => $data_section->post_title,
		'map_url' => null,
		'socials' => null,
		'bkg_url' => null,
		'bkg_class' => $background_class_section
	);

	// File image
	if( has_post_thumbnail( $data_section->ID ) ){
		$args_blade['map_url'] = get_src_image_post_thumbnail( $data_section->ID );
	}

	// Récupération des lien sociaux
	$instance_section = new Wpextend_Post($data_section->ID);
	$socials = $instance_section->get_sections_pc_buzzpress();
	if( is_array($socials) && count($socials) > 0){

		$args_blade['socials'] = array();

		foreach( $socials as $social ){

			$data_social = get_post($social);

			$args_social = array(
				'name' => $data_social->post_title,
				'url' => null,
				'icon' => null
			);

			$meta_data_social = get_metadata( 'post', $social );
			foreach( $meta_data_social as $key_data => $val_data ){
				if( strpos( $key_data, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
					$meta_data_social[ $key_data ] = get_post_meta( $social, $key_data, true );
				}
			}
			if( array_key_exists('link', $meta_data_social[ WPEXTEND_PREFIX_DATA_IN_DB . 'data']) ){ $args_social['url'] = $meta_data_social[ WPEXTEND_PREFIX_DATA_IN_DB . 'data']['link']; }
			if( array_key_exists('icon', $meta_data_social[ WPEXTEND_PREFIX_DATA_IN_DB . 'data']) ){ $args_social['icon'] = $meta_data_social[ WPEXTEND_PREFIX_DATA_IN_DB . 'data']['icon']; }

			$args_blade['socials'][] = (object) $args_social;
		}
	}

	print t_render_blade( 'components/contact-map', $args_blade );

?>
