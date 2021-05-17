(function($) {
	$(document).ready(function() {
		$('#me-cf-list-sortable').sortable({
			items: '> .me-cf-item',
			update : function () {
			    var order = $('#me-cf-list-sortable').sortable('serialize');
			    var data = {
			    	order: order,
			    	action: 'marketengine_cf_sort',
			    	category_id: $('#current-category').val(),
			    }

			    $.post( me_globals.ajaxurl, data, function(res) {
			    	console.log(res);
			    });
			}
		});
		$('#me-cf-list-sortable').disableSelection();
	});
})(jQuery)