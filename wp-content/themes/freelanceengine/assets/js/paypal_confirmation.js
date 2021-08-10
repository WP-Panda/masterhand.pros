jQuery(function ($) {
    /**
     * ТУТ ВНИМАТЕЛЬНО ПОСМОТРЕТЬ
     */
    $(document).ready(function () {
        $(document).on('click', '.confrim_paypal_account', function ($) {
            var data = {
                action: 'confirm_paypal_account'
            };

            jQuery.post("/wp-admin/admin-ajax.php", data, function (response) {
                if (response.success)
                    window.location.href = response.msg
                else
                    AE.pubsub.trigger('ae:notification', {
                        msg: response.msg,
                        notice_type: 'error',
                    })
            });
        });
    })

});