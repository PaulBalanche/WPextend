/** READY **/
jQuery(document).ready(function(){


	jQuery( '.add_new_metabox, .add_new_custom_field' ).click( function() {

		jQuery(this).addClass('hidden');
		jQuery(this).next('form').removeClass('hidden');
	});



	jQuery( '.accordion_wpextend' ).accordion({
		heightStyle: "content",
		collapsible: true,
		active: false
	});

	jQuery( '.tabs' ).tabs();



	if(jQuery(".contner_list_images .sortable").length > 0){
		jQuery(".contner_list_images .sortable").sortable();
	}




	/**
	* Repeatable field
	*
	*/
	jQuery('.repeat_field').click(function(){


		if( jQuery(this).prev('.repeatable_field').length > 0){

			var repeatable_field = jQuery(this).prev( '.repeatable_field' );
			var clone = repeatable_field.clone();

			// If repeatable field is CTA field
			if( jQuery(this).prev('.repeatable_field').hasClass('input_cta') && jQuery(this).next('.input_hidden_cta').length == 1 ){
				var new_name_repeatble_field = jQuery(this).next('.input_hidden_cta').val();
				var nb_repeatble_already_created = jQuery(this).parent('td').find('.repeatable_field').length;
				new_name_repeatble_field = new_name_repeatble_field.replace('index', nb_repeatble_already_created);

				clone.find('.cta_link').attr('name', new_name_repeatble_field + '[link]');
				clone.find('.cta_label').attr('name', new_name_repeatble_field + '[label]');
			}
			// END : If repeatable field is CTA field

			clone.insertAfter(repeatable_field);
		}
	});




	var frame,
		link_upload_img = jQuery('a.link_upload_img_wpextend'),
		link_remove_img = jQuery('span.link_remove_img_wpextend'),
		link_upload_file = jQuery('a.link_upload_file_wpextend'),
		link_remove_file = jQuery('a.link_remove_file_wptexend');

	/**
	* Ajout d'un média
	*
	*/
	link_upload_img.on('click', function( event ){

		link_upload_img_courant = jQuery(this);

		if(frame){
		  frame.open();
		  return false;
		}

		frame = wp.media({
			title: 'Select image',
			button: {
				text: 'Add'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		frame.on('select', function(){

			var attachment = frame.state().get('selection').first().toJSON();
			if( attachment.type == 'image' ){

				if( attachment.sizes )
					var src_image_uploade = attachment.sizes.thumbnail.url;
				else
					var src_image_uploade = attachment.url;

				link_upload_img_courant.html('<img src="'+src_image_uploade+'" />').removeClass('button button-primary');
				link_upload_img_courant.parent().find('.input_upload_img_wpextend').val(attachment.id);
				link_upload_img_courant.parent().find(link_remove_img).removeClass('hidden');
			}
		});

		frame.open();

		return false;
	});


	/**
	* Suppression d'un média
	*
	*/
	link_remove_img.on('click', function( event ){

		link_remove_img_courant = jQuery(this);
		link_remove_img_courant.parent().find('.input_upload_img_wpextend').val(-1);
		link_remove_img_courant.addClass('hidden');
		link_remove_img_courant.parent().find('a.thickbox').html('Add image').addClass('button button-primary');
	});




	/**
	* Ajout d'un fichier
	*
	*/
	link_upload_file.on('click', function( event ){

		link_upload_file_courant = jQuery(this);

		if(frame){
		  frame.open();
		  return false;
		}

		frame = wp.media({
			title: 'Select file',
			button: {
				text: 'Add'
			},
			multiple: false  // Set to true to allow multiple files to be selected
		});

		frame.on('select', function(){

			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();

			link_upload_file_courant.parents('td').find('.input_file_wpextend').val(attachment.id);
			link_upload_file_courant.parents('td').find(link_remove_file).removeClass('hidden');
			link_upload_file_courant.parents('td').find('div.contner_list_files .file').replaceWith('<ul class="sortable"><li><span class="file"><span class="link_upload_file_wpextend"><strong>' + attachment.filename + '</strong><br /><br /><i>(' + attachment.type + ')</i></span></span></li></ul>');			
		});

		frame.open();

		return false;
	});



	/**
	* Suppression d'un fichier
	*
	*/
	link_remove_file.on('click', function( event ){

		event.preventDefault();

		jQuery(this).addClass('hidden');

		var container_input = jQuery(this).parents('td').find('div.contner_list_files');
		container_input.find('.input_file_wpextend').val(-1);

		container_input.find('span.file a').appendTo(container_input);
		container_input.find('span.file').remove();
		container_input.append( container_input.find('ul li').html() );
		container_input.find('ul').remove();

		container_input.find('a.thickbox').html('Add file').addClass('button button-primary');
	});












	var frame,
		link_upload_multiple_img = jQuery('a.link_upload_multiple_img_wpextend'),
		link_remove_multiple_img = jQuery('.remove_image_gallery');

	/**
	* Ajout d'un ou plusieurs média
	*
	*/
	link_upload_multiple_img.on('click', function( event ){

		link_upload_img_courant = jQuery(this);
		name_input_courant = link_upload_img_courant.attr('data-name_input');

		if( link_upload_img_courant.parent().prev('.contner_list_images').length == 1 ){

			var accepteAllTypeFile = false;
			var contner_list_files = link_upload_img_courant.parent().prev('.contner_list_images');
		}
		else if( link_upload_img_courant.parent().prev('.contner_list_files').length == 1 ){

			var accepteAllTypeFile = true;
			var contner_list_files = link_upload_img_courant.parent().prev('.contner_list_files');
		}
		else
			return false;

		if(frame){
		  frame.open();
		  return false;
		}

		frame = wp.media({
			title: 'Select one or more images',
			button: {
				text: 'Add'
			},
			multiple: true  // Set to true to allow multiple files to be selected
		});

		frame.on('select', function(){

			var attachments = frame.state().get('selection').toJSON();
			for(var i in attachments){

				if( attachments[i].type == 'image' ){

					if( attachments[i].sizes )
						var src_image_uploade = attachments[i].sizes.thumbnail.url;
					else
						var src_image_uploade = attachments[i].url;

					contner_list_files.find('.sortable').append('<li class="ui-state-default"><img src="' + src_image_uploade + '" ><input type="hidden" name="' + name_input_courant + '[]" class="input_upload_multiple_img_wpextend" value="' + attachments[i].id + '" /></li>');
				}
				else if( accepteAllTypeFile ){

					var name_file = attachments[i].filename;

					contner_list_files.find('.sortable').append('<li class="ui-state-default"><span class="file"><span><strong>' + name_file + '</strong><br><br><i>(' + attachments[i].type + ')</i></span></span><input type="hidden" name="' + name_input_courant + '[]" class="input_upload_multiple_img_wpextend" value="' + attachments[i].id + '" /></li>');
				}
			}
		});

		frame.open();

		return false;
	});
	

	/**
	* Suppression d'une image dans un bloc gallery
	*
	*/
	link_remove_multiple_img.on('click', function( event ){

		link_remove_img_courant = jQuery(this);
		contner_list_image = link_remove_img_courant.parent().parent();
		name_input_courant = link_remove_img_courant.attr('data-name_input');
		
		link_remove_img_courant.parent().remove();

		if( contner_list_image.find('li').length == 0 ){
			contner_list_image.append('<li class="ui-state-default"><input type="hidden" name="' + name_input_courant + '" class="input_upload_multiple_img_wpextend" value="" /></li>');
		}

	});





	/**
	* Datepicker initialization
	*
	*/
	jQuery( ".input_daterange_from" ).each(function(){

		var current_date_picker_from = jQuery(this);
		var current_date_picker_to = jQuery(this).parent().find('.input_daterange_to');

		current_date_picker_from.datepicker({
			dateFormat: "mm/dd/yy"
		});
	    current_date_picker_to.datepicker({
			dateFormat: "mm/dd/yy"
		});
	});




	/**
	* Slider-range initialization
	*
	*/
	jQuery( ".div_sliderrange" ).each(function(){

		var sliderrange = jQuery(this);
		var sliderInput_from = jQuery(this).parent().find('.input_sliderrange_from');
		var sliderInput_to = jQuery(this).parent().find('.input_sliderrange_to');

		var attr_min = jQuery(this).attr('attr-min');
		var attr_max = jQuery(this).attr('attr-max');
		

		if( typeof(attr_min) == 'undefined' ){
			attr_min = 0;
		}

		if( typeof(attr_max) == 'undefined' ){
			attr_max = 24 * 60;
		}

		sliderrange.slider({
			range: true,
			min: 0,
			max: 24 * 60,
			step: 15,
			values: [ attr_min, attr_max ],
			slide: function( event, ui ) {

				var hours_min = Math.floor(ui.values[0] / 60);
            	var minutes_min = ui.values[0] - (hours_min * 60);

            	var hours_max = Math.floor(ui.values[1] / 60);
            	var minutes_max = ui.values[1] - (hours_max * 60);

	        	sliderInput_from.val( hours_min + ":" + minutes_min);
	        	sliderInput_to.val( hours_max + ":" + minutes_max );
	    	}
		});

		// sliderInput.val( attr_min + "-" + attr_max );
	});




});
