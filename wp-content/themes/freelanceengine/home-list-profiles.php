<?php
/**
 * List profiles
 */
$query_args = [
	'post_type'      => PROFILE,
	'post_status'    => 'publish',
	'posts_per_page' => 24,
	'meta_key'       => 'rating_score',
	'meta_query'     => [
		[
			'key'     => 'user_available',
			'value'   => 'on',
			'compare' => '='
		]
	],
	'orderby'        => [
		'meta_value_num' => 'DESC',
		'post_date'      => 'DESC',
	],
];
$loop       = new WP_Query( $query_args );
$num        = $loop->post_count;
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get( PROFILE );
?>
<div class="owl-carousel">
	<?php $i = 0;
	if ( $loop->have_posts() ) {
	$postdata = [];
	foreach ( $loop->posts as $key => $value ) {
	$post                       = $value;
	$convert                    = $post_object->convert( $post );
	$postdata[]                 = $convert;
	$hou_rate                   = (int) $convert->hour_rate; // from 1.8.5
	$current_profile_categories = $convert->tax_input['profile_category'] ?? '';
	$user_status                = get_user_pro_status( $convert->post_author );
	$visualFlag                 = getValueByProperty( $user_status, 'visual_flag' );
	if ( $visualFlag ) {
		$visualFlagNumber = get_user_meta( $convert->post_author, 'visual_flag', true );
	}
	if ( $i == 0 ) { ?>
    <div class="fre-profiles-list-item">
		<?php } ?>
        <div class="fre-freelancer-wrap">
            <a class="free-avatar" href="<?php echo get_author_posts_url( $convert->post_author ); ?>">
				<?php echo get_avatar( $convert->post_author ); ?>
            </a>
            <div class="row">
                <div class="col-sm-7 col-md-7 col-lg-8">
                    <a class="free-name" href="<?php echo get_author_posts_url( $convert->post_author ); ?>">
						<?php the_author_meta( 'display_name', $convert->post_author ); ?>
                    </a>
                    <span class="status">
                                <?php if ( ! empty( $convert->str_pro_account ) ) {
	                                _e( 'PRO', ET_DOMAIN );
                                } ?>
                            </span>
					<?php if ( $visualFlag ) {
						switch ( $visualFlagNumber ) {
							case 1:
								echo '<span class="status">' . translate( 'Master', ET_DOMAIN ) . '</span>';
								break;
							case 2:
								echo '<span class="status">' . translate( 'Creator', ET_DOMAIN ) . '</span>';
								break;
							case 3:
								echo '<span class="status">' . translate( 'Expert', ET_DOMAIN ) . '</span>';
								break;
						}
					} ?>
                    <div class="free-hourly-rate">
						<?php if ( $hou_rate > 0 ) { ?>
							<?php printf( __( '%s/hr', ET_DOMAIN ), "<span>" . fre_price_format( $convert->hour_rate ) . "</span>" ); ?>
						<?php } ?>
                    </div>
                </div>
                <div class="col-sm-5 col-md-5 col-lg-4">
                    <div class="free-rating-new">+<?php echo wpp_get_user_rating( $convert->post_author ); ?></div>
                    <div class="free-rating"><?php HTML_review_rating_user( $convert->post_author ); ?></div>
                </div>
            </div>
            <div class="free-category">
				<?php if ( $current_profile_categories ) {
					echo baskserg_profile_categories( $current_profile_categories );
				} ?>
            </div>

            <div class="free-experience">
                <span><?php echo $convert->experience; ?></span>
                <span><?php echo $convert->project_worked; ?></span>
            </div>
        </div>

		<?php if ( ( $i != 0 ) && ( $i % 2 ) ) { ?>
    </div>
<?php } ?>
	<?php if ( ( $i != 0 ) && ( $i % 2 ) && ( $i + 1 != $num ) ){ ?>
    <div class="fre-profiles-list-item">
		<?php } ?>

		<?php $i ++;
		if ( $i == $num + 1 ) {
			echo '</div>';
		}
		}
		} ?>
		<?php wp_reset_query(); ?>
    </div>
