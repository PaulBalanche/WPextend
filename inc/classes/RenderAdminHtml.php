<?php

namespace Wpextend;

/**
*
*/
class RenderAdminHtml {



    /**
    * Construct method
    */
    private function __construct() {

	}



	/**
    * Reader header with open wrap
    */
	static public function header($title){

		return '<div class="wrap"><h1>'.$title.'</h1>';
	}



	/**
    * Open form
    */
	static public function form_open($action = '', $action_hidden = '', $id = '', $class = ''){

		return '<form method="POST" action="'.$action.'" id="'.$id.'" class="'.$class.'" novalidate="novalidate"><input type="hidden" name="action" value="'.$action_hidden.'" />'.wp_nonce_field($action_hidden);
	}



	/**
    * Close form
    */
	static public function form_close($value_submit = 'Submit'){

		return '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.$value_submit.'"></p></form>';
	}



	/**
    * Open edition table
    */
	static public function table_edit_open(){

		return '<table class="form-table table_wpextend"><tbody>';
	}



	/**
    * Close edition table
    */
	static public function table_edit_close(){

		return '</tbody></table>';
	}



	/**
    *
    */
	static public function multiple_media($tab_media = [], $name_input = '', $multiple = true, $acceptAllTypeFile = false){

		$is_emtpy = true;
		$return = '';

		if( !empty( $name_input ) ){

			if( !is_array($tab_media) && is_numeric($tab_media) ){
				$tab_media = [$tab_media];
			}

			$return .= '<div class="contner_multiple_media">
				<ul class="sortable">';
				if( is_array($tab_media) && count($tab_media) ){

					foreach( $tab_media as $val ){
						
						$instance_post = get_post( $val );
						if( is_object( $instance_post ) ){

							$is_emtpy = false;

							$return .= '<li class="single_media_wpextend">';

							if( strpos( $instance_post->post_mime_type, 'image' ) !== false ){
								
								$src_thumbnail_image = wp_get_attachment_image_src( $val, 'thumbnail' );

								$return .= '<img src="'.$src_thumbnail_image[0].'" >';
							}
							else{
								$return .= '<span class="file">
						           	<span><strong>'.$instance_post->post_title. '</strong><br /><br /><i>(' . $instance_post->post_mime_type . ')</i></span>
						        </span>';								
							}

							$return .= '<input type="hidden" name="'.$name_input; if( $multiple ){ $return .= '[]'; } $return .= '" class="input_upload_multiple_img_wpextend" value="'.$val.'" />';
							if( !$multiple ){
								$return .= '<a href="" class="link_upload_multiple_media_wpextend dashicons dashicons-edit" data-name_input="'.$name_input.'" data-multiple="'.$multiple.'" data-accept-all-type-file="'.$acceptAllTypeFile.'" ></a>';
							}
							$return .= '<span class="remove_multiple_media_wpextend dashicons dashicons-no" data-name_input="'.$name_input.'"></span>
							</li>';
						}
					}
				}

				if( $multiple || $is_emtpy ){
					$return .= '
						<li class="add_multiple_media_wpextend">
							<a href="" class="link_upload_multiple_media_wpextend dashicons dashicons-plus-alt" data-name_input="'.$name_input.'" data-multiple="'.$multiple.'" data-accept-all-type-file="'.$acceptAllTypeFile.'" ></a>
						</li>';
				}

				$return .= '</ul>
			</div>';
		}

		return $return;
	}



}