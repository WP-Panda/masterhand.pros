/**
 * Toggle Content Editor/Page Builder
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2016, Genbu Media
 */
jQuery(document).ready(function ($) {

    /* Editor Toggle Function */
    function fxPb_Editor_Toggle() {
        if ($('#fx-page-builder').lenght)
            $('#postdivrich').hide();
        $('#fx-page-builder').show();
    }

    /* Toggle On Page Load */
    fxPb_Editor_Toggle();

    /* If user change page template drop down */
    $("#page_template").change(function (e) {
        fxPb_Editor_Toggle();
    });

});