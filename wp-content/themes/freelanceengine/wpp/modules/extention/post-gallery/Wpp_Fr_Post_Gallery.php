<?php
/**
 * @package    WPP_Core
 * @subpackage Gallery Images
 * @author     WP_Panda
 * @version    2.0.0
 *
 *  Галерея изображений для записей
 */

class Wpp_Fr_Post_Gallery {

	private static $ver = '2.0.0';


	public static function init() {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		add_action( 'add_meta_boxes', [ __CLASS__, 'metaboxes' ] );
		add_action( 'save_post', [ __CLASS__, 'save' ], 1, 2 );
	}


	public static function metaboxes() {

		$screens = apply_filters( 'wpp_post_gallery_types', [] );

		if ( empty( $screens ) || ! is_array( $screens ) ) {
			return false;
		}

		foreach ( $screens as $screen ) {
			add_meta_box( 'post-gallery', __( 'Gallery', WPP_TEXT_DOMAIN ), [
				__CLASS__,
				'output'
			], $screen, 'side', 'low' );
		}

	}

	/**
	 * Ресурсы
	 */
	public static function assets() {

		$url = str_replace( wp_normalize_path( ABSPATH ), home_url( '/' ), wp_normalize_path( __DIR__ ) );

		wp_register_script( 'wpp-post-gallery', $url . '/assets/wpp-post-gallery.js', [], self::$ver, true );
		wp_register_style( 'wpp-post-gallery', $url . '/assets/wpp-post-gallery.css', [], self::$ver, 'all' );
	}

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		global $thepostid;

		$thepostid = $post->ID;
		wp_nonce_field( 'wpp_fr_save_data', 'wpp_fr_meta_nonce' );
		wp_enqueue_style( 'wpp-post-gallery' );
		wp_enqueue_script( 'wpp-post-gallery' );
		?>

        <div id="wpp_images_container">
            <ul class="wpp_post_images">
				<?php
				$attachments         = explode( ',', get_post_meta( $thepostid, '_wpp_post_gallery', true ) );
				$update_meta         = false;
				$updated_gallery_ids = [];

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
						printf( $html, esc_attr( $attachment_id ), $attachment, __( 'Delete Image', WPP_TEXT_DOMAIN ), __( 'Delete', WPP_TEXT_DOMAIN ) );

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
            <a href="javascript:void(0);"
               data-choose="<?php esc_attr_e( 'Add an image to Gallery', 'wpp-fr' ); ?>"
               data-update="<?php esc_attr_e( 'Add to Gallery', 'wpp-fr' ); ?>"
               data-delete="<?php esc_attr_e( 'Delete Image', 'wpp-fr' ); ?>"
               data-text="<?php esc_attr_e( 'Delete', 'wpp-fr' ); ?>">
				<?php esc_html_e( 'Add an image to Gallery', 'wpp-fr' ); ?>
            </a>
        </p>

		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {

		$screens = apply_filters( 'wpp_post_gallery_types', [] );

		if ( empty( $screens ) || ! is_array( $screens ) ) {
			return false;
		}

		if ( ! in_array( $post->post_type, $screens ) ) {
			return false;
		}
		//if you get here then it's your post type so do your thing....

		$gallery_attachment_ids = isset( $_POST['wpp_post_gallery'] ) ? array_filter( explode( ',', wpp_clean( $_POST['wpp_post_gallery'] ) ) ) : [];
		update_post_meta( $post_id, '_wpp_post_gallery', ( ! empty( $gallery_attachment_ids ) ) ? implode( ',', $gallery_attachment_ids ) : '' );
	}
}

Wpp_Fr_Post_Gallery::init();