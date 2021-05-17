/**
 * plugin admin area javascript
 */
(function($){$(function () {

	// dismiss export template warnings
    $('.wpae-general-notice-dismiss').click(function(){

        var $parent = $(this).parent();
        var noticeId = $(this).attr('data-noticeId');

        var request = {
            action: 'dismiss_warnings',
            data: {
                notice_id: noticeId
            },
            security: wp_all_export_security
        };

        $parent.slideUp();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: request,
            success: function(response) {},
            dataType: "json"
        });
    });

});})(jQuery);
