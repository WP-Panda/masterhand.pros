<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
$selected = !empty($_GET['orderby']) ? esc_attr( $_GET['orderby'] ) : '';
global $wp_query;
?>
<div class="me-bar-shop">
	<div class="me-title-shop pull-left">
		<?php
			marketengine_the_archive_title( '<h2>', '</h2>' );
			the_archive_description( '<div class="taxonomy-description">', '</div>' );
		?>
		<span><?php printf(_n( 'One item in total', "%d items in totals", $wp_query->found_posts, "enginethemes" ), $wp_query->found_posts) ?></span>
	</div>
	<?php if(empty($_GET['type']) || $_GET['type'] != 'contact') : ?>
	<div class="me-sort-listing pull-right">
		<form method="get">

			<?php do_action('marketengine_before_filter_listing_form'); ?>

			<select class="me-chosen-select" name="orderby" id="listing-orderby">
				<option <?php selected( $selected, 'date') ?> value="date"><?php _e("Sort by Newest", "enginethemes"); ?></option>
				<option <?php selected( $selected, 'price') ?> value="price"><?php _e("Price: Low to High", "enginethemes"); ?></option>
				<option <?php selected( $selected, 'price-desc') ?> value="price-desc"><?php _e("Price: High to Low", "enginethemes"); ?></option>
			</select>
			<input type="hidden" name="paged" value="1" ?>
			<?php  if(!empty($_GET['price-min'])) : ?>
				<input type="hidden" name="price-min" value="<?php echo esc_attr( $_GET['price-min'] );  ?>" ?>
			<?php endif; ?>
			<?php if(!empty($_GET['price-max'])) : ?>
				<input type="hidden" name="price-max" value="<?php echo esc_attr( $_GET['price-max'] );  ?>" ?>
			<?php  endif; ?>
            <?php if (!empty($_GET['keyword'])): ?>
                <input type="hidden" name="keyword" value="<?php echo esc_attr( $_GET['keyword'] ); ?>" ?>
            <?php endif;?>

            <?php do_action('marketengine_after_filter_listing_form'); ?>

		</form>
	</div>
	<?php endif; ?>
</div>