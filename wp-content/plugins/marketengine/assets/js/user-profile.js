(function($) {
    $(document).ready(function() {
        var select = $('#display_name');
        if (select.length) {
            $('#first_name, #last_name, #nickname').bind('blur.user_profile', function() {
                var dub = [],
                    inputs = {
                        display_username: $('#user_login').val() || '',
                        display_firstname: $('#first_name').val() || '',
                        display_lastname: $('#last_name').val() || ''
                    };
                if (inputs.display_firstname && inputs.display_lastname) {
                    inputs.display_firstlast = inputs.display_firstname + ' ' + inputs.display_lastname;
                    inputs.display_lastfirst = inputs.display_lastname + ' ' + inputs.display_firstname;
                }
                $.each($('option', select), function(i, el) {
                    dub.push(el.value);
                });
                $.each(inputs, function(id, value) {
                    if (!value) {
                        return;
                    }
                    var val = value.replace(/<\/?[a-z][^>]*>/gi, '');
                    if (inputs[id].length && $.inArray(val, dub) === -1) {
                        dub.push(val);
                        $('<option />', {
                            'text': val
                        }).appendTo(select);
                    }
                });
            });
        }
        var flag = false;
        $('#resend-confirmation-email').on('click', function(e) {
            if (flag) {
                e.preventDefault();
            }
            flag = true;
        });
        $('#upload_user_avatar').MaketEngineUploader({
            browse_button: 'change-avatar-user',
            multi: false,
            removable: false,
            name: 'user_avatar',
            extension: 'jpg,jpeg,gif,png',
            upload_url: me_globals.ajaxurl + '?nonce=' + $('#me-user-avatar').val(),
            maxsize: '2mb',
            maxcount: 1,
        });
    });
})(jQuery);