(function($) {
    $(document).ready(function() {
        if (window.location.hash) {
            if ($(window.location.hash).length > 0) {
                var step = $(window.location.hash).attr('data-step');
                $('.me-setup-container').removeClass('active');
                $('.me-setup-line-step').removeClass('active');
                $(window.location.hash).addClass('active');
                for (var i = 0; i <= parseInt(step); i++) {
                    $('.me-setup-line-step').eq(i).addClass('active');
                };
            }
        }
        $('.wizard-start').on('click', function(event) {
            //event.preventDefault();
            var target = event.currentTarget;
            var parent_section = $(target).parents('.me-setup-section');
            var parent_container = $(target).parents('.me-setup-container');
            var data_step = parent_container.data('step');
            var data_next = data_step + 1;
            // activity
            parent_container.removeClass('active');
            $('.me-setup-container').eq(data_next).addClass('active');
            $('.me-setup-line-step').eq(data_next).addClass('active');
        });
        //=== Click continue button
        $('.me-next').on('click', function(event) {
            //event.preventDefault();
            var $target = $(event.currentTarget);
            var $parent_section = $target.parents('.me-setup-section');
            var $parent_container = $target.parents('.me-setup-container');
            var data_step = $parent_container.data('step');
            var data_next = data_step + 1;
            // activity
            $.ajax({
                type: 'post',
                url: me_globals.ajaxurl,
                data: {
                    action: 'me-do-setup',
                    _wpnonce: $('#_wpnonce').val(),
                    step: $parent_container.find('input[name="step"]').val(),
                    content: $parent_container.find('form').serialize()
                },
                beforeSend: function() {
                    $parent_section.addClass('me-setup-section-loading');
                },
                success: function(res, xhr) {
                    $parent_section.removeClass('me-setup-section-loading');
                    $parent_container.removeClass('active');
                    if (res.success && res.step == 'payment') {
                        $('select[name="contact_available[]"]').html(res.data.contact_option);
                        $('select[name="purchasion_available[]"]').html(res.data.purchase_option);
                    }
                    $('.me-setup-container').eq(data_next).addClass('active');
                    $('.me-setup-line-step').eq(data_next).addClass('active');
                }
            });
        });
        $('#me-add-sample-data').on('click', function(event) {
            var $target = $(event.currentTarget);
            var $parent_section = $target.parents('.me-setup-section');
            var $parent_container = $target.parents('.me-setup-container');
            var count = 1;
            for (var i = 1; i <= 12; i++) {
                $.ajax({
                    type: 'post',
                    url: me_globals.ajaxurl,
                    data: {
                        action: 'me-add-sample-data',
                        number: i,
                        _wpnonce: $('#_wpnonce').val()
                    },
                    beforeSend: function() {
                        $parent_section.addClass('me-setup-section-loading');
                    },
                    success: function(res, xhr) {
                        count++;
                        if (count == i) {
                            $parent_section.removeClass('me-setup-section-loading');
                            $target.parents('.me-setup-wrap').addClass('active');
                        }
                    }
                });
            };
            setTimeout(function() {
                $parent_section.removeClass('me-setup-section-loading');
                $target.parents('.me-setup-wrap').addClass('active');
            }, 45000);
        });
        // remove sample data
        $('#me-remove-sample-data').on('click', function(event) {
            var $target = $(event.currentTarget);
            var $parent_section = $target.parents('.me-setup-section');
            var $parent_container = $target.parents('.me-setup-container');
            var count = 1;
            $.ajax({
                type: 'post',
                url: me_globals.ajaxurl,
                data: {
                    action: 'me-remove-sample-data',
                    _wpnonce: $('#_wpnonce').val()
                },
                beforeSend: function() {
                    $parent_section.addClass('me-setup-section-loading');
                },
                success: function(res, xhr) {
                    $parent_section.removeClass('me-setup-section-loading');
                    $target.parents('.me-setup-wrap').removeClass('active');
                }
            });
        });
        $('.me-smail-submit-btn').click(function(event) {
            var $target = $(event.currentTarget);
            $target.parents('.me-setup-wrap').addClass('active');
        });
        //=== Click skip button
        $('.me-skip-btn').on('click', function(event) {
            //event.preventDefault();
            var target = event.currentTarget;
            var parent_section = $(target).parents('.me-setup-section');
            var parent_container = $(target).parents('.me-setup-container');
            var data_step = parent_container.data('step');
            var data_next = data_step + 1;
            // activity
            parent_container.removeClass('active');
            $('.me-setup-container').eq(data_next).addClass('active');
            $('.me-setup-line-step').eq(data_next).addClass('active');
        });
        $('.me-setup-add-cat').on('click', function(event) {
            var parent_sfield = $(this).parent();
            $(parent_sfield).find('.more-cat').slideDown();
            $(this).hide();
        });
        $(".me-input-price").keydown(function(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: Ctrl+C
                (e.keyCode == 67 && e.ctrlKey === true) ||
                // Allow: Ctrl+X
                (e.keyCode == 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        $(".me-input-price").blur(function(event) {
            var $target = $(event.currentTarget);
            if (!$target.val()) {
                $target.val('0');
            }
        });
    });
})(jQuery);