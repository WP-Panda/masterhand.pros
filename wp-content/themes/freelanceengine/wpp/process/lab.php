<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class WPP_Variation {

	/**
	 * Extra attribute types
	 *
	 * @var array
	 */
	public static $types = [];

	public static function init() {

		self::$types = [
			'image' => esc_html__( 'Image', 'wcvs' ),
			'radio' => esc_html__( 'Radio', 'wcvs' ),
		];

		add_filter( 'wpp_fr_term_meta_boxes_args', [
			__CLASS__,
			'wpp_term_metaboxes'
		] );
		add_filter( 'product_attributes_type_selector', [
			__CLASS__,
			'add_attribute_types'
		] );

		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [
			__CLASS__,
			'wpp_dropdown_variation_attribute_options'
		], 10, 2 );
	}

	public static function wpp_term_metaboxes( $meta_boxes ) {

		$attribute_taxonomies = wpp_get_taxonomies();
		$taxonomy_terms       = [];

		if ( $attribute_taxonomies ) :
			foreach ( $attribute_taxonomies as $tax ) :
				$taxonomy_terms[] = 'pa_' . $tax->attribute_name;
			endforeach;
		endif;

		$meta_boxes[] = [
			'title'      => 'Атрибуты',
			'taxonomies' => $taxonomy_terms,
			'fields'     => [
				[

					'name'             => 'Изображение',
					'id'               => 'attr_img',
					'type'             => 'image_advanced',
					'force_delete'     => false,
					'max_file_uploads' => 1,
					'admin_columns'    => [
						'position' => 'after cb',
						'title'    => 'Genre',
						// Custom title
					],

				],
			]
		];

		return $meta_boxes;

	}


	/**
	 * Get attribute's properties
	 *
	 * @param string $taxonomy
	 *
	 * @return object
	 */
	public static function get_tax_attribute( $taxonomy ) {
		global $wpdb;

		$attr = substr( $taxonomy, 3 );
		$attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "attribute_taxonomies WHERE attribute_name = '$attr'" );

		return $attr;
	}

	/**
	 * Add extra attribute types
	 * Add color, image and label type
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public static function add_attribute_types( $types ) {

		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/screen.php' );
		}
		$screen = get_current_screen();

		if ( ! empty( $screen ) ) {
			$types = array_merge( $types, self::$types );
		}

		return $types;
	}

	/**
	 * Замена лэйбла
	 *
	 * @param       $html
	 * @param array $args
	 *
	 * @return mixed|void
	 */
	public static function wpp_dropdown_variation_attribute_options( $html_parent, $args = [] ) {
		$html = 'bbbbbbbbbbbbb';
		$args = wp_parse_args( apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ), [
			'options'          => false,
			'attribute'        => false,
			'variant'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'show_option_none' => __( 'Choose an option', 'woocommerce' ),
		] );

		$this_types = [
			'image' => esc_html__( 'Image', 'wcvs' ),
			'radio' => esc_html__( 'Radio', 'wcvs' ),
		];
		$attr       = self::get_tax_attribute( $args['attribute'] );

		if ( ! array_key_exists( $attr->attribute_type, $this_types ) ) {
			return $html_parent;
		}

		// Get selected value.
		if ( false === $args['selected'] && $args['attribute'] && $args['variant'] instanceof WPP_Validation ) {
			$selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wpp_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['variant']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
		}

		$options               = $args['options'];
		$variant               = $args['variant'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $variant ) && ! empty( $attribute ) ) {
			$attributes = $variant->get_variation();
			$options    = $attributes[ $attribute ];
		}


		$radio['image'] = <<<HTML
        <label for="%1\$s-%6\$s"><input type="radio" id="%1\$s-%6\$s" class="wpp-variation %3\$s" name="%4\$s" data-attribute_name="attribute_%4\$s" data-show_option_none="%5\$s" value="%6\$s" %7\$s>%8\$s</label>	
HTML;
		$radio_html     = apply_filters( 'wpp_rf_variable_input_html', $radio );

		if ( ! empty( $options ) ) {
			if ( $variant && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wpp_get_terms( $variant->get_id(), $attribute, [
					'fields' => 'all',
				] );

				$n = 1;
				foreach ( $terms as $term ) {
					$img = get_term_meta( $term->term_id, 'attr_img', true );
					if ( ! empty( $img ) ) {
						$link = wp_get_attachment_url( $img );
					} else {
						$link = '';
					}

					$active = sanitize_title( $args['selected'] ) === $term->slug ? ' active' : '';
					if ( in_array( $term->slug, $options, true ) ) {
						$html .= sprintf( $radio_html[ $attr->attribute_type ], esc_attr( $id ), $n, esc_attr( $class ), esc_attr( sanitize_title( $attribute ) ), $show_option_none ? 'yes' : 'no', esc_attr( $term->slug ), checked( sanitize_title( $args['selected'] ), $term->slug, false ), esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $variant ) ), $link, $active );
						$n ++;
					}
				}
			}
		}

		return $html_parent . $html; // WPCS: XSS ok.
	}


}

WPP_Variation::init();