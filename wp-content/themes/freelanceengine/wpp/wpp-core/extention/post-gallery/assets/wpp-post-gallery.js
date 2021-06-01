jQuery(function ($) {
    // Product gallery file uploads.
    var $product_gallery_frame,
        $image_gallery_ids = $('#wpp_post_gallery'),
        $wpp_post_images = $('#wpp_images_container').find('ul.wpp_post_images');

    $('.add_wpp_post_images').on('click', 'a', function (event) {

        var $el = $(this);

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ($product_gallery_frame) {
            $product_gallery_frame.open();
            return;
        }

        // Create the media frame.
        $product_gallery_frame = wp.media.frames.product_gallery = wp.media({
            // Set the title of the modal.
            title: $el.data('choose'),
            button: {
                text: $el.data('update')
            },
            states: [
                new wp.media.controller.Library({
                    title: $el.data('choose'),
                    filterable: 'all',
                    multiple: true
                })
            ]
        });

        // When an image is selected, run a callback.
        $product_gallery_frame.on('select', function () {
            var selection = $product_gallery_frame.state().get('selection'),
                attachment_ids = $image_gallery_ids.val();

            selection.map(function (attachment) {
                attachment = attachment.toJSON();

                if (attachment.id) {
                    attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                    var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                    $wpp_post_images.append(
                        '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image +
                        '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' +
                        $el.data('text') + '</a></li></ul></li>'
                    );
                }
            });

            $image_gallery_ids.val(attachment_ids);
        });

        // Finally, open the modal.
        $product_gallery_frame.open();
    });

    // Image ordering.
    $wpp_post_images.sortable({
        items: 'li.image',
        cursor: 'move',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        forceHelperSize: false,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'wc-metabox-sortable-placeholder',
        start: function (event, ui) {
            ui.item.css('background-color', '#f6f6f6');
        },
        stop: function (event, ui) {
            ui.item.removeAttr('style');
        },
        update: function () {
            var attachment_ids = '';

            $('#wpp_images_container').find('ul li.image').css('cursor', 'default').each(function () {
                var attachment_id = $(this).attr('data-attachment_id');
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val(attachment_ids);
        }
    });

    // Remove images.
    $('#wpp_images_container').on('click', 'a.delete', function () {
        $(this).closest('li.image').remove();

        var attachment_ids = '';

        $('#wpp_images_container').find('ul li.image').css('cursor', 'default').each(function () {
            var attachment_id = $(this).attr('data-attachment_id');
            attachment_ids = attachment_ids + attachment_id + ',';
        });

        $image_gallery_ids.val(attachment_ids);

        // Remove any lingering tooltips.
        $('#tiptip_holder').removeAttr('style');
        $('#tiptip_arrow').removeAttr('style');

        return false;
    });
});