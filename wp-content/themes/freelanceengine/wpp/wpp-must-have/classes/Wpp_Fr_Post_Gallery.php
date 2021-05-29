<?php
/**
 * Created by PhpStorm.
 * User: WP_Panda
 * Date: 26.06.2020
 * Time: 11:55
 */

class Wpp_Fr_Post_Gallery {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		global $thepostid;

		$thepostid = $post->ID;
		wp_nonce_field( 'wpp_fr_save_data', 'wpp_fr_meta_nonce' );
		?>
        <style>

            #wpp_images_container ul li.image {
                width: 75px;
                height: 75px;
                float: left;
                cursor: move;
                border: 1px solid #d5d5d5;
                margin: 9px 9px 0 0;
                background: #f7f7f7;
                border-radius: 2px;
                position: relative;
                box-sizing: border-box;
            }


            #wpp_images_container ul li:hover ul.actions {
                display: block;
            }

            #wpp_images_container ul ul.actions {
                position: absolute;
                top: -8px;
                right: -8px;
                padding: 2px;
                display: none;
            }

            #wpp_images_container ul ul.actions li a.delete {
                display: block;
                text-indent: -9999px;
                position: relative;
                height: 1em;
                width: 1em;
                font-size: 1.4em;
            }

            #wpp_images_container ul ul.actions li a.delete:hover::before {
                color: #a00;
            }

            #wpp_images_container ul ul.actions li a.delete::before {
                font-family: Dashicons;
                speak: none;
                font-weight: 400;
                font-variant: normal;
                text-transform: none;
                line-height: 1;
                -webkit-font-smoothing: antialiased;
                margin: 0;
                text-indent: 0;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                text-align: center;
                content: "";
                color: #999;
                background: #fff;
                border-radius: 50%;
                height: 1em;
                width: 1em;
                line-height: 1em;
            }

            #wpp_images_container ul.wpp_post_images.ui-sortable {
                overflow: hidden;
                float: left;
                padding: 10px 0;
            }

        </style>
        <div id="wpp_images_container">
            <ul class="wpp_post_images">
				<?php
				$attachments         = explode( ',', get_post_meta( $thepostid, '_wpp_post_gallery', true ) );
				$update_meta         = false;
				$updated_gallery_ids = array();

				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment_id ) {
						$attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

						if ( empty( $attachment ) ) {
							$update_meta = true;
							continue;
						}

						$html = <<<HTML
                            <li class="image" data-attachment_id="%s">
                                %s
                                <ul class="actions">
                                    <li><a href="javascript:void(0);" class="delete tips" data-tip="%s">%s</a></li>
                                </ul>
                            </li>
HTML;
						printf( $html, esc_attr( $attachment_id ), $attachment, __( 'Удалить картинку', 'wpp_fr' ), __( 'Удалить', 'wpp_fr' ) );

						$updated_gallery_ids[] = $attachment_id;
					}

					// need to update product meta to set new gallery ids
					if ( $update_meta ) {
						update_post_meta( $post->ID, '_wpp_post_gallery', implode( ',', $updated_gallery_ids ) );
					}
				}
				?>
            </ul>

            <input type="hidden" id="wpp_post_gallery" name="wpp_post_gallery"
                   value="<?php echo esc_attr( implode( ',', $updated_gallery_ids ) ); ?>"/>

        </div>
        <p class="add_wpp_post_images hide-if-no-js">
            <a href="javascript:void(0);" data-choose="<?php esc_attr_e( 'Добавить картинку в Галлерею', 'wpp-fr' ); ?>"
               data-update="<?php esc_attr_e( 'Добавить в Галлерею', 'wpp-fr' ); ?>"
               data-delete="<?php esc_attr_e( 'Удалить Изображение', 'wpp-fr' ); ?>"
               data-text="<?php esc_attr_e( 'Удалить', 'wpp-fr' ); ?>">
				<?php esc_html_e( 'Добаить Изображение в Галлерею', 'wpp-fr' ); ?>
            </a>
        </p>
        <script>
            jQuery(function ($) {
                // Product gallery file uploads.
                var product_gallery_frame;
                var $image_gallery_ids = $('#wpp_post_gallery');
                var $wpp_post_images = $('#wpp_images_container').find('ul.wpp_post_images');

                $('.add_wpp_post_images').on('click', 'a', function (event) {
                    var $el = $(this);

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if (product_gallery_frame) {
                        product_gallery_frame.open();
                        return;
                    }

                    // Create the media frame.
                    product_gallery_frame = wp.media.frames.product_gallery = wp.media({
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
                    product_gallery_frame.on('select', function () {
                        var selection = product_gallery_frame.state().get('selection');
                        var attachment_ids = $image_gallery_ids.val();

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
                    product_gallery_frame.open();
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
        </script>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		$gallery_attachment_ids = isset( $_POST['wpp_post_gallery'] ) ? array_filter( explode( ',', wc_clean( $_POST['wpp_post_gallery'] ) ) ) : array();
		update_post_meta( $post_id, '_wpp_post_gallery', ( ! empty( $gallery_attachment_ids ) ) ? implode( ',', $gallery_attachment_ids ) : '' );
	}
}

add_action( 'save_post', 'Wpp_Fr_Post_Gallery::save', 1, 2 );