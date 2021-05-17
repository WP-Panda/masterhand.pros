/**
 * plugin admin area javascript
 */
(function($){$(function () {
	
	if ( ! $('body.wpallimport-plugin').length) return; // do not execute any code if we are not on plugin page
	
	// [ WC Customers View ]
	// swither show/hide logic
	$('select.switcher').live('change', function (e) {	

		var $targets = $('.switcher-target-' + $(this).attr('id'));

		var is_show = $(this).val() == 'xpath'; if ($(this).is('.switcher-reversed')) is_show = ! is_show;
		if (is_show) {
			$targets.slideDown();
		} else {
			$targets.slideUp().find('.clear-on-switch').add($targets.filter('.clear-on-switch')).val('');
		}

	}).change();

	$('.existing_umeta_keys').change(function(){

		var parent_fieldset = $(this).parents('fieldset').first();
		var key = $(this).find('option:selected').val();
		
		if ("" != $(this).val()) {

			parent_fieldset.find('input[name^=custom_name]:visible').each(function(){
				if ("" == $(this).val()) $(this).parents('tr').first().remove();
			});
			parent_fieldset.find('a.add-new-custom').click();
			parent_fieldset.find('input[name^=custom_name]:visible').last().val($(this).val());

			$(this).prop('selectedIndex',0);	

			parent_fieldset.addClass('loading');		

			var request = {
				action:'get_umeta_values',		
				security: wp_all_import_security,		
				key: key
		    };
		    
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: request,
				success: function(response) {
					parent_fieldset.find('input[name^=custom_name]:visible:last').after(response.html);
					parent_fieldset.removeClass('loading');			
				},
				dataType: "json"
			});
					
		}

	});

	$('.existing_umeta_values').live('change', function(){
		var parent_fieldset = $(this).parents('.form-field:first');
		if ($(this).val() != ""){
			parent_fieldset.find('textarea').val($(this).val());
			$(this).prop('selectedIndex', 0);
		}
	});

});})(jQuery);
