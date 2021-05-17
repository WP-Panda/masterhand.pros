<?php
/**
 * The template for displaying listing information of the conversation page.
 *
 * This template can be override by copying it to yourtheme/marketengine/inquiry/listing-info.php.
 *
 * @author EngineThemes
 * @package MarketEngine/Templates
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$inquiry_count = $listing->get_inquiry_count();

?>

<div class="me-orderlisting-info">
<?php if($listing) : ?>

	<?php $author = $listing->get_author() ==  get_current_user_id(); ?>

	<?php marketengine_get_template('purchases/order-listing-image', array('listing' => $listing)); ?>

	<div class="me-listing-info <?php echo (!$listing->is_available() ) ? 'me-listing-info-archive' : ''; ?>">
		<?php if($author || $listing->is_available()) : ?>
			<a class="" href="<?php echo $listing->get_permalink(); ?>">
				<?php echo esc_html( $listing->get_title() ); ?>
			</a>
		<?php else : ?>
			<span>
				<?php echo esc_html( $listing->get_title() ); ?>
			</span>
		<?php endif; ?>

		<div class="me-rating-contact">
			<span class="me-count-contact">
				<?php printf(_n("%d Contact", "%d Contacts", $inquiry_count,"enginethemes"),$inquiry_count ); ?>
			</span>
		</div>

		<?php if( $listing->is_available()) : ?>
			<?php echo wp_kses( $listing->get_short_description(), '<p><a><ul><ol><li><h6><span><b><em><strong><br>' ); ?>
		<?php endif; ?>

	</div>

<?php endif; ?>
	<?php marketengine_get_template('purchases/listing-archived', array('listing' => $listing) ); ?>
</div>
