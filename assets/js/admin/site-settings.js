jQuery( document ).ready(function() {


   //  jQuery( '#add_category_setting_buzzpress' ).submit( function(event) {
	 //
	// 	 var post_name = jQuery(this).find('#input_name').val();
	// 	 var post_wpnonce = jQuery(this).find('#_wpnonce').val();
	 //
	// 	 event.preventDefault();
	 //
	// 	 jQuery.ajax({
	// 	  method: "POST",
	// 	  url: OBJECT.ajax_url,
	// 	  data: { action: 'add_category_setting_buzzpress', name: post_name, _wpnonce: post_wpnonce, ajax: true }
	// 	})
	//    .done(function( msg ) {
	 //
	//    });
	 //
	//  });



	jQuery( '.add_new_section, .add_new_category_section' ).click( function() {

		jQuery(this).addClass('hidden');
		jQuery(this).next('form').removeClass('hidden');
	});



	jQuery( '.add_new_settings' ).click( function() {

		jQuery(this).addClass('hidden');
		jQuery(this).next('form').removeClass('hidden');
	});




});
