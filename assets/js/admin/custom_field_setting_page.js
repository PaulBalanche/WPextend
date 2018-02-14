jQuery( document ).ready(function() {


	jQuery(".input_type_field, .input_metabox").change(function(){

		if( jQuery(this).val() == 'select' || jQuery(this).val() == 'radio' || jQuery(this).val() == 'checkbox' ){
			jQuery(this).after('<table><tr><td><input type="text" name="options[]" value="" /></td></tr><tr><td><input type="bouton" class="button button-primary" value="Add option" onclick="add_option(this);" /></td></tr>');
		}
		else if( jQuery(this).val() == 'select_post_type' ){
			jQuery(this).parents('.form-table').find('.tr_post_type_options th, .tr_post_type_options td').show();
		}
	});

});


function add_option(elt){

	jQuery(elt).parent('td').parent('tr').before('<tr><td><input type="text" name="options[]" value="" /></td></tr>');
	return false;
}
