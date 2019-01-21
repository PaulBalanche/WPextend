<?php

	/**
	* Check if array is assocative or not
	*
	* @return boolean
	*/
	function isAssoc(array $arr) {
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}




	/**
	* Convert variable to boolean
	*
	* @return boolean
	*/
	function convertToBoolean($var) {

		if(is_numeric($var) ){

			if($var == 0)
				return false;
			else
				return true;
		}
		else{
			switch($var){
				case 'true':
					return true;

				case 'false':
					return false;
			}
		}
		return false;
	}



	/**
	* Function pour récupérer le liens de l'image thumbnail
	*
	* @return string
	*/
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




	/**
	* Helper qui print un array
	*
	*/
	function pre($array){

		echo "<pre>";
			print_r($array);
		echo "</pre>";
	}