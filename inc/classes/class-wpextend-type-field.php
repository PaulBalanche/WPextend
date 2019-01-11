<?php


/**
 *
 */
class Wpextend_Type_Field {

	public static $class_input_text = 'input_text_wpextend';
	public static $class_input_link = 'input_link_wpextend';



    /**
     *
     */
    public function __construct()
    {
    }
    


	 /**
     *
     */
    public static function get_available_fields() {
		 return array(
	 		'text' 					=> 'Text',
	 		'textarea' 				=> 'Textarea',
	 		'select' 				=> 'Select',
			'select_post_type'		=> 'Select post type',
	 		'radio' 				=> 'Radio',
	 		'checkbox' 				=> 'Checkbox',
			'link' 					=> 'Link',
	 		'image' 				=> 'Image',
	 		'gallery_image'			=> 'Image gallery',
	 		'multiple_files'		=> 'Multiple files',
	 		'file' 					=> 'File',
	 		'daterange'				=> 'Datepicker range',
	 		'sliderrange'			=> 'Slider range'
	 	);
    }



    /**
     *
     */
    public static function render_input_text( $label, $name, $value = '', $placeholder = '', $repeatable = false, $description = '' ) {

		 $retour_html = '<tr class="tr_'.$name.'">
		 <th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
		 <td>';
		 if( $repeatable ){

			 if( is_array($value) ){
				 foreach($value as $single_value){
					 $retour_html .= '<input name="'.$name.'[]" type="text" id="input_'.$name.'" class="input_'.$name.' repeatable_field '.self::$class_input_text.'" value="'.str_replace('"', '&quot;', $single_value).'" placeholder="'.$placeholder.'" class="regular-text ltr">';
				 }
			 }
			 else{
			 	$retour_html .= '<input name="'.$name.'[]" type="text" id="input_'.$name.'" class="input_'.$name.' repeatable_field '.self::$class_input_text.'" value="" placeholder="'.$placeholder.'" class="regular-text ltr">';
			}

			 $retour_html .= '<span class="repeat_field">+</span>';
		 }
		 else{
		 	$retour_html .= '<input name="'.$name.'" type="text" id="input_'.$name.'" class="input_'.$name.' '.self::$class_input_text.'" value="'.str_replace('"', '&quot;', $value).'" placeholder="'.$placeholder.'" class="regular-text ltr">';
		 }
		 $retour_html .= '</td>
		 </tr>';

		 return $retour_html;
    }




    /**
     *
     */
    public static function render_disable_input_text( $label, $name, $value = '', $description = '' ) {

		$retour_html = '<tr class="tr_'.$name.'">
		<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
			<td>
		 		<input type="text" id="input_'.$name.'" class="input_'.$name.' '.self::$class_input_text.'" value="'.str_replace('"', '&quot;', $value).'" class="regular-text ltr" disabled >
		 		<input name="'.$name.'" type="hidden" value="'.str_replace('"', '&quot;', $value).'" />
			</td>
		</tr>';

		return $retour_html;
	}
	


	/**
     *
     */
    public static function render_label_and_free_html( $label, $name, $html = '', $description = '' ) {

		$retour_html = '<tr class="tr_'.$name.'">
		<th scope="row"><label>'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
			<td>' . $html . '</td>
		</tr>';

		return $retour_html;
    }



	 /**
     *
     */
    public static function render_input_cta( $label, $name, $value = '', $repeatable = false, $description = '', $placeholder_link = 'http://...', $placeholder_label = 'Titre du lien') {

		 $retour_html = '<tr class="tr_'.$name.'">
		 <th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
		 <td>';

		 if( $repeatable ){

		 	if( is_array($value) ){

				 foreach($value as $key_value => $single_value){
					 if( is_array($single_value) ){

						 $value_cta = ( array_key_exists('link', $single_value) ) ? $single_value['link'] : '';
		   			 $label_cta = ( array_key_exists('label', $single_value) ) ? $single_value['label'] : '';

						 $retour_html .= '<div class="repeatable_field input_cta"><input name="'.$name.'['.$key_value.'][link]" type="text" id="input_'.$name.'" class="input_'.$name.' cta_link '.self::$class_input_link.'" value="'.str_replace('"', '&quot;', $value_cta).'" placeholder="'.$placeholder_link.'" class="regular-text ltr">
						<input name="'.$name.'['.$key_value.'][label]" type="text" class="input_'.$name.' cta_label '.self::$class_input_link.'" value="'.str_replace('"', '&quot;', $label_cta).'" placeholder="'.$placeholder_label.'" class="regular-text ltr"></div>';
					 }
					 else{

	   				 $retour_html .= '<div class="repeatable_field input_cta"><input name="'.$name.'['.$key_value.'][link]" type="text" id="input_'.$name.'" class="input_'.$name.' cta_link '.self::$class_input_link.'" value="" placeholder="'.$placeholder_link.'" class="regular-text ltr">
		   			  <input name="'.$name.'['.$key_value.'][label]" type="text" class="input_'.$name.' cta_label '.self::$class_input_link.'" value="" placeholder="'.$placeholder_label.'" class="regular-text ltr"></div>';
					 }
				 }
			 }
			 else{

				$value_cta = '';
   				$label_cta = '';

   			 $retour_html .= '<div class="repeatable_field input_cta"><input name="'.$name.'[0][link]" type="text" id="input_'.$name.'" class="input_'.$name.' cta_link '.self::$class_input_link.'" value="'.str_replace('"', '&quot;', $value_cta).'" placeholder="'.$placeholder_link.'" class="regular-text ltr">
   		  <input name="'.$name.'[0][label]" type="text" class="input_'.$name.' cta_label '.self::$class_input_link.'" value="'.str_replace('"', '&quot;', $label_cta).'" placeholder="'.$placeholder_label.'" class="regular-text ltr"></div>';
			 }

			 $retour_html .= '<span class="repeat_field">+</span><input type="hidden" class="input_hidden_cta" value="'.$name.'[index]" />';
		 }
		 else{

			  $value_cta = ( is_array($value) && array_key_exists('link', $value) ) ? $value['link'] : '';
			  $label_cta = ( is_array($value) && array_key_exists('label', $value) ) ? $value['label'] : '';

			  $retour_html .= '<div class="input_cta"><input name="'.$name.'[link]" type="text" id="input_'.$name.'" class="input_'.$name.' cta_link '.self::$class_input_link.'" value="'.str_replace('"', '&quot;', $value_cta).'" placeholder="'.$placeholder_link.'" class="regular-text ltr">
			<input name="'.$name.'[label]" type="text" class="input_'.$name.' cta_label '.self::$class_input_link.'" value="'.str_replace('"', '&quot;', $label_cta).'" placeholder="'.$placeholder_label.'" class="regular-text ltr"></div>';
		 }

		 $retour_html .= '</td>
		 </tr>';

		 return $retour_html;
    }


    public static function get_link_object( $data_link ){

    	if( !$data_link ||
    		!is_array($data_link) ||
    		(!isset($data_link['label']) || empty($data_link['label']) ) ||
    		( !isset($data_link['link']) || empty($data_link['link']) ) ){
    		return null;
    	}

		$object_link = [
			'target' => null
		];

		if( $data_link && is_array($data_link) ){

			if( isset($data_link['label']) ){
				$object_link['label'] = $data_link['label'];
				$object_link['title'] = $data_link['label'];
			}

			if( isset($data_link['link']) ){
				$object_link['url'] = $data_link['link'];
			}
		}

		return (object) $object_link;
	}



	 public static function render_input_hidden( $name, $value = '', $repeatable = false ) {

		 $retour_html = '<tr class="tr_'.$name.' tr_input_hidden">
		 <th scope="row"></th>
		 <td><input name="'.$name.'" type="hidden" id="input_'.$name.'" value="'.$value.'" ></td>
		 </tr>';

		 return $retour_html;
    }





    /**
     *
     */
    public static function render_input_textarea( $label, $name, $value = '', $repeatable = false, $description = '', $tinymce = 'true'){

		$retour_html = '<tr class="tr_'.$name.'">
		<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
		<td>';

		ob_start();
		wp_editor( stripslashes($value), $name, [
			'wpautop'             	=> true,
			'media_buttons'       	=> true,
			'default_editor'      	=> '',
			'drag_drop_upload'    	=> false,
			'textarea_rows'       	=> 20,
			'teeny'               	=> false,
			'dfw'                 	=> false,
			'_content_editor_dfw' 	=> false,
			'tinymce'             	=> $tinymce,
			'quicktags'           	=> true,
			'editor_height'			=> 120
		]);
		$retour_html .= ob_get_contents();
		ob_end_clean();

		$retour_html .= '</td>
		</tr>';

		return $retour_html;
    }



    /**
     *
     */
    public static function render_input_select( $label, $name, $list_option = array(), $defaut_value = false, $repeatable = false, $description = '' ) {

		if( $repeatable ){
			
			$retour_html = '<tr class="tr_'.$name.'">
			 	<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
			 	<td>';

			if( is_array($defaut_value) && count($defaut_value) > 0 ){

		 		foreach( $defaut_value as $key => $val ){

		 			$retour_html .= '<select id="input_'.$name.'" class="input_'.$name.' repeatable_field" name="'.$name.'[]"><option value="null"></option>';

		 			if( isAssoc($list_option) ) {

		 				foreach( $list_option as $key2 => $val2) {
							if($val && $val == $key2)
								$retour_html .= '<option selected="selected" value="'.$key2.'">'.$val2.'</option>';
							else
		 						$retour_html .= '<option value="'.$key2.'">'.$val2.'</option>';
		 		  		}
		 			}
		 			else{
			 			foreach( $list_option as $val2) {
							if($val && $val == $val2)
								$retour_html .= '<option selected="selected" value="'.$val2.'">'.$val2.'</option>';
							else
		 						$retour_html .= '<option value="'.$val2.'">'.$val2.'</option>';
		 		  		}
		 		  	}
		 		  	$retour_html .= '</select>';
		 		}
		 	}
		 	else{

		 		$retour_html .= '<select id="input_'.$name.'" class="input_'.$name.' repeatable_field" name="'.$name.'[]"><option value="null"></option>';

		 		if( isAssoc($list_option) ) {
		 			foreach( $list_option as $key => $val) {
						if($defaut_value && $defaut_value == $key)
							$retour_html .= '<option selected="selected" value="'.$key.'">'.$val.'</option>';
						else
	 						$retour_html .= '<option value="'.$key.'">'.$val.'</option>';
	 		  		}
		 		}
		 		else{
		 			foreach( $list_option as $val) {
						if($defaut_value && $defaut_value == $val)
							$retour_html .= '<option selected="selected" value="'.$val.'">'.$val.'</option>';
						else
	 						$retour_html .= '<option value="'.$val.'">'.$val.'</option>';
	 		  		}
	 		  	}
	 		  	$retour_html .= '</select>';
		 	}

	 		$retour_html .= '<span class="repeat_field">+</span>';

	 		$retour_html .= '</td>
		 	</tr>';
		}
		else{

		 	$retour_html = '<tr class="tr_'.$name.'">
		 	<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label></th>
		 	<td><select id="input_'.$name.'" class="input_'.$name.'" name="'.$name.'"><option value="null"></option>';

			 if( is_array($list_option) && count($list_option) > 0 ) {

				 if( isAssoc($list_option) ) {
					 foreach( $list_option as $key => $val) {

		                if( is_array($val) ){

							if($defaut_value && $defaut_value == $key)
		   					 	$retour_html .= '<option selected="selected" value="'.$key.'">'.$key.'</option>';
		   					else
		   						$retour_html .= '<option value="'.$key.'">'.$key.'</option>';

		                  foreach( $val as $key2 => $val2 ) {

		                     if( is_array($val2) ){
		                        foreach( $val2 as $key3 => $val3 ) {

		                           if( is_array($val3) ){

		                              foreach( $val3 as $key4 => $val4 ) {

		                                 if($defaut_value && $defaut_value == $key.'__'.$key2.'__'.$key3.'__'.$key4)
		                                    $retour_html .= '<option selected="selected" value="'.$key.'__'.$key2.'__'.$key3.'__'.$key4.'">'.$key.'__'.$key2.'__'.$key3.'__'.$val4.'</option>';
		                                 else
		                                    $retour_html .= '<option value="'.$key.'__'.$key2.'__'.$key3.'__'.$key4.'">'.$key.'__'.$key2.'__'.$key3.'__'.$val4.'</option>';
		                              }
		                           }
		                           else{
		                              if($defaut_value && $defaut_value == $key.'__'.$key2.'__'.$key3)
		                                 $retour_html .= '<option selected="selected" value="'.$key.'__'.$key2.'__'.$key3.'">'.$key.'__'.$key2.'__'.$val3.'</option>';
		                              else
		                                 $retour_html .= '<option value="'.$key.'__'.$key2.'__'.$key3.'">'.$key.'__'.$key2.'__'.$val3.'</option>';
		                           }
		                        }
		                     }
		                     else{
		                        if($defaut_value && $defaut_value == $key.'__'.$key2)
		                           $retour_html .= '<option selected="selected" value="'.$key.'__'.$key2.'">'.$key.'__'.$val2.'</option>';
		                        else
		                           $retour_html .= '<option value="'.$key.'__'.$key2.'">'.$key.'__'.$val2.'</option>';
		                     }
		                  }
		                }
		                else{
		   					 if($defaut_value && $defaut_value == $key)
		   					 	$retour_html .= '<option selected="selected" value="'.$key.'">'.$val.'</option>';
		   					 else
		   						$retour_html .= '<option value="'.$key.'">'.$val.'</option>';
		               }
					}
				 }
				 else{
					foreach( $list_option as $val) {
						if($defaut_value && $defaut_value == $val)
							$retour_html .= '<option selected="selected" value="'.$val.'">'.$val.'</option>';
						else
	 						$retour_html .= '<option value="'.$val.'">'.$val.'</option>';
	 		  		}
				}
			 }

			$retour_html .= '</select></td>
		 </tr>';
		}
		return $retour_html;
    }






    /**
     *
     */
    public static function render_input_radio( $label, $name, $list_option = array(), $defaut_value = false, $repeatable = false, $description = '' ) {

		$retour_html = '<tr class="tr_'.$name.'">
		<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
		<td>';
		if( is_array($list_option) && count($list_option) > 0 ) {

			$incide = 0;
			if( isAssoc($list_option) ) {
				foreach( $list_option as $key => $val) {
					if( ($defaut_value && $defaut_value == $key) || ($defaut_value == false && $incide == 0) )
				 		$retour_html .= '<input type="radio" name="'.$name.'" value="'.$key.'" checked> '.$val.'<br>';
					else
						$retour_html .= '<input type="radio" name="'.$name.'" value="'.$key.'"> '.$val.'<br>';

					$incide++;
				}
			}
			else{
				foreach( $list_option as $val) {
					if( ($defaut_value && $defaut_value == $val) || ($defaut_value == false && $incide == 0) )
						$retour_html .= '<input type="radio" name="'.$name.'" value="'.$val.'" checked> '.$val.'<br>';
				  	else
				  		$retour_html .= '<input type="radio" name="'.$name.'" value="'.$val.'"> '.$val.'<br>';

				  	$incide++;
				}
			}
		}
		$retour_html .= '</td>
		</tr>';

		return $retour_html;
    }





    /**
     *
     */
    public static function render_input_checkbox( $label, $name, $list_option = array(), $defaut_value = false, $repeatable = false, $description = '' ) {

		 $retour_html = '<tr class="tr_'.$name.'">
		 <th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
		 <td>';
		 if( is_array($list_option) && count($list_option) > 0 ) {

			if( isAssoc($list_option) ) {
				foreach( $list_option as $key => $val) {
					if( is_array($defaut_value) && in_array($key, $defaut_value) )
						$retour_html .= '<label for="'.$name.'_'.$key.'"><input type="checkbox" name="'.$name.'[]" id="'.$name.'_'.$key.'" value="'.$key.'" checked> '.$val.'</label><br>';
					else
				  		$retour_html .= '<label for="'.$name.'_'.$key.'"><input type="checkbox" name="'.$name.'[]" id="'.$name.'_'.$key.'" value="'.$key.'"> '.$val.'</label><br>';
				}
			}
			else{
			  foreach( $list_option as $val) {
				  if( is_array($defaut_value) && in_array($val, $defaut_value) )
					  $retour_html .= '<label for="'.$name.'_'.$val.'"><input type="checkbox" name="'.$name.'[]" id="'.$name.'_'.$val.'" value="'.$val.'" checked> '.$val.'</label><br>';
				  else
					  $retour_html .= '<label for="'.$name.'_'.$val.'"><input type="checkbox" name="'.$name.'[]" id="'.$name.'_'.$val.'" value="'.$val.'"> '.$val.'</label><br>';
				}
		  }
		 }
		 $retour_html .= '</td>
		 </tr>';

		 return $retour_html;
    }




    /**
     *
     */
    public static function render_input_image( $label, $name, $defaut_value = false, $repeatable = false, $description = '' ){

		$no_image = true;

		if( is_numeric($defaut_value) ){
			$attachment_image_src = wp_get_attachment_image_src( $defaut_value );
			if( $attachment_image_src ){
				$html_image_post_thumbnail = '<img src="'.$attachment_image_src[0].'" />';
				$class_link_upload_file_wpextend = '';
				$class_link_remove = '';
				$no_image  = false;
			}
		}

		if( $no_image ){
			$html_image_post_thumbnail = 'Add image';
			$class_link_upload_file_wpextend = 'button button-primary';
			$class_link_remove = 'hidden';
		}

		$retour_html = '<tr class="tr_'.$name.'">
			<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
			<td>';

				$retour_html .= Wpextend_Render_Admin_Html::multiple_media( $defaut_value, $name, false, false );

			$retour_html .= '</td>
		</tr>';

		return $retour_html;
    }



    public static function get_image_object($id_image, $size = 'full'){

		if( $id_image && is_numeric($id_image) ){

			$info_image = wp_get_attachment_image_src($id_image, $size);
			if( $info_image && is_array($info_image) ){

				$object_image = (object) [
					'src' => $info_image[0],
					'url' => $info_image[0],
					'title' => Thorin::esc_attr( get_the_title($id_image) ),
					'alt' => get_post_meta( $id_image, '_wp_attachment_image_alt', true )
				];

				return $object_image;
			}
		}

		return null;
	}



    /**
    *
    */
    public static function render_input_image_gallery( $label, $name, $value, $description = '', $acceptAllTypeFile = false ) {

    	if( !is_array($value) ){
    		$value = [];
    	}

		$retour_html = '<tr class="tr_'.$name.'">
			<th scope="row"><label>'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
			<td>';

				$retour_html .= Wpextend_Render_Admin_Html::multiple_media( $value, $name, true, $acceptAllTypeFile );
				
				$retour_html .= '</td>
		</tr>';

		return $retour_html;
    }



    /**
     *
     */
    public static function render_input_file( $label, $name, $defaut_value = false, $repeatable = false, $description = '' ){

      $no_file = true;

      if( is_numeric($defaut_value) && $defaut_value > -1 ){

      	$instance_post = get_post( $defaut_value );
      	if( is_object( $instance_post ) ){
			$name_file = '<strong>' . $instance_post->post_title . '</strong><br /><br /><i>(' . $instance_post->post_mime_type . ')</i>';
			if( !empty( $name_file ) ){
				$html_file = $name_file;
				$class_link_upload_file_wpextend = '';
				$class_link_remove = '';
				$no_file  = false;
			}
		}
      }

      if( $no_file ){
         $html_file = 'Add file';
         $class_link_upload_file_wpextend = 'button button-primary';
         $class_link_remove = 'hidden';
      }

    	$retour_html = '<tr class="tr_'.$name.'">
			<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
			<td>';

				$retour_html .= Wpextend_Render_Admin_Html::multiple_media( $defaut_value, $name, false, true );

			$retour_html .= '</td>
		</tr>';

     return $retour_html;
    }



    /**
    *
    */
    public static function render_input_daterange( $label, $name, $value = array('from' => '', 'to' => ''), $placeholder = '', $description = '' ) {

    	if( !is_array($value) ||
    		( !array_key_exists('from', $value) || !array_key_exists('to', $value) )
    	){
    		$value = array('from' => '', 'to' => '');
    	}

		 $retour_html = '<tr class="tr_'.$name.'">
		 <th scope="row"><label>'.stripslashes($label).'</label><i class="description">'.$description.'</i></th>
		 <td>';
			$retour_html .= '
			<label for="input_'.$name.'_from">From </label><input name="'.$name.'[from]" type="text" id="input_'.$name.'_from" class="input_'.$name.' input_daterange_from" value="'.str_replace('"', '&quot;', $value['from']).'" placeholder="'.$placeholder.'" class="regular-text ltr">
			<label for="input_'.$name.'_to"> To </label><input name="'.$name.'[to]" type="text" id="input_'.$name.'_to" class="input_'.$name.' input_daterange_to" value="'.str_replace('"', '&quot;', $value['to']).'" placeholder="'.$placeholder.'" class="regular-text ltr">';
		 $retour_html .= '</td>
		 </tr>';

		 return $retour_html;
    }



    /**
    *
    */
    public static function render_input_sliderrange( $label, $name, $value = '', $placeholder = '' ) {

    	if( !is_array($value) ||
    		( !array_key_exists('from', $value) || !array_key_exists('to', $value) )
    	){
    		$value = array('from' => '0:0', 'to' => '24:00');
    	}

    	$attr_min_slider = explode(':', $value['from']);
    	$attr_max_slider = explode(':', $value['to']);

    	$attr_min_slider = $attr_min_slider[0] * 60 + $attr_min_slider[1];
    	$attr_max_slider = $attr_max_slider[0] * 60 + $attr_max_slider[1];

		$retour_html = '<tr class="tr_'.$name.'">
		<th scope="row"><label for="input_'.$name.'">'.stripslashes($label).'</label><i class="description">'.date('H:i:s').'<br />'.date('(e)').'</i></th>
		<td>';
		$retour_html .= '<div class="container_sliderange">
			<div class="div_sliderrange" attr-min="'.$attr_min_slider.'" attr-max="'.$attr_max_slider.'"></div>
			<label for="input_'.$name.'_from">From </label><input name="'.$name.'[from]" type="text" id="input_'.$name.'_from" class="input_'.$name.' input_sliderrange_from" readonly value="'.str_replace('"', '&quot;', $value['from']).'" placeholder="'.$placeholder.'" class="regular-text ltr">
			<label for="input_'.$name.'_to"> To </label><input name="'.$name.'[to]" type="text" id="input_'.$name.'_to" class="input_'.$name.' input_sliderrange_to" readonly value="'.str_replace('"', '&quot;', $value['to']).'" placeholder="'.$placeholder.'" class="regular-text ltr">
		</div>';
		$retour_html .= '</td>
		</tr>';

		return $retour_html;
    }




    public static function force_boolean($bool){

    	if( $bool &&
    		(
    			$bool === true ||
    			$bool == 'true' ||
    			$bool == '1'
    		)
    	)
    		return true;
    	else
    		return false;
    }



}
