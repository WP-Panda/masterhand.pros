<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

?>

<?php  get_header(); ?>

<?php do_action('marketengine_before_main_content'); ?>

<div id="marketengine-page">
	<div class="me-container marketengine">
		<div class="marketengine-content-wrap">
			<!-- marketengine-content -->
			<div class="marketengine-content">
				<div class="me-row">

					<div id="primary" class="content-area me-col-md-9 marketengine-snap-column">

						<?php do_action('marketengine_before_listing_archive_content'); ?>

						<div class="me-content-shop">

							<?php marketengine_get_template('archive-heading') ?>

							<?php marketengine_get_template('listing-list'); ?>

						</div>

						<?php do_action('marketengine_after_listing_archive_content'); ?>

					</div>

					<?php do_action('marketengine_sidebar'); ?>

				</div>
			</div>
		</div>
		<!--// marketengine-content -->
	</div>
</div>

<?php do_action('marketengine_after_main_content'); ?>

<?php get_footer(); ?>