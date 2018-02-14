<?php


/**
 *
 */
class Buzzpress_Render_Admin_Html {

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

		 return '<table class="form-table table_buzzpress"><tbody>';
	 }





	 /**
     * Close edition table
     */
	 static public function table_edit_close(){

		 return '</tbody></table>';
	 }




}
