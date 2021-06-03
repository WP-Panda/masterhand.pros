<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;

	extract( $args );

	if ( ! empty( $images ) ) : ?>
        <div class="wpp-post-slider">
			<?php
				foreach ( $images as $image ) :

					printf( '<div class="fre-blog-item_img" style="background:url(%s) center no-repeat;" alt=""><div class="fre-blog-item_cat">%s</div></div>', bfi_thumb( $image, [
						'width'  => 1140,
						'height' => 600,
						'crop'   => true
					] ), $category[ 0 ]->cat_name );
				endforeach;
			?>
        </div>
	<?php endif;