<?php
	/**
	 * @package masterhand.pros
	 * @author  WP_Panda
	 * @version 1.0.0
	 */

	defined( 'ABSPATH' ) || exit;
extract($args);
?>
<div class="fre-blog-item fre-blog-item-<?php echo $type; ?>">
    <div class="overlay-block">
        <div class="fre-blog-item_t center">
            <a href="<?php echo $url; ?>" title="<?php echo $ankor; ?>">
	            <?php echo $ankor; ?>
            </a>
        </div>
        <div class="fre-blog-item_d center">
            <p><?php echo $text; ?></p>
        </div>
        <br>
		<?php wpp_get_template_part( 'wpp/templates/universal/more-btn', [ 'url' => $url ] ); ?>
    </div>
</div>