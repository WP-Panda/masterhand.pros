jQuery(function ($) {
    $('.user_login a').on('click', function (e) {
        e.preventDefault();

        $(this).closest('.user_login').find('.referrals').show();
        $(this).hide();
    });

    $('.referrals .cancel').on("click", function (e) {
        e.preventDefault();

        $(this).closest('.referrals').hide();
        $(this).closest('.user_login').find('a').css('display', 'initial');
    });
});
