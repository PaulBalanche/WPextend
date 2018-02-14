<?php

	/* Function pour récupérer le contenu d'une page */ 
	function get_contenu_page($id_page, $order = "DESC", $orderby = "date"){
			
		$pages = get_posts(array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'category'         => '',
			'orderby'          => $orderby,
			'order'            => $order,
			'include'          => $id_page,
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'page',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true ));
			
		return $pages;
	}
	/* Fin get_contenu_page() : pour récupérer le contenu d'une page */
	
	
	



	/* Function pour récupérer les data d'un post, ou d'une page, ou autre */ 
	function get_data_post($id_post, $post_type = 'page'){
			
		$post = get_posts(array(
			'posts_per_page'   => -1,
			'include'          => $id_post,
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => $post_type,
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true ));
		
		if(is_array($post) && count($post) == 1){
			return $post[0];
		}
		else
			return false;
	}
	/* Fin get_post_name() : pour récupérer les data d'un post, ou d'une page, ou autre */
	
	
	
	
	
	
	
	/* Function pour récupérer les articles */ 
	function get_articles($order = "DESC", $orderby = "date"){
			
		$pages_enfant = get_posts(array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'category'         => '',
			'orderby'          => $orderby,
			'order'            => $order,
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true ));
			
		return $pages_enfant;
	}
	/* Fin get_articles() : pour récupérer les articles */
	
	
	
	
	
	
	
	
	
	/* Function pour récupérer les pages enfants */ 
	function get_pages_enfant($id_page, $order = "ASC", $orderby = "menu_order"){
	
		if(is_numeric($id_page)){
			
			$pages_enfant = get_posts(array(
				'posts_per_page'   => -1,
				'offset'           => 0,
				'category'         => '',
				'orderby'          => $orderby,
				'order'            => $order,
				'include'          => '',
				'exclude'          => '',
				'meta_key'         => '',
				'meta_value'       => '',
				'post_type'        => 'page',
				'post_mime_type'   => '',
				'post_parent'      => $id_page,
				'post_status'      => 'publish',
				'suppress_filters' => true ));
				
			return $pages_enfant;
		}
		else
			return false;
	}
	/* Fin get_pages_enfant() : pour récupérer les pages enfants */ 
	
	
	
	
	
	
	
	/* Function pour récupérer le liens de l'image thumbnail */ 
	function get_src_image_post_thumbnail($id_post, $taille = "full"){
	
		if(is_numeric($id_post) && has_post_thumbnail($id_post)){
			
			$the_ID_image_post_thumbnail = get_post_thumbnail_id($id_post);
			$src_image_post_thumbnail = wp_get_attachment_image_src($the_ID_image_post_thumbnail, $taille);
			
			return $src_image_post_thumbnail[0];
		}
		else
			return false;
	}
	/* Fin get_src_image_post_thumbnail() : pour récupérer le liens de l'image thumbnail */ 
	
	
	
	
	
	
	
	
	/* Function pour récupérer les images d'un post */ 
	function get_images_post($id_post, $nb_images = -1, $excerpt = false, $order = "ASC", $orderby = "menu_order"){
		
		if($excerpt !== false)
			$nb_images = -1;
		
		$tab_images = get_posts(array(
			'posts_per_page'   => $nb_images,
			'offset'           => 0,
			'category'         => '',
			'orderby'          => $orderby,
			'order'            => $order,
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'attachment',
			'post_mime_type'   => '',
			'post_parent'      => $id_post,
			'suppress_filters' => true ));
		
		
		if($excerpt !== false){
		
			$tab_images_valide = array();
			
			if(count($tab_images)>0){			
				foreach($tab_images as $limage_en_cours){
					if(count($tab_images_valide) == $nb_images)
						break;
						
					if($limage_en_cours->post_excerpt == $excerpt){
						$tab_images_valide[] = $limage_en_cours;
					}
				}
			}
		}
		else
			$tab_images_valide = $tab_images;
			
		return $tab_images_valide;
	}
	/* Fin get_images_post() : pour récupérer les images d'un post */ 
	
	
	
	
	
	
	
	
	
	
	
	/* Function pour récupérer les info d'une image */ 
	function get_info_image($id_image){
		
		$tab_images = get_posts(array(
			'posts_per_page'   => 1,
			'include'          => $id_image,
			'post_type'        => 'attachment'));
			
		return $tab_images;
	}
	/* Fin get_info_image() : pour récupérer les info d'une image */ 
	
	
	
	
	
	
	
	
	
	
	
	
	/* Function pour afficher un résumé */ 
	function cleanCut($string, $length, $cutString = '...'){
		
		$string = strip_tags($string);
		
		if(strlen($string) <= $length){
			return $string;
		}

		$str = substr($string, 0, $length-strlen($cutString)+1);
		return substr($str,0,strrpos($str,' ')).$cutString;
	}
	/* Fin cleanCut() :  pour afficher un résumé */ 

?>