jQuery(document).ready(function($) {
    $('.me-switch-tab-filter-1, .me-switch-tab-filter-2').on('click', function() {
        $('.me-resolution').toggleClass('me-rslt-filter');
    });
    $('.me-dispute-case-tabs').on('click', function() {
        $(this).toggleClass('active');
        $('body').toggleClass('me-dispute-case-tabs-active');
        return false;
    });
    $('.me-dispute-action-tabs').on('click', function() {
        $(this).toggleClass('active');
        $('body').toggleClass('me-dispute-action-tabs-active');
        return false;
    });
    $('.me-dispute-related-tabs').on('click', function() {
        $(this).toggleClass('active');
        $('body').toggleClass('me-dispute-related-tabs-active');
        return false;
    });
    $('.me-receive-item-field').on('change', function(event) {
        var get_refund_block_id = $(this).data('get-refund-block');
        $('#dispute-get-refund-yes').removeClass('active');
        $('#dispute-get-refund-no').removeClass('active');
        $(document.getElementById(get_refund_block_id)).addClass('active');
    });
    $('#dispute-file').MaketEngineUploader({
        browse_button: 'me-dipute-upload',
        multi: true,
        name: 'dispute_file',
        extension: 'psd,jpg,jpeg,gif,png,pdf,doc,docx,xlsx,xls,zip',
        upload_url: me_globals.ajaxurl + '?nonce=' + $('#me-dispute-file').val(),
        maxsize: '2mb',
        maxcount: 5,
        file : 1
    });
    /*submit dispute message form*/
    $('#dispute-message-form').submit(function(e) {
        e.preventDefault();
        var content = $('#debate_content').val();
        if (!content) {
            $('#debate_content').focus();
            return false;
        }
        if ($('.upload-container').hasClass('uploading')) {
            return false;
        }
        /* ajax send debate message in dispute details */
        $.get({
            url: me_globals.ajaxurl,
            data: $(this).serialize() + '&action=me-dispute-debate',
            beforeSend: function() {
                // loading
            },
            success: function(res) {
                // remove loading
                if (res.success) {
                    $('.me-contact-messages-list').append(res.html);
                    $('#messages-container').scrollTop($('#messages-container')[0].scrollHeight);
                    $('#debate_content').val('').focus();
                    $('.upload_preview_container ul').html('');
                }
            }
        });
    });
    // init message box
    $('.dispute-message-wrapper').MEmessage({
        paged: 2,
        nonce: $('#_wpnonce').val(),
        parent: $('input[name="dispute"]').val(),
        type: 'dispute'
    });

    var scrollto = 0,
        scroll_el = 0;
    $('.me-dispute-event a').click(function(e) {
        e.preventDefault();
        scrollto = $(this).attr('href');
        scroll_el = $(this);
        var $container = $('#messages-container');
        if ($(scrollto).length != 0) {
            $container.scrollTop($container.scrollTop() + $(scrollto).position().top - 100);
            scrollto = 0;
            scroll_el = 0;
        } else {
            $container.scrollTop(0);
        }
    });
    
    $('#messages-container').on('scroll', function() {
        if ( scrollto != 0 && $('#messages-container').scrollTop() >= 50) {
            scroll_el.click();
        }
    });
});