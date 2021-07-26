<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="cats-list" class="cats-list">
	<?php
	$terms = get_terms( [ 'taxonomy' => 'category', 'hide_empty' => 0, 'parent' => 1 ] );
	if ( $terms && ! is_wp_error( $terms ) ) : ?>
        <div class="row">
			<?php foreach ( $terms as $term ) : ?>
                <div class="col-sm-3 col-xs-6">
                    <div class="profs-cat-list_t text-center">
                        <a href="<?php echo get_term_link( $term ); ?>" title="">
							<?php echo $term->name; ?>
                        </a>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>
</div>