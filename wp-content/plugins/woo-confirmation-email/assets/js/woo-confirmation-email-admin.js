jQuery(window).on('load',function () {
    xlwuev_verification_page();
    email_template_tab();
});

function xlwuev_verification_page() {
    jQuery(document).on('click', '.xlwuev_verification_page_radio', function () {
        var radio_value = jQuery(this).val();
        var element_to_add = jQuery(this).attr('data-element');
        var to_hide = jQuery(this).attr('data-add-class');
        var to_show = jQuery(this).attr('data-remove-class');
        if (to_show != '0') {
            jQuery('.' + element_to_add).removeClass(to_show);
        }
        if (to_hide != '0') {
            jQuery('.' + element_to_add).addClass(to_hide);
        }
    });
}

function email_template_tab() {
    jQuery(document).on('click', '.conditional_radio', function () {
        render_email_tab_settings(jQuery(this));
    });

    jQuery('.conditional_radio').each(function () {
        if (jQuery(this).is(':checked')) {
            render_email_tab_settings(jQuery(this));
        }
    });
}

function render_email_tab_settings($this) {
    var to_show = $this.attr('data-add');
    var to_hide = $this.attr('data-remove');
    var to_condition = $this.attr('data-condition');
    var to_condition_show = $this.attr('data-condition-show');
    if (to_show != '0') {
        to_show = JSON.parse(to_show);
        jQuery.each(to_show, function (idx, class_name) {
            jQuery('.' + class_name).show();
        });
    }
    if (to_hide != '0') {
        to_hide = JSON.parse(to_hide);
        jQuery.each(to_hide, function (idx, class_name) {
            jQuery('.' + class_name).hide();
        });
    }
    if (typeof to_condition != 'undefined') {
        to_condition = JSON.parse(to_condition);
        jQuery.each(to_condition, function (radio_name, radio_value) {
            jQuery('input[name=' + radio_name + ']').prop('checked',true);
            jQuery('input[name=' + radio_name + '][value="1"]').trigger('click');
            var selected_radio_value = jQuery('input[name=' + radio_name + ']:checked').val();
            if (radio_value == selected_radio_value) {
                to_condition_show = JSON.parse(to_condition_show);
                jQuery.each(to_condition_show, function (idx, class_name) {
                    jQuery('.' + class_name).show();
                });
            }
        });
    }
}