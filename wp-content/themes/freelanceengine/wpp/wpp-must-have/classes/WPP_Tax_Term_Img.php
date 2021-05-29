<?php
	/**
	 * @package teplo-centr.tw1.rus
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;


	class WPP_Tax_Term_Img{

		/*
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		*/
		public static function init() {

			add_action( 'init', [ __CLASS__, 'actions' ], 10, 1 );
		}

		public static function load_media() {
			wp_enqueue_media();
		}

		public static function actions() {
			$taxnames = apply_filters( 'wpp_tax_imgs_targets', [] );

			if ( ! empty( $taxnames ) ) :
				foreach ( $taxnames as $taxname ) :

					$name = is_array( $taxname ) ? $taxname[ 0 ] : $taxname;

					add_action( "{$name}_add_form_fields", [ __CLASS__, 'add_category_image' ], 10, 3 );
					add_action( "created_{$name}", [ __CLASS__, 'save_category_image' ], 10, 3 );
					add_action( "{$name}_edit_form_fields", [ __CLASS__, 'update_category_image' ], 10, 3 );
					add_action( "edited_{$name}", [ __CLASS__, 'save_category_image' ], 10, 3 );

					// Add columns.
					add_filter( "manage_edit-{$name}_columns", [ __CLASS__, 'wpp_cat_columns' ] );
					add_filter( "manage_{$name}_custom_column", [ __CLASS__, 'wpp_cat_column' ], 10, 3 );


					add_action( 'admin_enqueue_scripts', [ __CLASS__, 'load_media' ] );
					add_action( 'admin_footer', [ __CLASS__, 'add_script' ] );

				endforeach;
			endif;
		}

		/**
		 * Cхранение поля
		 *
		 * @param mixed  $term_id  Term ID being saved.
		 * @param mixed  $tt_id    Term taxonomy ID.
		 * @param string $taxonomy Taxonomy slug.
		 */
		public static function save_category_image( $term_id, $tt_id = '', $taxonomy = '' ) {
			if ( isset( $_POST[ 'wpp_thumbnail_id' ] ) && '' !== $_POST[ 'wpp_thumbnail_id' ] ) {
				update_term_meta( $term_id, 'wpp_thumbnail_id', absint( $_POST[ 'wpp_thumbnail_id' ] ) );
			}
		}

		/**
		 * Category thumbnail fields.
		 */
		public static function add_category_image() {
			?>
            <div class="form-field term-group">
                <label for="wpp_thumbnail_id"><?php _e( 'Image', 'hero-theme' ); ?></label>
                <input type="hidden" id="wpp_thumbnail_id" name="wpp_thumbnail_id" class="wpp_thumbnail_id" value="">
                <div id="category-image-wrapper"></div>
                <p>
                    <input type="button" class="button button-secondary wpp_tax_media_button" id="wpp_tax_media_button"
                           name="wpp_tax_media_button" value="<?php _e( 'Добавить изображение' ); ?>"/>
                    <input type="button" class="button button-secondary wpp_tax_media_remove" id="wpp_tax_media_remove"
                           name="wpp_tax_media_remove" value="<?php _e( 'Удалить изображение' ); ?>"/>
                </p>
            </div>
			<?php
		}


		/**
		 * Edit category thumbnail field.
		 *
		 * @param mixed $term Term (category) being edited.
		 */
		public static function update_category_image( $term, $taxonomy ) { ?>
            <tr class="form-field term-group-wrap">
                <th scope="row">
                    <label for="wpp_thumbnail_id"><?php _e( 'Image', 'hero-theme' ); ?></label>
                </th>
                <td>
					<?php $image_id = get_term_meta( $term->term_id, 'wpp_thumbnail_id', true ); ?>
                    <input type="hidden" id="wpp_thumbnail_id" name="wpp_thumbnail_id" value="<?php echo $image_id; ?>">
                    <div id="category-image-wrapper">
						<?php if ( $image_id ) { ?>
							<?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
						<?php } ?>
                    </div>
                    <p>
                        <input type="button" class="button button-secondary wpp_tax_media_button"
                               id="wpp_tax_media_button" name="wpp_tax_media_button"
                               value="<?php _e( 'Добавить изображение' ); ?>"/>
                        <input type="button" class="button button-secondary wpp_tax_media_remove"
                               id="wpp_tax_media_remove" name="wpp_tax_media_remove"
                               value="<?php _e( 'Удалить изображение' ); ?>"/>
                    </p>
                </td>
            </tr>
			<?php
		}


		/**
		 * Thumbnail column added to category admin.
		 *
		 * @param mixed $columns Columns array.
		 *
		 * @return array
		 */
		public static function wpp_cat_columns( $columns ) {
			$new_columns = [];

			if ( isset( $columns[ 'cb' ] ) ) {
				$new_columns[ 'cb' ] = $columns[ 'cb' ];
				unset( $columns[ 'cb' ] );
			}

			$new_columns[ 'wpp_thumb' ] = __( 'Image', 'woocommerce' );

			$columns             = array_merge( $new_columns, $columns );
			$columns[ 'handle' ] = '';

			return $columns;
		}

		/**
		 * Thumbnail column value added to category admin.
		 *
		 * @param string $columns Column HTML output.
		 * @param string $column  Column name.
		 * @param int    $id      Product ID.
		 *
		 * @return string
		 */
		public static function wpp_cat_column( $columns, $column, $id ) {
			if ( 'wpp_thumb' === $column ) {

				$thumbnail_id = get_term_meta( $id, 'wpp_thumbnail_id', true );

				if ( $thumbnail_id ) {
					$image = wp_get_attachment_thumb_url( $thumbnail_id );
				} else {
					$image = wc_placeholder_img_src();
				}

				$image   = str_replace( ' ', '%20', $image );
				$columns .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Миниатюра' ) . '" class="wp-post-image" height="48" width="48" />';
			}
			if ( 'handle' === $column ) {
				$columns .= '<input type="hidden" name="term_id" value="' . esc_attr( $id ) . '" />';
			}

			return $columns;
		}

		/*
		 * Add script
		 * @since 1.0.0
		 */
		public static function add_script() { ?>
            <script>
                jQuery(document).ready(function ($) {
                    function wpp_media_upload(button_class) {
                        var _custom_media = true,
                            _orig_send_attachment = wp.media.editor.send.attachment;
                        $('body').on('click', button_class, function (e) {
                            var button_id = '#' + $(this).attr('id');
                            var send_attachment_bkp = wp.media.editor.send.attachment;
                            var button = $(button_id);
                            _custom_media = true;
                            wp.media.editor.send.attachment = function (props, attachment) {
                                if (_custom_media) {
                                    $('#wpp_thumbnail_id').val(attachment.id);
                                    $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                                    $('#category-image-wrapper .custom_media_image').attr('src', attachment.url).css('display', 'block');
                                } else {
                                    return _orig_send_attachment.apply(button_id, [props, attachment]);
                                }
                            }
                            wp.media.editor.open(button);
                            return false;
                        });
                    }

                    wpp_media_upload('.wpp_tax_media_button.button');
                    $('body').on('click', '.wpp_tax_media_remove', function () {
                        $('#wpp_thumbnail_id').val('');
                        $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                    });
                    // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
                    $(document).ajaxComplete(function (event, xhr, settings) {
                        var queryStringArr = settings.data.split('&');
                        if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                            var xml = xhr.responseXML;
                            $response = $(xml).find('term_id').text();
                            if ($response != "") {
                                // Clear the thumb image
                                $('#category-image-wrapper').html('');
                            }
                        }
                    });
                });
            </script>
		<?php }
	}

	WPP_Tax_Term_Img::init();


	function wpp_term_img( $id ) {
		$thumbnail_id = get_term_meta( $id, 'wpp_thumbnail_id', true );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = wc_placeholder_img_src();
		}

		$image = str_replace( ' ', '%20', $image );

		echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Миниатюра' ) . '" width="100%" />';
	}