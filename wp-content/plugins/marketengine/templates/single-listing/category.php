<?php 
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
global $post;
$terms = wp_get_object_terms( $post->ID, 'listing_category', array('orderby' => 'parent') );
?>

<?php do_action('marketengine_before_single_listing_categories'); ?>

<div itemprop="category" class="me-categories">
	<?php foreach ($terms as $key => $term) : ?>
		<?php if($term->parent) echo '<span>&nbsp;&gt;&nbsp;</span>'; ?>
		<a href="<?php echo get_term_link( $term, 'listing_category' ); ?>"><?php echo $term->name; ?></a>
	<?php endforeach; ?>

	<?php marketengine_get_template('single-listing/tags');?>
</div>

<?php do_action('marketengine_after_single_listing_categories'); ?>