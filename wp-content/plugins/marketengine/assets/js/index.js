/* global me_globals.ajaxurl, wpAjax*/
(function($) {
    $('.me-list-thumbs').meSliderThumbs();
    var magnificInstance = true;
    var magnificItem = 0;
    $('.me-large-fancybox').on('click', function(event) {
        magnificInstance = true;
        $('.me-fancybox').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            },
            disableOn: function() {
                return magnificInstance;
            }
        }).magnificPopup('open', magnificItem);
    });
    $('.me-fancybox').on('click', function(ev) {
        ev.preventDefault();
        var target = ev.currentTarget;
        var medium_img = $(target).attr('medium-img');
        $('.me-large-fancybox').find('img').attr('src', medium_img);
        magnificItem = $(target).parent('li').index();
        magnificInstance = false;
    });
    var min = parseInt($('#me-range-price').attr('min'));
    var max = parseInt($('#me-range-price').attr('max'));
    var range_price = $('#me-range-price').slider({
        range: true,
        min: min,
        max: max,
        values: [$('input.me-range-min').val(), $('input.me-range-max').val()],
        slide: function(event, ui) {
            $('input.me-range-min').val(ui.values[0]);
            $('input.me-range-max').val(ui.values[1]);
        }
    });
    $('input.me-range-min').val(range_price.slider('values', 0));
    $('input.me-range-max').val(range_price.slider('values', 1));
    $('#listing-orderby').on('change', function() {
        $(this).closest('form').submit();
    });
    $('.do-rating').raty({
        // half: true,
        readOnly: false,
    });
    $('.result-rating').raty({
        half: true,
        readOnly: true,
        score: function() {
            return $(this).attr('data-score');
        }
    });
    // init message box
    $('.inquiry-message-wrapper').MEmessage({
        paged: 2,
        nonce: $('#_msg_wpnonce').val(),
        upload_file_nonce: $('#_msg_file_nonce').val(),
        parent: $('input[name="inquiry_id"]').val(),
        listing: $('input[name="inquiry_listing"]').val(),
        type: 'inquiry'
    });
    $(document).on('click', '.me-message-fancybox', function(e) {
        

        $(this).magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        })
        e.preventDefault();
    });
    $('form#send-inquiry textarea').keydown(function(e) {
        // enter send message
        if (e.keyCode == '13' && !e.shiftKey) {
            $('#send-inquiry').submit();
        }
    });
    $('.me-listing-info-view').on('click', function() {
        $('.me-listing-desc').toggleClass('active');
    });
})(jQuery);