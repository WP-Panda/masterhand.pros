jQuery(document).ready(function ($) {
    'use strict';

    var $licenseBtn = $('#activate-license');
    var $licenseForm = $('[data-license-form]');
    var $licenseInput = $licenseForm.find('input[type="text"]');

    $licenseBtn.on( 'click', function(e) {

        e.preventDefault();

        var licenseNumber = $licenseInput.val().trim();

        if ( licenseNumber ) {

            var sendData = {
                action: 'wpunit-sti-ajax-actions',
                type: 'verify-license',
                license: licenseNumber
            };

            var self = $(this);
            var isActive = self.data('is-active');

            console.log(isActive);

            if ( isActive && isActive === 'active' ) {
                sendData.type = 'deactivate-license';
            }

            $licenseInput.attr('disabled','disabled');
            $licenseBtn.attr('disabled','disabled');
            $licenseForm.addClass('sti-processing');
            $licenseForm.removeClass('valid');
            $licenseForm.removeClass('invalid');

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: sendData,
                dataType: "json",
                success: function (response) {

                    $licenseBtn.removeAttr('disabled');
                    $licenseForm.removeClass('sti-processing');

                    if ( response.data === 'valid' ) {

                        $licenseForm.addClass('valid');
                        self.data('is-active', 'active');
                        self.text('Deactivate License');

                    } else if( response.data === 'invalid' ) {

                        $licenseInput.removeAttr('disabled');
                        $licenseForm.addClass('invalid');
                        self.data('is-active', 'inactive');
                        self.text('Activate License');

                    } else if( response.data === 'deactivated' ) {

                        $licenseInput.removeAttr('disabled');
                        self.data('is-active', 'inactive');
                        self.text('Activate License');

                    }

                }
            });

        } else {
            alert('License field is empty.');
        }

    });

});