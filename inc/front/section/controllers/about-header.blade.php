<?php

	$args_blade = array(
		'title' => $data_section->post_title,
		'subtitle' => $data_section->meta_data->configuration['sous-titre']
	);

	print t_render_blade('components/about-header', $args_blade);
?>
