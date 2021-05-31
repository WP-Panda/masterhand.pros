<?php
/**
 * Get WordPress Attachment Image Attributes.
 *
 * Similiar to running WordPress's wp_get_attachment_image() but
 * instead of getting back rendered HTML, we get back an array
 * where the key is the attribute name and the value is the attribute value.
 *
 * This is done by using the `wp_get_attachment_image_attributes` filter.
 *
 * e.g.
 *
 * [
 *     'width' => '1920'
 *     'height' => '1080'
 *     'src' => 'https://example.test/wp-content/uploads/2018/08/sal-1920x1080.jpg',
 *     'srcset' => 'https://example.test/wp-content/uploads/2018/08/sal-1920x1080.jpg 1920,
 *     https://example.test/wp-content/uploads/2018/08/sal-960x540.jpg 960w',
 *     'alt' => 'Photograph of Sal.',
 *     'sizes' => '(max-width: 1920px) 100vw, 1920px'
 *     'class' => 'attachment-fe-hero size-fe-hero'
 * ]
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class: WPP_Get_IMG
 */
class WPP_Get_IMG {
	const FILTER_PRIORITY = 9999999;
	/**
	 * Image Attributes.
	 *
	 * Key/value array.
	 *
	 * @var string[]
	 */
	protected $attributes = [];
	/**
	 * The attachment_id for the image.
	 *
	 * @var int
	 */
	protected $attachment_id;
	/**
	 * WordPress Image Size.
	 *
	 * Either a WordPress image size string or an array of [ $width, $height ].
	 *
	 * @var string | array
	 */
	protected $size;
	/**
	 * The image should be treated as an icon.
	 *
	 * @var bool
	 */
	protected $icon = false;
	/**
	 * Attributes to be applied to the image.
	 *
	 * @var string | array
	 */
	protected $attr = '';

	public function __construct( $attachment_id, $size = 'thumbnail', $icon = false, $attr = '' ) {
		$this->attachment_id = (int) $attachment_id;
		$this->size          = $size;
		$this->icon          = $icon;
		$this->attr          = $attr;
	}

	public function get_attributes() {
		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'capture_attributes' ], self::FILTER_PRIORITY );
		wp_get_attachment_image( $this->attachment_id, $this->size, $this->icon, $this->attr );
		remove_filter( 'wp_get_attachment_image_attributes', [
			$this,
			'capture_attributes'
		], self::FILTER_PRIORITY );

		$attr = ! empty( $this->attributes ) ? $this->attributes : [];

		if ( ! empty( $attr ) ) {
			$webp = self::convert_to_webp( $attr['src'] );

			if ( ! empty( $webp ) ) {
				$attr['webp'] = $webp;
			}

		}

		return $attr;
	}

	public function capture_attributes( $attributes ) {
		$this->attributes = $attributes;

		return $attributes;
	}

	public static function convert_to_webp( $src ) {

		$out = false;

		if ( ! empty( $src ) ) :
			$upload_dir = wp_get_upload_dir();

			$dir_full = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $src );
			$path     = pathinfo( $dir_full );
			$dir      = $path['dirname']; // дирректория файла
			$web_p    = $dir . '/' . $path['filename'] . '.webp';
			if ( file_exists( $web_p ) ) {
				$out = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $web_p );
			} else {
				$ext = wp_check_filetype( $dir_full );
				if ( $ext['type'] == 'image/jpeg' ) {
					$image = imagecreatefromjpeg( $dir_full );
				} elseif ( $ext['type'] == 'image/png' ) {
					$image = imagecreatefrompng( $dir_full );
					imagepalettetotruecolor( $image ); // восстанавливает цвета
					imagealphablending( $image, false ); // выключает режим сопряжения цветов
					imagesavealpha( $image, true ); // сохраняет прозрачность
				}

				if ( empty( $image ) || empty( $web_p ) ) {
					wpp_console( $src );
					wpp_console( $dir_full );
					wpp_console( $path );
					wpp_console( $web_p );
				}
				imagewebp( $image, $web_p, 100 ); // сохраняет файл в webp
				$out = ! empty( $image ) ? str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $web_p ) : false;
			}
		endif;

		return $out;
	}

	public static function get_img( $attachment_id, $size = 'thumbnail', $icon = false, $attr = '' ) {
		$obj = new static( (int ) $attachment_id, $size, $icon, $attr );

		return $obj->get_attributes();
	}
}