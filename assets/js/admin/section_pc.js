jQuery( document ).ready(function() {

	if(jQuery(".postbox .sortable, .contner_listing_sections_sortable .sortable").length > 0){
		jQuery(".postbox .sortable, .contner_listing_sections_sortable .sortable").sortable({
			cancel: ".ui-state-disabled",
			update: function(event, ui){

				update_listing_sections_in_page(jQuery(this));
			}
		});
	}


	if( jQuery("body").hasClass("iframe") ){

		jQuery("#TB_window").width('90%');
		jQuery("#TB_window").find('iframe').width('90%');
	}

});



	/**
	* Mise à jour de la valeur du champs caché qui contient la liste des sections
	*/
	function update_listing_sections_in_page(elt_sortable){

		if(elt_sortable.next(".input_hidden_list_elt_sortable").length > 0){
			var tab_attr_id_sortable = elt_sortable.sortable("toArray", {attribute: 'attr_id_sortable'});
			var tab_attr_id_sortable_final = [];

			for(var i in tab_attr_id_sortable){
				if(tab_attr_id_sortable[i] != '')
					tab_attr_id_sortable_final.push(tab_attr_id_sortable[i]);
			}

			elt_sortable.next(".input_hidden_list_elt_sortable").val('['+tab_attr_id_sortable_final+']');
		}
		else if(elt_sortable.parents('ul').next(".input_hidden_list_elt_sortable").length > 0){
			var tab_attr_id_sortable = elt_sortable.sortable("toArray", {attribute: 'attr_id_sortable'});
			var tab_attr_id_sortable_final = [];

			for(var i in tab_attr_id_sortable){
				if(tab_attr_id_sortable[i] != '')
					tab_attr_id_sortable_final.push(tab_attr_id_sortable[i]);
			}

			elt_sortable.parents('ul').next(".input_hidden_list_elt_sortable").val('['+tab_attr_id_sortable_final+']');
		}
	}







	/**
	* Suppression d'un panneau
	*/
	function remove_elt_sortable(elt){

		var elt_sortable = jQuery(elt).parents('.sortable');
		jQuery(elt).parent('.ui-state-default').remove();
		update_listing_sections_in_page(elt_sortable);
	}
