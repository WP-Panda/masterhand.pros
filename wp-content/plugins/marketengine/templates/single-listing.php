<?php
/**
 *     The Template for displaying details of a listing.
 *     This template can be overridden by copying it to yourtheme/marketengine/single-listing.php.
 *
 * @author         EngineThemes
 * @package     MarketEngine/Templates
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();?>

<?php do_action('marketengine_before_main_content');?>

<?php while (have_posts()): the_post();?>

		<?php $listing = marketengine_get_listing();?>

		<?php do_action('marketengine_before_single_listing_content');?>

		<?php marketengine_get_template('content-single-listing', array('listing' => $listing));?>

		<?php do_action('marketengine_after_single_listing_content');?>

	<?php endwhile; // end of the loop. ?>

<?php do_action('marketengine_after_main_content');?>

<?php get_footer();