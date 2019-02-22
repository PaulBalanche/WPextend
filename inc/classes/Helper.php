<?php

namespace Wpextend;

/**
 * Helper class
 * 
 */
class Helper {



	/**
     * Clean repeatable variable
     * 
     */
	static public function clean_repeatable_element($value, $is_link = false){

        foreach($value as $key => $val){
            if( 
                ( $is_link && empty(trim($val['link'])) && empty(trim($val['label'])) ) ||
                ( !$is_link && empty(trim($val)) )
            ) {
                unset($value[$key]);
            }
        }

        return $value;
	}



}