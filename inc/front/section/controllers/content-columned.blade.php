<?php

	$background_class_section = ($data_section->meta_data->class['background-class'] != '' && $data_section->meta_data->class['background-class'] != 'Aucune...') ? $data_section->meta_data->class['background-class'] : null;

	$args_blade = array(
		'bkg_class' => $background_class_section,
		'bkg_url' => '',
		'title' => $data_section->post_title,
		'columns' => array()
	);


	// Récupération des columns
	$instance_section = new Wpextend_Post($data_section->ID);
	$slides = $instance_section->get_sections_pc_buzzpress();
	if( is_array($slides) && count($slides) > 0){
		foreach( $slides as $slide ){

			$data_current_slide = get_post($slide);

			$data_current_slide->meta_data = (object) array();
			$meta_data_slide = get_metadata( 'post', $slide );
			foreach( $meta_data_slide as $key_data => $val_data ){
				if( strpos( $key_data, WPEXTEND_PREFIX_DATA_IN_DB ) !== false ){
					$data_current_slide->meta_data->{str_replace( WPEXTEND_PREFIX_DATA_IN_DB, '', $key_data )} = get_post_meta( $slide, $key_data, true );
				}
			}

			$args_blade['columns'][] = (object) array(
				'title' => $data_current_slide->post_title,
				'body' => apply_filters('the_content', $data_current_slide->post_content, false)
			);
		}
	}

	print t_render_blade('components/content-columned', $args_blade);
?>
