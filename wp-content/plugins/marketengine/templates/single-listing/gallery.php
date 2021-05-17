<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$galleries = $listing->get_galleries();

?>

<?php do_action('marketengine_before_single_listing_gallery'); ?>

<?php if(!empty($galleries) && !is_null($galleries[0])) : ?>
<div class="me-images">
	<div class="me-image-large">

		<a class="me-large-fancybox">
			<img src="<?php echo wp_get_attachment_image_url( $galleries[0], 'large' ); ?>" alt="<?php the_title(); ?>">
		</a>

	</div>

	<div class="me-image-thumbs">
		<div class="me-thumbs-slider">
			<ul class="me-list-thumbs">
			<?php foreach ($galleries as $key => $value) : ?>
				<li>
					<a href="<?php echo wp_get_attachment_image_url( $value, 'full' ); ?>" medium-img="<?php echo wp_get_attachment_image_url( $value, 'large' ); ?>" rel="gallery" class="me-fancybox">
						<img src="<?php echo wp_get_attachment_image_url( $value, 'thumbnail' ); ?>" alt="<?php the_title('', '-'. $key); ?>">
					</a>
				</li>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<?php endif; ?>

<?php do_action('marketengine_after_single_listing_gallery'); ?>