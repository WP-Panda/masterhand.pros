<?php
/**
 * @package masterhand.pros
 * @author  WP_Panda
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

extract( $args );
?>

<li class="company-item 1 project-item">
    <div class="project-content fre-freelancer-wrap">
		<?php if ( ! empty( $company->email ) ) { ?>
            <div class="fre-input-field" style="float:right;">
                <div class="checkline">
                    <input class="get-quote-company" name="company_checked" type="checkbox"
                           data-id="<?php echo $company->id ?>" data-name="<?php echo $company->title ?>">
                    <label for="company_checked"></label>
                </div>
            </div>
		<?php } ?>
        <a class="project-name"><?php echo $company->title; ?></a>
        <div class="reviews-rating-summary">
            <div class="review-rating-result" style="width: <?php echo $company->rating * 100 / 5; ?>%"></div>
        </div>
        <span class="company-item_rating"><?php echo $company->rating; ?></span>
        <div class="fre-location"><?php echo wpp_convert_int_too_location( $company->country, $company->state, $company->city ); ?></div>
        <div class="project-list-desc"><?php echo $company->address; ?></div>
        <div class="project-list-skill">
            <span class="fre-label"><?php echo get_term_by( 'id', $company->cat, 'project_category', ARRAY_A )['name']; ?></span>
        </div>

        <div class="project-list-info project-list-adres">
            <span class="company-item_phone"><?php echo $company->phone; ?></span>
            <span class="company-item_site">
                <a href="<?php echo $company->site; ?>" rel="nofollow" target="_blank"><?php echo _( 'Website' ); ?></a>
            </span>
			<?php if ( ! empty( $company->email ) ) { ?>
                <span class="company-item_btn" data-id="<?php echo $company->id ?>"
                      data-name="<?php echo $company->title ?>"><input class="btn-get-quote" type="button"
                                                                       value="<?php _e( 'Get a Quote', 'wpp' ); ?>"></span>
			<?php } else { ?>
                <span class="company-item_btn">
                    <a href="/login" class="btn-get-quote-to-login"><?php _e( 'Get a Quote' ); ?></a>
                </span>
			<?php } ?>
        </div>
    </div>
</li>
