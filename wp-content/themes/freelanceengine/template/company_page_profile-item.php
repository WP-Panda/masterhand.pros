<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get( COMPANY );
$current     = $post_object->current_post;
?>

<li class="company-item 1 project-item">
    <div class="project-content fre-freelancer-wrap">
		<? if ( $current->button ) { ?>
            <div class="fre-input-field" style="float:right;">
                <div class="checkline">
                    <input class="get-quote-company" name="company_checked" type="checkbox"
                           data-id="<?= $current->ID ?>" data-name="<?= $current->post_title ?>">
                    <label for="company_checked"></label>
                </div>
            </div>
		<? } ?>
        <a class="project-name"><?php echo $current->post_title; ?></a>
        <div class="reviews-rating-summary">
            <div class="review-rating-result" style="width: <?= $current->percent; ?>%"></div>
        </div>
        <span class="company-item_rating"><?php echo $current->raiting; ?></span>
        <div class="fre-location"><?php echo $current->str_location; ?></div>
        <div class="project-list-desc"><?php echo $current->adress; ?></div>
        <div class="project-list-skill">
            <span class="fre-label"><?php echo $current->str_cat; ?></span>
        </div>

        <div class="project-list-info project-list-adres">
            <span class="company-item_phone"><?php echo $current->phone; ?></span>
            <span class="company-item_site">
                <a href="<?php echo $current->site; ?>" rel="nofollow" target="_blank"><?php echo _( 'Website' ); ?></a>
            </span>
			<? if ( $current->button ) { ?>
                <span class="company-item_btn" data-id="<?= $current->ID ?>"
                      data-name="<?= $current->post_title ?>"><?= $current->button; ?></span>
			<? } else { ?>
                <span class="company-item_btn"><a href="/login"
                                                  class="btn-get-quote-to-login"><? _e( 'Get a Quote' ); ?></a></span>
			<? } ?>
        </div>
    </div>
</li>